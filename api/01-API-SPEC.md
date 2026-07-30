# API Specification (API)
## PEDULI YPKM — Sistem Informasi Penyaluran Bantuan Yayasan Pelangi Kesejahteraan Masyarakat
**Kode:** API-01 | **Versi:** 1.0

---

## 1. Base URL

```
Development: http://localhost:8000/api
Production:  https://peduli.ypkm.info/api
```

## 2. Endpoints

### 2.1 Autentikasi

| Method | Endpoint | Auth | Deskripsi |
|:---|:---|---:|:---|
| POST | `/login` | - | Login email + password |
| POST | `/logout` | ✅ | Logout |
| GET | `/user` | ✅ | Profil user saat ini |

### 2.2 Penerima

| Method | Endpoint | Auth | Deskripsi |
|:---|:---|---:|:---|
| GET | `/penerima` | ✅ | List penerima (filter by status) |
| GET | `/penerima/{id}` | ✅ | Detail penerima |
| POST | `/penerima` | ✅ | Tambah penerima baru |
| PUT | `/penerima/{id}` | ✅ | Edit penerima |
| DELETE | `/penerima/{id}` | ✅ | Hapus penerima |
| POST | `/penerima/daftar` | - | Registrasi mandiri (publik) |
| POST | `/penerima/import` | ✅ | Import Excel |
| GET | `/penerima/export` | ✅ | Export Excel |

**Request Body — Tambah Penerima:**
```json
{
    "nik": "1101010101010001",
    "no_kk": "1101010101010001",
    "nama": "Ahmad", 
    "tempat_lahir": "Lhokseumawe",
    "tanggal_lahir": "1985-06-15",
    "jenis_kelamin": "L",
    "alamat": "Jl. Merdeka No. 10",
    "provinsi": "Aceh",
    "kabupaten": "Aceh Utara",
    "kecamatan": "Lhoksukon",
    "desa": "Meunasah",
    "phone": "0812xxxxxx",
    "jumlah_keluarga": 4,
    "kelompok_id": 1,
    "sumber_data": "mandiri|relawan|ketua_kelompok"
}
```

### 2.3 Kelompok

| Method | Endpoint | Auth | Deskripsi |
|:---|:---|---:|:---|
| GET | `/kelompok` | ✅ | List kelompok |
| GET | `/kelompok/{id}` | ✅ | Detail + anggota |
| POST | `/kelompok` | ✅ | Tambah kelompok |
| PUT | `/kelompok/{id}` | ✅ | Edit kelompok |
| DELETE | `/kelompok/{id}` | ✅ | Hapus kelompok |
| GET | `/kelompok/{id}/anggota` | ✅ | Anggota kelompok |

### 2.4 Distribusi

| Method | Endpoint | Auth | Deskripsi |
|:---|:---|---:|:---|
| GET | `/distribusi` | ✅ | List distribusi |
| POST | `/distribusi` | ✅ | Buat jadwal |
| PUT | `/distribusi/{id}` | ✅ | Update distribusi |
| GET | `/distribusi/{id}/penerima` | ✅ | Daftar penerima distribusi |
| PUT | `/distribusi/{id}/terima/{penerimaId}` | ✅ | Tanda terima |
| POST | `/distribusi/{id}/selesai` | ✅ | Selesaikan distribusi |
| GET | `/distribusi/{id}/items` | ✅ | Daftar barang di distribusi |
| POST | `/distribusi/{id}/items` | ✅ | Tambah barang ke distribusi |
| GET | `/distribusi/{id}/biaya` | ✅ | Biaya operasional distribusi |
| POST | `/distribusi/{id}/biaya` | ✅ | Catat biaya operasional |
| GET | `/distribusi/{id}/laporan` | ✅ | Laporan lengkap (nilai barang + biaya) |

### 2.5 Barang Bantuan (Inventory)

| Method | Endpoint | Auth | Deskripsi |
|:---|:---|---:|:---|
| GET | `/barang` | ✅ | Katalog barang bantuan |
| POST | `/barang` | ✅ | Tambah jenis barang baru |
| PUT | `/barang/{id}` | ✅ | Edit barang |
| GET | `/stok` | ✅ | Stok barang masuk/keluar |
| POST | `/stok/masuk` | ✅ | Catat barang masuk dari donatur |
| GET | `/stok/{id}/mutasi` | ✅ | Riwayat mutasi stok |

### 2.5 Relawan

| Method | Endpoint | Auth | Deskripsi |
|:---|:---|---:|:---|
| GET | `/relawan` | ✅ | List relawan |
| POST | `/relawan` | ✅ | Tambah relawan |
| PUT | `/relawan/{id}` | ✅ | Edit relawan |
| PUT | `/relawan/{id}/tugas` | ✅ | Assign ke distribusi |

### 2.6 Dashboard

| Method | Endpoint | Auth | Deskripsi |
|:---|:---|---:|:---|
| GET | `/dashboard/stats` | ✅ | Statistik (total penerima, distribusi, dll) |
| GET | `/dashboard/rekap-per-daerah` | ✅ | Rekap per daerah |
| GET | `/dashboard/rekap-per-bulan` | ✅ | Grafik distribusi per bulan |

## 3. Response Format

```json
// Success
{
    "success": true,
    "message": "Data berhasil disimpan",
    "data": { ... }
}

// Error
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "nik": ["NIK sudah terdaftar"]
    }
}

// List with pagination
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 20,
        "total": 200
    }
}
```

## 4. HTTP Status Codes

| Code | Arti |
|:---:|:---|
| 200 | OK |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |
