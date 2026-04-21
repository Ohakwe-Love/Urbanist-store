<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::query()->where('email', 'lovely@love.com')->delete();

        $user = User::updateOrCreate(
            ['email' => 'customer@urbanist.com'],
            [
                'name' => 'Ada Nwosu',
                'username' => 'adanwosu',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('12345678'),
                'phone' => '+1 (646) 555-0182',
                'address' => '14 Prince Street, Apt 5B',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10012',
                'country' => 'USA',
                'role' => User::ROLE_CUSTOMER,
                'is_active' => true,
            ]
        );

        return $user;
    }
}
