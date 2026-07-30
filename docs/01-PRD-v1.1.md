# Product Requirements Document (PRD) - UPDATE
## PEDULI YPKM — Fitur Keuangan & Pelaporan
**Kode:** PRD-01 | **Revisi:** 1.1 | **Tanggal:** 30 Juli 2026

---

## Fitur Tambahan: Manajemen Keuangan & Inventaris

### 1. Manajemen Barang Bantuan (Inventory)

| Fitur | Deskripsi |
|:---|---|
| **Katalog Barang** | Data master jenis barang bantuan (sembako, pakaian, alat sekolah, dll) |
| **Harga Satuan** | Setiap barang memiliki harga per unit untuk nilai total bantuan |
| **Stok Masuk** | Catat barang masuk dari donatur beserta nilai/nominal |
| **Stok Keluar** | Catat barang yang didistribusikan, kurangi stok otomatis |
| **Nilai Total Bantuan** | Hitung otomatis total nilai rupiah dari setiap distribusi |

### 2. Biaya Operasional

| Fitur | Deskripsi |
|:---|---|
| **Kategori Biaya** | Transportasi, konsumsi relawan, sewa kendaraan, ATK, dll |
| **Catat Transaksi** | Tanggal, jumlah, kategori, keterangan, bukti foto |
| **Per Distribusi** | Biaya operasional dikaitkan dengan distribusi tertentu |
| **Anggaran** | Rencana anggaran vs realisasi |

### 3. Laporan Keuangan

| Fitur | Deskripsi |
|:---|---|
| **Neraca Bantuan** | Total nilai bantuan tersalurkan |
| **Laporan Biaya** | Total biaya operasional per periode |
| **Cost per Distribution** | Biaya operasional per paket sembako |
| **Rekap Donatur** | Sumber dana/barang + nilai |
| **Export Laporan** | PDF/Excel untuk akuntabilitas donatur |

### Struktur Data Tambahan

```
barang_bantuan
├── id, nama, kategori, satuan (kg/pcs/paket)
├── harga_perkiraan (untuk nilai bantuan)

stok_barang
├── barang_id, jumlah, sumber (donatur/nama)
├── tanggal_masuk, kadaluarsa, nilai

distribusi_items (pivot detail)
├── distribusi_id, barang_id, jumlah_per_paket
├── total_paket, total_nilai

biaya_operasional
├── distribusi_id (nullable), kategori
├── deskripsi, jumlah, tanggal
├── bukti_foto, dicatat_oleh
```
