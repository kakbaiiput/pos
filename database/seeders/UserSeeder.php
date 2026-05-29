<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $stores = Store::where('status', 'active')->get();

        $superAdmin = User::where('email', 'superadmin@test.com')->first();
        if (! $superAdmin) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@test.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]);
        }

        $admins = [
            'Budi Santoso', 'Siti Rahayu', 'Dewi Lestari',
            'Agus Prasetyo', 'Rina Susanti', 'Hendra Wijaya',
            'Tan Mei Ling', 'Fajar Nugroho', 'Putri Ayu',
            'Rizky Ramadhan', 'Linda Kusuma', 'Eko Saputra',
            'Yoga Aditya', 'Nur Halimah', 'Dimas Kurniawan',
        ];

        $kasirs = [
            'Andi Pratama', 'Maya Sari', 'Doni Firmansyah', 'Nur Aini',
            'Reza Mahendra', 'Fitri Handayani', 'Bayu Setiawan',
            'Ayu Lestari', 'Firman Hidayat', 'Ratna Dewi', 'Galih Permana',
            'Indah Wulandari', 'Arif Budiman', 'Sari Melati', 'Tari Anggraini',
            'Roni Saputra', 'Wati Susilawati', 'Hadi Purnomo', 'Lina Marlina',
            'Joko Widodo', 'Nita Permata', 'Rudi Hartono', 'Dewi Sartika',
            'Irfan Hakim', 'Mega Putri', 'Eko Prasetyo', 'Rini Astuti',
            'Dian Saputra', 'Lina Sari', 'Bambang Irawan', 'Sinta Dewi',
            'Agus Setiawan', 'Putri Rahayu', 'Fajar Hidayat', 'Nur Azizah',
        ];

        $adminIdx = 0;
        $kasirIdx = 0;

        foreach ($stores as $store) {
            for ($i = 0; $i < 3; $i++) {
                $name = $admins[$adminIdx++];
                User::create([
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)).'@test.com',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'store_id' => $store->id,
                ]);
            }

            for ($i = 0; $i < 7; $i++) {
                $name = $kasirs[$kasirIdx++];
                User::create([
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)).'@test.com',
                    'password' => Hash::make('password'),
                    'role' => 'kasir',
                    'store_id' => $store->id,
                ]);
            }
        }
    }
}
