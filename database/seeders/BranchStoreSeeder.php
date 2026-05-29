<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Store;
use Illuminate\Database\Seeder;

class BranchStoreSeeder extends Seeder
{
    public function run(): void
    {
        // Create branches
        $jakarta = Branch::create(['name' => 'Jakarta Pusat', 'city' => 'Jakarta', 'address' => 'Jl. Sudirman No. 1']);
        $bandung = Branch::create(['name' => 'Bandung', 'city' => 'Bandung', 'address' => 'Jl. Braga No. 15']);
        $surabaya = Branch::create(['name' => 'Surabaya', 'city' => 'Surabaya', 'address' => 'Jl. Basuki Rahmat No. 20']);

        // Create stores
        Store::create(['branch_id' => $jakarta->id, 'name' => 'Toko Jakarta 1', 'code' => 'JKT001', 'status' => 'active']);
        Store::create(['branch_id' => $jakarta->id, 'name' => 'Toko Jakarta 2', 'code' => 'JKT002', 'status' => 'active']);
        Store::create(['branch_id' => $bandung->id, 'name' => 'Toko Bandung 1', 'code' => 'BDG001', 'status' => 'active']);
        Store::create(['branch_id' => $surabaya->id, 'name' => 'Toko Surabaya 1', 'code' => 'SBY001', 'status' => 'active']);
    }
}
