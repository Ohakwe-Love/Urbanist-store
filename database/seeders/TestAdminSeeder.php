<?php

namespace Database\Seeders;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@urbanist.com'],
            [
                'name' => 'Urbanist Admin',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('12345678'),
                'is_active' => true,
            ]
        );
    }
}
