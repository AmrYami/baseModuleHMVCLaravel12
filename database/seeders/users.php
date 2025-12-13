<?php

namespace Database\Seeders;

use Users\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => json_encode(['en' => 'admin', 'ar' => 'admin']),
                'user_name' => 'admin',
                'email' => 'admin@admin.com',
                'mobile' => '01557011197',
                'password'=> Hash::make('admin@admin.com'),
                'type' => 'crm admin',
                'code' => uniqid(),
                'status' => 1
            ],
            [
                'name' => json_encode(['en' => 'HR', 'ar' => 'HR']),
                'user_name' => 'HR',
                'email' => 'hr@fakeeh.com',
                'mobile' => '01011426275',
                'password'=> Hash::make('hr@fakeeh.com'),
                'type' => 'hr',
                'code' => uniqid(),
                'status' => 1
            ],
            [
                'name' => json_encode(['en' => 'COO', 'ar' => 'COO']),
                'user_name' => 'COO',
                'email' => 'coo@fakeeh.com',
                'mobile' => '01011426285',
                'password'=> Hash::make('coo@fakeeh.com'),
                'type' => 'coo',
                'code' => uniqid(),
                'status' => 1
            ],
            [
                'name' => json_encode(['en' => 'Marketing', 'ar' => 'Marketing']),
                'user_name' => 'Marketing',
                'email' => 'marketing@fakeeh.com',
                'mobile' => '01011426254',
                'password'=> Hash::make('marketing@fakeeh.com'),
                'type' => 'marketing',
                'code' => uniqid(),
                'status' => 1
            ],
        ];
        User::insert($data);
    }
}
