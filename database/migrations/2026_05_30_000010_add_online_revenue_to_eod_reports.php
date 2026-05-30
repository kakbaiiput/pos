<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('eod_reports', function (Blueprint $table) {
            $table->decimal('online_gofood', 15, 2)->default(0)->after('sales_credit');
            $table->decimal('online_grabfood', 15, 2)->default(0)->after('online_gofood');
            $table->decimal('online_shopeefood', 15, 2)->default(0)->after('online_grabfood');
        });
    }
    public function down(): void {
        Schema::table('eod_reports', function (Blueprint $table) {
            $table->dropColumn(['online_gofood', 'online_grabfood', 'online_shopeefood']);
        });
    }
};
