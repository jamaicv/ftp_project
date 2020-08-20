<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;   

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'birth_date' => '1997-11-19',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'is_admin' => true
        ]);
    }
}
