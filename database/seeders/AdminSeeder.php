<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insertOrIgnore([
            [
                'id' => 1,
                'user_name' => 'Super Admin',
                'email' => 'iamsoftwarepublishing@gmail.com',
                'password' => Hash::make('Hh@2020492020'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
