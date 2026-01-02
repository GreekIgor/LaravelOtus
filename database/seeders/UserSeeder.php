<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            'name' => 'user',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
            'role' => 'admin',
        ]);
        DB::table('users')->insert([
            'name' => 'user2',
            'email' => 'user2@example.com',
            'password' => bcrypt('user2'),
            'role' => 'moderator',
        ]);
        DB::table('users')->insert([
            'name' => 'user3',
            'email' => 'user3@example.com',
            'password' => bcrypt('user3'),
            'role' => 'viewer',
        ]);
    }
}
