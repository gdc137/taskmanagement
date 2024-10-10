<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'is_admin' => 1,
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('test123test'),
        ]);

        DB::table('users')->insert([
            'is_admin' => 0,
            'name' => 'user1',
            'email' => 'user1@mail.com',
            'password' => Hash::make('user1pass'),
        ]);
    }
}
