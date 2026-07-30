# Peta Distribusi Interaktif

**Kode:** MAP-01 | **Versi:** 2.0 | **Diverifikasi:** 30 Juli 2026

## Sumber data

- Marker: tabel `distribusis` yang memiliki `titik_koordinat`.
- Statistik dan jumlah penerima: relasi Kelompok/Penerima.
- Boundary: tabel `wilayah_boundaries`, format GeoJSON.
- Tile: OpenStreetMap melalui Leaflet 1.9.4.

## Perilaku akses

| Role | Data yang terlihat |
|---|---|
| Admin | Seluruh Distribusi dan boundary |
| Ketua Kelompok | Distribusi pada `users.kelompok_id` |
| Relawan | Distribusi dalam kabupaten/kecamatan/desa penugasan |

## Informasi marker

Popup marker menampilkan nama kegiatan, jumlah paket, jumlah penerima, kelompok, tanggal, status, nilai, serta link detail. Tabel di bawah peta memakai dataset database yang sama.

## Integritas data

Selisih `jumlah_paket - jumlah_penerima` ditampilkan pada detail Distribusi dan tidak dikoreksi otomatis. Batch 4 saat audit akhir mempunyai 250 paket dan 242 penerima (+8), menunggu klasifikasi bisnis YPKM.

## Verifikasi

- halaman `/peta` HTTP 200 untuk Admin, Ketua, dan Relawan sesuai scope;
- marker Distribusi aktif terbaca;
- 28 boundary tersedia di database dan master versioned;
- tidak ada `production.ERROR` baru pada smoke test.
