<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Master wilayah dijalankan eksplisit agar instalasi biasa tidak menimpa data produksi:
        // php artisan db:seed --class=Database\\Seeders\\WilayahAcehSeeder
    }
}
