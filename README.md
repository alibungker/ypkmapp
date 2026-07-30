# PEDULI YPKM

**Pendataan & Distribusi untuk Layanan Insani**
Yayasan Pelangi Kesejahteraan Masyarakat — [peduli.ypkm.info](https://peduli.ypkm.info)

## Status

Aplikasi Laravel dengan RBAC **Admin**, **Relawan**, dan **Ketua Kelompok**. Source aplikasi berada di `src/`, sedangkan scaffold Laravel, dependency lock, master wilayah Aceh, setup fresh clone, dan CI telah terversi di repository ini.

## Fitur aktif

- penerima dan kelompok dengan scope kelompok/wilayah;
- verifikasi oleh Admin/Relawan;
- distribusi, alokasi penerima terverifikasi, tanda terima, dan upload bukti JPG/PNG/PDF maksimal 5 MB;
- filter cascading Kabupaten → Kecamatan → Desa;
- peta distribusi dan boundary wilayah berbasis database;
- laporan dinamis, filter periode/wilayah, cetak browser, dan export CSV;
- keuangan, barang bantuan, dan biaya operasional;
- automated feature tests pada SQLite terisolasi.

## Fresh clone untuk development

Prasyarat: PHP **8.3+**, Composer 2, ekstensi `fileinfo` dan `pdo_sqlite`.

```bash
git clone https://github.com/alibungker/ypkmapp.git
cd ypkmapp
bash setup-local.sh
```

`setup-local.sh` akan:

1. memasang dependency dari `composer.lock`;
2. membuat `.env` lokal dan SQLite;
3. menyalin source `src/` ke runtime Laravel;
4. menjalankan seluruh migration;
5. mengimpor **6.810 wilayah Aceh** dan **28 boundary**;
6. menjalankan seluruh automated test.

## Deployment produksi

```bash
cd /www/wwwroot/peduli.ypkm.info
git pull --ff-only origin main
bash deploy.sh
php artisan test --do-not-cache-result
```

`deploy.sh` memerlukan `.env` dan `vendor/` yang sudah tersedia. Script menjalankan migration, membersihkan cache, memastikan storage link, serta menerapkan permission minimum pada `storage`, `bootstrap/cache`, dan `.env`.

> Jangan gunakan `chmod -R 777`. Jalankan Git dan deployment dengan satu akun deployment yang konsisten; produksi saat ini memakai user/group `www:www` untuk working tree, `.git`, `vendor`, dan storage.

## Master wilayah

Data versioned:

- `database/data/wilayah-aceh.csv.gz` — 6.810 record;
- `database/data/wilayah-boundaries-aceh.jsonl.gz` — 28 record.

Import idempotent:

```bash
php artisan db:seed --class='Database\Seeders\WilayahAcehSeeder' --force
```

Seeder memverifikasi jumlah record dan melakukan `upsert`, sehingga dapat dipakai pada MySQL maupun SQLite.

## Testing dan CI

```bash
php artisan test --do-not-cache-result
```

GitHub Actions: `.github/workflows/ci.yml`. Pipeline memvalidasi Composer, membangun runtime SQLite dari fresh checkout, mengimpor master wilayah, dan menjalankan test suite.

## Struktur utama

```text
src/                    # source aplikasi custom
app/, bootstrap/, config/ # scaffold Laravel terversi
database/data/          # master wilayah terkompresi
database/seeders/       # importer idempotent
.github/workflows/      # CI
setup-local.sh          # bootstrap fresh clone
deploy.sh               # deployment runtime
```

## Brand

| Elemen | Warna |
|---|---|
| Navy | `#00034a` |
| Green | `#017723` |
| Gold | `#e5a820` |
