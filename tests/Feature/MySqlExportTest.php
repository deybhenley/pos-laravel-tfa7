<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use App\Services\MySqlExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MySqlExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_contains_current_records_and_escapes_text_as_utf8_hex(): void
    {
        Customer::create(['full_name' => "O'Neil \\ Test", 'email' => 'oneil@example.com', 'phone' => null]);
        $avatar = 'avatars/00000000-0000-0000-0000-000000000000.png';
        User::create(['username' => 'lab.cashier', 'full_name' => 'Lab Cashier', 'avatar' => $avatar]);
        $path = app(MySqlExportService::class)->export(storage_path('framework/testing/tfa7-export-test.sql'));

        try {
            $sql = file_get_contents($path);
            $this->assertStringContainsString('CREATE TABLE `customers`', $sql);
            $this->assertStringContainsString('CREATE TABLE `users`', $sql);
            $this->assertStringContainsString('CONVERT(0x'.bin2hex("O'Neil \\ Test").' USING utf8mb4)', $sql);
            $this->assertStringContainsString('CONVERT(0x'.bin2hex($avatar).' USING utf8mb4)', $sql);
            $this->assertStringContainsString('NULL', $sql);
            $this->assertStringNotContainsString('DROP TABLE', $sql);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
