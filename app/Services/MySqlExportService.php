<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use RuntimeException;

class MySqlExportService
{
    public function export(?string $destination = null): string
    {
        $sql = "-- TFA7 current records. Import into a NEW, EMPTY MySQL database.\n";
        $sql .= "-- Existing tables are not dropped. Avatar files are copied separately.\nSET NAMES utf8mb4;\n\n";
        $sql .= "CREATE TABLE `customers` (\n `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n `full_name` VARCHAR(255) NOT NULL,\n `email` VARCHAR(255) NOT NULL,\n `phone` VARCHAR(30) NULL,\n PRIMARY KEY (`id`)\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";
        $sql .= "CREATE TABLE `users` (\n `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n `username` VARCHAR(100) NOT NULL,\n `full_name` VARCHAR(255) NOT NULL,\n `avatar` VARCHAR(255) NULL,\n PRIMARY KEY (`id`),\n UNIQUE KEY `users_username_unique` (`username`)\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

        foreach ([
            ['customers', ['id', 'full_name', 'email', 'phone'], Customer::orderBy('id')->get()],
            ['users', ['id', 'username', 'full_name', 'avatar'], User::orderBy('id')->get()],
        ] as [$table, $columns, $records]) {
            if ($records->isEmpty()) {
                continue;
            }

            $values = [];
            foreach ($records as $record) {
                $row = [];
                foreach ($columns as $column) {
                    $value = $record->getAttribute($column);
                    // Hex-encoded UTF-8 literals avoid SQL quoting and backslash ambiguity.
                    $row[] = $column === 'id' ? (string) (int) $value : $this->literal($value);
                }
                $values[] = '('.implode(', ', $row).')';
            }

            $sql .= 'INSERT INTO `'.$table.'` (`'.implode('`, `', $columns).'`) VALUES'."\n";
            $sql .= implode(",\n", $values).";\n\n";
        }

        $path = $destination ?? database_path('exports/tfa7-current-data.sql');
        $directory = dirname($path);
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException('Cannot create the database exports directory.');
        }

        if (file_put_contents($path, $sql) === false) {
            throw new RuntimeException('Cannot write the MySQL export.');
        }

        return $path;
    }

    private function literal(?string $value): string
    {
        if ($value === null) {
            return 'NULL';
        }
        if ($value === '') {
            return "''";
        }

        return 'CONVERT(0x'.bin2hex($value).' USING utf8mb4)';
    }
}
