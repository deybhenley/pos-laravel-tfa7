<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['username' => 'alex.admin', 'full_name' => 'Alex Mendoza'],
            ['username' => 'jamie.manager', 'full_name' => 'Jamie Ramos'],
            ['username' => 'sam.cashier', 'full_name' => 'Sam Torres'],
            ['username' => 'louise.inventory', 'full_name' => 'Louise Tan'],
            ['username' => 'chris.support', 'full_name' => 'Chris Lim'],
        ];

        foreach ($records as $record) {
            User::updateOrCreate(['username' => $record['username']], $record);
        }
    }
}
