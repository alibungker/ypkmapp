<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WilayahAcehSeeder extends Seeder
{
    public function run(): void
    {
        $wilayahFile = database_path('data/wilayah-aceh.csv.gz');
        $boundaryFile = database_path('data/wilayah-boundaries-aceh.jsonl.gz');
        foreach ([$wilayahFile, $boundaryFile] as $file) {
            if (!is_file($file)) throw new RuntimeException("Master data tidak ditemukan: {$file}");
        }

        DB::transaction(function () use ($wilayahFile, $boundaryFile) {
            $wilayahCount = $this->importWilayah($wilayahFile);
            $boundaryCount = $this->importBoundaries($boundaryFile);
            if ($wilayahCount !== 6810 || $boundaryCount !== 28) {
                throw new RuntimeException("Jumlah master tidak sesuai: wilayah={$wilayahCount}, boundary={$boundaryCount}");
            }
        });

        $this->command?->info('Master Aceh selesai: 6.810 wilayah dan 28 boundary.');
    }

    private function importWilayah(string $file): int
    {
        $handle = gzopen($file, 'rb');
        if ($handle === false) throw new RuntimeException('Gagal membuka master wilayah.');
        fgetcsv($handle); // header
        $count = 0;
        $batch = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;
            $batch[] = ['kode' => trim($row[0]), 'nama' => trim($row[1])];
            $count++;
            if (count($batch) >= 500) {
                DB::table('wilayah')->upsert($batch, ['kode'], ['nama']);
                $batch = [];
            }
        }
        gzclose($handle);
        if ($batch) DB::table('wilayah')->upsert($batch, ['kode'], ['nama']);
        return $count;
    }

    private function importBoundaries(string $file): int
    {
        $handle = gzopen($file, 'rb');
        if ($handle === false) throw new RuntimeException('Gagal membuka master boundary.');
        $count = 0;
        $batch = [];
        while (!gzeof($handle)) {
            $line = trim((string) gzgets($handle));
            if ($line === '') continue;
            $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
            $batch[] = [
                'kode' => $row['kode'],
                'nama' => $row['nama'],
                'lat' => $row['lat'] ?? null,
                'lng' => $row['lng'] ?? null,
                'path' => $row['path'] ?? null,
            ];
            $count++;
        }
        gzclose($handle);
        if ($batch) DB::table('wilayah_boundaries')->upsert($batch, ['kode'], ['nama', 'lat', 'lng', 'path']);
        return $count;
    }
}
