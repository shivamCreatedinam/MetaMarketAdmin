<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = [
            "name" => "Super Admin",
            "username" => "superadmin",
            "email" => "superadmin@yopmail.com",
            "mobile_no" => "123456780",
            "role" => "superadmin",
            "email_verified_at" => now(),
            "mobile_verified_at" => now(),
            "password" => Hash::make("12345678"),
        ];

        User::create($superAdmin);
    }
}
