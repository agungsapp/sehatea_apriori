<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [

            [
                'nama' => 'admin',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ],
            [
                'nama' => 'agung',
                'username' => 'agung',
                'email' => 'agung@gmail.com',
                'role' => 'owner',
                'password' => Hash::make('owner123'),
            ],
            [
                'nama' => 'putri intan utami',
                'username' => 'intan',
                'email' => 'putriintanutami14@gmail.com',
                'role' => 'owner',
                'password' => Hash::make('putri123'),
            ],
            [
                'nama' => 'mas ajis santoso',
                'username' => 'ajis',
                'email' => 'ajis@gmail.com',
                'role' => 'investor',
                'password' => Hash::make('ajis123'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
