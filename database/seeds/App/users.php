<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        \App\User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('test123')
        ]);
    }
}
