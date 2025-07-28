# Siaga Bapok - Sistem Informasi Harga Bahan Pokok

Sistem informasi untuk memantau harga bahan pokok di Kota Bandar Lampung secara real-time dengan visualisasi data yang interaktif.

## 📋 Deskripsi

Siaga Bapok adalah aplikasi web yang dirancang untuk memberikan transparansi informasi harga bahan pokok kepada masyarakat, pedagang, dan pemerintah daerah. Sistem ini menampilkan data harga dari berbagai pasar tradisional di Bandar Lampung dengan fitur grafik dan tabel yang mudah dipahami.

## 🚀 Fitur Utama

- **Dashboard Analytics**: Visualisasi data dengan grafik interaktif menggunakan Chart.js
- **Tabel Harga**: Data harga detail per pasar dan komoditas dengan filter
- **Top 3 Komoditas**: Menampilkan komoditas dengan kenaikan harga tertinggi
- **Export Data**: Unduh data dalam format CSV
- **Responsive Design**: Tampilan optimal di desktop dan mobile
- **Real-time Update**: Data diperbarui secara berkala

## 🛠️ Teknologi yang Digunakan

### Backend
- **PHP Native**: Server-side scripting tanpa framework
- **MySQL**: Database management system
- **Apache/Nginx**: Web server (via Laragon)

### Frontend
- **Bootstrap 5**: CSS framework untuk responsive design
- **Chart.js**: Library untuk visualisasi data
- **jQuery**: JavaScript library untuk interaktivitas
- **Bootstrap Icons**: Icon library

## 📁 Struktur Direktori

```
/siagabapok/
│
├── /assets/
│   ├── /css/
│   │   └── style.css          → CSS kustom
│   ├── /js/
│   │   └── main.js            → JavaScript utilities
│   └── /img/                  → Logo dan gambar
│
├── /includes/
│   ├── db.php                 → Koneksi database
│   ├── header.php             → Header dan navbar
│   └── footer.php             → Footer dan scripts
│
├── /pages/
│   ├── dashboard.php          → Dashboard analytics
│   ├── komoditas.php          → Tabel harga komoditas
│   └── about.php              → Informasi sistem
│
├── index.php                  → Halaman utama
├── database.sql               → Struktur database dan data sample
└── README.md                  → Dokumentasi
```

## 🗄️ Struktur Database

### Tabel `pasar`
- `id_pasar` (Primary Key)
- `nama_pasar`
- `alamat`
- `keterangan`
- `created_at`, `updated_at`

### Tabel `komoditas`
- `id_komoditas` (Primary Key)
- `nama_komoditas`
- `satuan`
- `kategori`
- `keterangan`
- `created_at`, `updated_at`

### Tabel `harga`
- `id_harga` (Primary Key)
- `id_pasar` (Foreign Key)
- `id_komoditas` (Foreign Key)
- `tanggal`
- `harga_per_kg`
- `stok_tersedia`
- `keterangan`
- `created_at`, `updated_at`

## 🚀 Instalasi dan Setup

### Prasyarat
- Laragon (atau XAMPP/WAMP)
- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web browser modern

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   # Jika menggunakan Git
   git clone [repository-url]
   
   # Atau download dan extract ke folder Laragon
   # Lokasi: C:\laragon\www\siagabapok\
   ```

2. **Setup Database**
   ```bash
   # Buka phpMyAdmin atau MySQL client
   # Import file database.sql
   mysql -u root -p < database.sql
   ```

3. **Konfigurasi Database**
   - Buka file `includes/db.php`
   - Sesuaikan konfigurasi database:
   ```php
   $host = 'localhost';
   $username = 'root';
   $password = '';
   $database = 'siagabapok_db';
   ```

4. **Jalankan Aplikasi**
   - Start Laragon
   - Buka browser dan akses: `http://localhost/siagabapok/`

## 📊 Data Sample

Database sudah dilengkapi dengan data sample meliputi:
- **12 Pasar**: Pasar tradisional di Bandar Lampung
- **25 Komoditas**: Bahan pokok utama
- **Data Harga**: 7 hari terakhir untuk semua kombinasi pasar-komoditas

### Pasar yang Tercakup
- Pasar Tugu
- Pasar Bambu Kuning
- Pasar Smep
- Pasar Kangkung
- Pasar Pasir Gintung
- Pasar Way Halim
- Dan lainnya...

### Komoditas Utama
- Beras (Premium & Medium)
- Cabai (Merah & Rawit)
- Bawang (Merah & Putih)
- Minyak Goreng
- Gula Pasir
- Daging (Sapi & Ayam)
- Dan lainnya...

## 🎯 Penggunaan

### Halaman Utama (index.php)
- Menampilkan hero section dengan informasi sistem
- Top 3 komoditas dengan kenaikan harga tertinggi
- Grafik pergerakan harga 7 hari terakhir
- Quick stats dan call-to-action

### Dashboard (pages/dashboard.php)
- Statistik umum (jumlah komoditas, pasar, dll.)
- Grafik line chart dengan filter komoditas
- Bar chart rata-rata harga hari ini
- Daftar harga tertinggi dan terendah

### Tabel Harga (pages/komoditas.php)
- Filter berdasarkan tanggal dan komoditas
- Tabel data harga dengan status (Tinggi/Normal/Rendah)
- Export ke CSV
- Statistik ringkas

### Tentang (pages/about.php)
- Informasi lengkap tentang sistem
- Teknologi yang digunakan
- Sumber data dan kontak

## 🎨 Customization

### Mengubah Warna Tema
Edit file `includes/header.php` pada bagian CSS variables:
```css
:root {
    --primary-green: #28a745;    /* Warna hijau utama */
    --light-green: #d4edda;      /* Hijau muda */
    --dark-green: #155724;       /* Hijau tua */
}
```

### Menambah Komoditas Baru
1. Insert ke tabel `komoditas`
2. Tambahkan data harga di tabel `harga`
3. Update akan otomatis muncul di semua halaman

### Menambah Pasar Baru
1. Insert ke tabel `pasar`
2. Tambahkan data harga untuk komoditas yang ada
3. Pasar baru akan muncul di filter dan tabel

## 📱 Responsive Design

Aplikasi dioptimalkan untuk berbagai ukuran layar:
- **Desktop**: Layout penuh dengan sidebar dan multiple columns
- **Tablet**: Layout adaptif dengan collapsible navigation
- **Mobile**: Single column layout dengan touch-friendly interface

## 🔧 Maintenance

### Update Data Harga
Data harga dapat diupdate dengan:
1. Manual via phpMyAdmin
2. Import CSV bulk data
3. API endpoint (jika dikembangkan)

### Backup Database
```bash
# Backup database
mysqldump -u root -p siagabapok_db > backup_siagabapok.sql

# Restore database
mysql -u root -p siagabapok_db < backup_siagabapok.sql
```

## 🐛 Troubleshooting

### Database Connection Error
- Pastikan MySQL service berjalan
- Cek konfigurasi di `includes/db.php`
- Pastikan database `siagabapok_db` sudah dibuat

### Chart Tidak Muncul
- Pastikan koneksi internet untuk CDN Chart.js
- Cek console browser untuk error JavaScript
- Pastikan data tersedia di database

### Halaman Blank/Error
- Aktifkan error reporting di PHP
- Cek log error Apache/Nginx
- Pastikan semua file ada dan readable

## 🤝 Kontribusi

Untuk berkontribusi pada project ini:
1. Fork repository
2. Buat feature branch
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## 📄 Lisensi

Project ini dibuat untuk keperluan edukasi dan pengembangan sistem informasi publik.

## 📞 Kontak

Untuk pertanyaan atau dukungan teknis:
- Email: info@siagabapok.com
- Telepon: (0721) 123456

---

**Siaga Bapok** - Sistem Informasi Harga Bahan Pokok Kota Bandar Lampung
Dikembangkan dengan ❤️ untuk transparansi informasi harga.