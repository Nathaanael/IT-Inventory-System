<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->insert([
            [
                'id_karyawan' => '50180670',
                'username_ad' => 'nathanael.prasetyo',
                'name' => 'nathan',
                'role' => 'IT Support',
                'password' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_karyawan' => '11223344',
                'username_ad' => 'it.support',
                'name' => 'IT Support',
                'role' => 'Super Admin',
                'password' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
