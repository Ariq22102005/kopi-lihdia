<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin KopiLihdia',
            'email' => 'admin@kopilihdia.test',
            'password' => 'password',   // otomatis di-hash oleh cast 'hashed'
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password',
            'role' => 'customer',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
        ]);

        User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'password',
            'role' => 'customer',
        ]);
    }
}