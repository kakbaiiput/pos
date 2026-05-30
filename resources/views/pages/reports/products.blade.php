<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <main class="flex-1 flex flex-col min-h-screen relative w-full">

        <header class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl sticky top-0 z-30 flex items-center gap-3 w-full px-4 lg:px-8 py-3 lg:py-4 shadow-sm font-manrope antialiased tracking-tight">
            <div class="flex items-center gap-3 lg:gap-8 pl-10 lg:pl-0">
                <span class="material-symbols-outlined text-primary font-black text-xl">bar_chart</span>
                <h1 class="text-lg lg:text-xl font-extrabold tracking-tighter text-blue-900 dark:text-blue-100">{{ $title }}</h1>
            </div>
        </header>

        <div class="p-4 lg:p-8 flex-1 overflow-y-auto no-scrollbar space-y-6">
            <x-report-header title="Laporan Produk" module="Reports" submodule="Products" description="Analisis produk terlaris berdasarkan jumlah terjual, pendapatan, dan profit." />

            <!-- Filter -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
                <form method="GET" action="/reports/products" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    @if(auth()->user()->isSuperAdmin())
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Toko</label>
                        <select name="store_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="">Semua Toko</option>
                            @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Urutkan Berdasarkan</label>
                        <select name="sort_by" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm font-semibold focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="qty"     {{ $sortBy === 'qty'     ? 'selected' : '' }}>Terbanyak Terjual (Qty)</option>
                            <option value="revenue" {{ $sortBy === 'revenue' ? 'selected' : '' }}>Pendapatan Tertinggi</option>
                            <option value="profit"  {{ $sortBy === 'profit'  ? 'selected' : '' }}>Profit Tertinggi</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                        Tampilkan
                    </button>
                </form>
            </div>

            <!-- Summary Cards -->
            @php
                $grandQty     = $topProducts->sum('total_qty');
                $grandRevenue = $topProducts->sum('total_revenue');
                $grandProfit  = $topProducts->sum('total_profit');
            @endphp
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['Produk Tercatat', $topProducts->count().' produk', 'inventory_2', 'blue'],
                    ['Total Terjual', number_format($grandQty, 0, ',', '.').' pcs', 'shopping_cart', 'green'],
                    ['Total Pendapatan', 'Rp'.number_format($grandRevenue, 0, ',', '.'), 'payments', 'indigo'],
                    ['Total Profit', 'Rp'.number_format($grandProfit, 0, ',', '.'), 'trending_up', $grandProfit >= 0 ? 'emerald' : 'red'],
                ] as [$label, $value, $icon, $color])
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-100 dark:border-slate-700 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-{{ $color }}-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-{{ $color }}-500 text-lg">{{ $icon }}</span>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</span>
                    </div>
                    <p class="text-xl font-black text-on-surface">{{ $value }}</p>
                </div>
                @endforeach
            </div>

            <div class="grid grid-cols-12 gap-6">
                <!-- Top Products Table -->
                <div class="col-span-12 lg:col-span-8">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <h3 class="font-black text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">emoji_events</span>
                                Top 20 Produk
                                <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full">
                                    {{ $startDate }} — {{ $endDate }}
                                </span>
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left bg-slate-50/70 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                        <th class="py-4 px-5">#</th>
                                        <th class="py-4 px-5">Produk</th>
                                        <th class="py-4 px-5 text-right">Qty Terjual</th>
                                        <th class="py-4 px-5 text-right">Pendapatan</th>
                                        <th class="py-4 px-5 text-right">Profit</th>
                                        <th class="py-4 px-5 text-right">Pesanan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                                    @forelse($topProducts as $i => $p)
                                    @php
                                        $maxQty = $topProducts->max('total_qty') ?: 1;
                                        $barPct = round(($p->total_qty / $maxQty) * 100);
                                        $medal  = match($i) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => '#'.($i+1) };
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-all group">
                                        <td class="py-4 px-5">
                                            <span class="text-sm font-black {{ $i < 3 ? 'text-lg' : 'text-slate-400' }}">{{ $medal }}</span>
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="flex items-center gap-3">
                                                @if($p->image)
                                                <img src="{{ $p->image }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                                                @else
                                                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                    <span class="material-symbols-outlined text-primary text-sm">inventory_2</span>
                                                </div>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-black text-on-surface">{{ $p->name }}</p>
                                                    <p class="text-[10px] text-slate-400 font-bold">{{ $p->category_name ?? 'Tanpa Kategori' }}</p>
                                                    <!-- Progress bar -->
                                                    <div class="mt-1 h-1 w-24 bg-slate-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-primary rounded-full" style="width: {{ $barPct }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-5 text-right">
                                            <span class="font-black text-on-surface">{{ number_format($p->total_qty, 0, ',', '.') }}</span>
                                            <span class="text-[10px] text-slate-400 ml-1">pcs</span>
                                        </td>
                                        <td class="py-4 px-5 text-right font-black text-indigo-600 text-sm">
                                            Rp{{ number_format($p->total_revenue, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-5 text-right text-sm font-black {{ $p->total_profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                            Rp{{ number_format($p->total_profit, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-5 text-right">
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full text-xs font-black">{{ $p->total_orders }}x</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="py-16 text-center">
                                            <span class="material-symbols-outlined text-4xl text-slate-200 block mb-2">inbox</span>
                                            <p class="text-sm font-bold text-slate-400">Belum ada data penjualan pada periode ini.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="col-span-12 lg:col-span-4">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700">
                            <h3 class="font-black text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">category</span>
                                Per Kategori
                            </h3>
                        </div>
                        <div class="p-4 space-y-3">
                            @php $maxCatRev = $categoryBreakdown->max('total_revenue') ?: 1; @endphp
                            @forelse($categoryBreakdown as $cat)
                            @php $pct = round(($cat->total_revenue / $maxCatRev) * 100); @endphp
                            <div class="p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-xs font-black text-slate-700">{{ $cat->category }}</span>
                                    <span class="text-xs font-black text-primary">{{ number_format($cat->total_qty, 0, ',', '.') }} pcs</span>
                                </div>
                                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden mb-1">
                                    <div class="h-full bg-gradient-to-r from-primary to-primary/60 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 font-bold text-right">Rp{{ number_format($cat->total_revenue, 0, ',', '.') }}</p>
                            </div>
                            @empty
                            <p class="text-sm text-slate-400 text-center py-8 font-bold">Tidak ada data</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout>
