<?php

namespace App\Http\Controllers;

use App\Models\Closing;
use App\Models\History;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClerekController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $nik  = $request->nik;
        $date = $request->date ?? now()->format('Y-m-d');
        $targetUser    = null;
        $pendingClerek = null;
        $history       = null;

        if ($nik) {
            $targetUser = User::where('nik', $nik)
                ->where('store_id', $user->store_id)
                ->first();

            if ($targetUser) {
                // All pending closings for this date (could be multiple shifts)
                $pendingClerek = Closing::where('user_id', $targetUser->id)
                    ->where('status', 'pending')
                    ->whereDate('closing_date', $date)
                    ->latest()
                    ->first();

                $history = Closing::where('user_id', $targetUser->id)
                    ->whereDate('closing_date', $date)
                    ->where('status', 'approved')
                    ->latest()
                    ->get();

                if ($history->isEmpty()) {
                    $history = Closing::where('user_id', $targetUser->id)
                        ->where('status', 'approved')
                        ->latest()
                        ->take(10)
                        ->get();
                }
            }
        }

        return view('pages.clerek-data', [
            'title'        => 'Data Clerek',
            'nik'          => $nik,
            'date'         => $date,
            'targetUser'   => $targetUser,
            'pendingClerek'=> $pendingClerek,
            'history'      => $history,
        ]);
    }

    public function summary(Request $request)
    {
        $user  = Auth::user();
        $shift = $request->get('shift', 'pagi');
        $summary = $this->getShiftSummary($user, $shift);

        return response()->json($summary);
    }

    /**
     * Calculate sales summary for a user's specific shift.
     * Transactions are counted from after the last approved closing today,
     * so shift-2 only sees transactions that happened after shift-1 closed.
     */
    private function getShiftSummary($user, string $shift = 'pagi')
    {
        $today = now()->format('Y-m-d');

        // Find the most recent approved closing today for this user
        // (could be a previous shift). Use its approved_at as the start of the current window.
        $lastApproved = Closing::where('user_id', $user->id)
            ->whereDate('closing_date', $today)
            ->where('status', 'approved')
            ->latest('approved_at')
            ->first();

        $since = $lastApproved ? $lastApproved->approved_at : now()->startOfDay();

        $sales = History::where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'voided')
            ->get();

        $cashSales = $qrisSales = $debitSales = $creditSales = 0;

        foreach ($sales as $trx) {
            if ($trx->payment_method === 'split') {
                foreach ($trx->payments as $pmt) {
                    $method = $pmt->method;
                    ${$method.'Sales'} += $pmt->amount;
                }
            } else {
                $method = $trx->payment_method;
                ${$method.'Sales'} += $trx->total_amount;
            }
        }

        $totalSales = $cashSales + $qrisSales + $debitSales + $creditSales;

        // Opening balance only applies to the first shift of the day
        $openingBalance = $lastApproved
            ? 0
            : (int) StoreSetting::getVal('opening_balance', $user->store_id, 0);

        $expectedCash = $openingBalance + $cashSales;

        // Check pending for THIS specific shift
        $pendingThisShift = Closing::where('user_id', $user->id)
            ->whereDate('closing_date', $today)
            ->where('shift', $shift)
            ->where('status', 'pending')
            ->first();

        // Check approved for THIS specific shift
        $approvedThisShift = Closing::where('user_id', $user->id)
            ->whereDate('closing_date', $today)
            ->where('shift', $shift)
            ->where('status', 'approved')
            ->first();

        return [
            'cashSales'      => (int) $cashSales,
            'qrisSales'      => (int) $qrisSales,
            'debitSales'     => (int) $debitSales,
            'creditSales'    => (int) $creditSales,
            'totalSales'     => (int) $totalSales,
            'openingBalance' => $openingBalance,
            'expectedCash'   => $expectedCash,
            'shift'          => $shift,
            'isClosed'       => (bool) $approvedThisShift,
            'hasPending'     => (bool) $pendingThisShift,
            'existingData'   => $approvedThisShift ?? $pendingThisShift,
        ];
    }

    public function store(Request $request)
    {
        $user  = Auth::user();
        $today = now()->format('Y-m-d');
        $shift = $request->shift ?? 'pagi';

        $summary = $this->getShiftSummary($user, $shift);

        if ($summary['isClosed']) {
            return response()->json([
                'success' => false,
                'message' => "Shift {$shift} Anda hari ini sudah di-clerek.",
            ], 400);
        }

        if ($summary['hasPending']) {
            return response()->json([
                'success' => false,
                'message' => "Shift {$shift} Anda sedang menunggu verifikasi admin.",
            ], 400);
        }

        $isStaff = ! $user->hasMinRole('admin');

        $closing = Closing::create([
            'user_id'         => $user->id,
            'store_id'        => $user->store_id,
            'closing_date'    => $today,
            'opening_balance' => $summary['openingBalance'],
            'total_sales'     => $summary['totalSales'],
            'cash_sales'      => $summary['cashSales'],
            'qris_sales'      => $summary['qrisSales'],
            'debit_sales'     => $summary['debitSales'],
            'credit_sales'    => $summary['creditSales'],
            'expected_cash'   => $summary['expectedCash'],
            'actual_cash'     => 0,
            'difference'      => -$summary['expectedCash'],
            'shift'           => $shift,
            'status'          => 'pending',
            'notes'           => $request->notes,
        ]);

        if ($isStaff) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'success'      => true,
            'message'      => $isStaff
                ? 'Shift berhasil ditutup. Silahkan serahkan uang ke Admin.'
                : 'Shift berhasil ditutup.',
            'shouldLogout' => $isStaff,
            'data'         => $closing,
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'closing_id'  => 'required|exists:closings,id',
            'actual_cash' => 'required|numeric|min:0',
        ]);

        $closing = Closing::findOrFail($request->closing_id);

        if ($closing->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Clerek ini sudah diproses.'], 400);
        }

        // Prevent duplicate: same user + same date + same shift
        $alreadyApproved = Closing::where('user_id', $closing->user_id)
            ->whereDate('closing_date', $closing->closing_date)
            ->where('shift', $closing->shift)
            ->where('status', 'approved')
            ->exists();

        if ($alreadyApproved) {
            return response()->json([
                'success' => false,
                'message' => "Shift {$closing->shift} kasir ini sudah diverifikasi hari ini.",
            ], 400);
        }

        $closing->update([
            'actual_cash' => $request->actual_cash,
            'difference'  => $request->actual_cash - $closing->expected_cash,
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes'       => $request->notes ?? $closing->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Clerek berhasil diverifikasi dan disimpan.',
        ]);
    }

    public function print(Closing $closing)
    {
        return view('print.clerek', [
            'closing' => $closing,
            'store'   => $closing->store,
        ]);
    }
}
