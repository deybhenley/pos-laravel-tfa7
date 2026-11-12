<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['full_name' => 'Ana Reyes', 'email' => 'ana.reyes@example.com', 'phone' => '0900 000 1001'],
            ['full_name' => 'Miguel Santos', 'email' => 'miguel.santos@example.com', 'phone' => '0900 000 1002'],
            ['full_name' => 'Bea Cruz', 'email' => 'bea.cruz@example.com', 'phone' => '0900 000 1003'],
            ['full_name' => 'Carlo Garcia', 'email' => 'carlo.garcia@example.com', 'phone' => '0900 000 1004'],
            ['full_name' => 'Dani Flores', 'email' => 'dani.flores@example.com', 'phone' => '0900 000 1005'],
        ];

        foreach ($records as $record) {
            Customer::updateOrCreate(['email' => $record['email']], $record);
        }
    }
}
