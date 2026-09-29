# 🏹 ArcheryPro - Sistem Manajemen & Penilaian Panahan (v1.0.0)

Dokumentasi teknis resmi untuk proyek **ArcheryPro v1.0.0**, sebuah platform terintegrasi untuk klub dan akademi panahan yang mencakup **Web Portal (Laravel 10)** dan **Aplikasi Mobile (React Native Expo)** yang berkomunikasi secara langsung melalui **REST API v1**.

> [!NOTE]
> **Status Repositori**: Proyek ini saat ini berada di lingkungan pengembangan lokal (*local workspace*) dan belum dipublikasikan ke repositori publik GitHub. Seluruh instruksi dan jalur file menggunakan struktur direktori lokal.

---

## 🏛️ Arsitektur & Struktur Proyek

Proyek ini mengadopsi pemisahan arsitektur (*split workspace*) yang rapi antara backend/web dan aplikasi mobile:

```
ArcheryPro/
├── web/                                 # BACKEND LARAVEL 10 & WEB UI
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/V1/      # REST API Controllers (Auth, Sesi, Skor, Atlet, Public)
│   │   │   └── Requests/StoreSkorRequest.php # Validasi input skor & sanitize keys atlet
│   │   ├── Models/                      # Eloquent Models (User, Atlet, Sesi, SesiLatihan, Skor, dll.)
│   │   ├── Services/SkorService.php     # Business logic multi-atlet & handling soft delete
│   │   └── Support/ApiResponse.php      # Trait response standar JSON & paginasi ketat 10 data
│   ├── config/cors.php                  # Konfigurasi Cross-Origin Resource Sharing
│   ├── database/
│   │   ├── migrations/                  # Skema database & table constraints
│   │   └── seeders/
│   │       ├── DemoDataSeeder.php       # Data seeder resmi (Pelatih, Atlet, Event, Sekolah)
│   │       └── AdminSeeder.php          # Akun default administrator
│   ├── routes/
│   │   ├── api.php                      # Routing REST API v1 (/api/v1/...)
│   │   └── web.php                      # Routing antarmuka Web Blade
│   └── tests/                           # 41 Unit & Feature Test Cases (100% Pass)
│
└── mobile/                              # FRONTEND MOBILE (REACT NATIVE EXPO SDK 57)
    ├── App.js                           # Root component, AuthProvider & splash screen
    ├── package.json                     # Expo 57, React Native 0.86, AsyncStorage, Vector Icons
    └── src/
        ├── constants/theme.js           # Solid Forest Green Design System (#2E7D32, #1B5E20)
        ├── context/AuthContext.js       # Global session manager (token, user, role, atlet)
        ├── services/api.js              # REST client, dynamic IP resolver, offline drafts
        ├── components/
        │   ├── Header.js                # Top bar responsif dengan back button & role pill
        │   ├── CardStat.js              # Double-bezel metric card
        │   └── BadgeItem.js             # Kartu lencana pencapaian motivasi
        ├── screens/
        │   ├── LoginScreen.js           # Form login + quick chips demo + server URL config
        │   ├── DashboardScreen.js       # Ringkasan statistik, banner evaluasi, sesi terkini
        │   ├── SesiLatihanScreen.js     # Filter tab, paginasi 10 item, detail sesi & ranking
        │   ├── InputSkorScreen.js       # Keypad tembakan (X..M), multi-atlet, draft offline
        │   ├── PerformaScreen.js        # Moving average 4-sesi, stabilitas SD, riwayat 10-item, badges
        │   └── ProfilScreen.js          # Detail atlet, toggle privasi publik, uji koneksi API
        └── navigation/
            └── AppNavigator.js          # Bottom navigation responsif berbasis peran (Pelatih vs Atlet)
```

---

## 🎯 Akun Demo & Kredensial Resmi (v1.0.0)

Semua akun pengujian di bawah ini telah terdaftar resmi di database melalui `DemoDataSeeder.php`:

| Peran | Nama Lengkap | Username | Password | Deskripsi / Afiliasi |
|---|---|---|---|---|
| **Pelatih** | **Bambang Sutrisno** | `pelatih1` | `password123` | Pelatih Utama (SMAN 1 Rogojampi) |
| **Pelatih (Alternatif)** | **Sri Wahyuni** | `pelatih2` | `password123` | Pelatih Pendamping |
| **Petugas Scoring** | **Andi Kurnia** | `scoring1` | `password123` | Petugas Pencatat Skor Lapangan |
| **Petugas Scoring 2** | **Rudi Hartono** | `scoring2` | `password123` | Petugas Pencatat Skor Lapangan |
| **Atlet 1 (Utama)** | **Rizky Pratama** | `rizky1` | `password123` | Atlet Kategori Recurve (SMAN 1 Rogojampi) |
| **Atlet 2** | **Dewi Anggraini** | `dewi2` | `password123` | Atlet Kategori Compound (SMKN 1 Rogojampi) |
| **Atlet 3** | **Budi Santoso** | `budi3` | `password123` | Atlet Kategori Traditional (SMPN 1 Rogojampi) |
| **Atlet 4** | **Dimas Prasetyo** | `dimas7` | `password123` | Atlet Kategori Recurve (SMPN 2 Genteng) |
| **Administrator** | **Administrator** | `admin` | `admin123` | Akses Penuh Sistem & Pengaturan |

> [!TIP]
> Pada aplikasi mobile ([`LoginScreen.js`](file:///c:/xampp/htdocs/Non-Production/ArcheryPro/mobile/src/screens/LoginScreen.js)), tersedia tombol **Chip Pilihan Cepat** untuk langsung mengisi kredensial Pelatih, Atlet, Scoring, atau Admin tanpa harus mengetik manual.

---

## ⚡ Fitur yang Diimplementasikan pada Versi 1.0.0

### 1. Autentikasi & Manajemen Sesi
- **Laravel Sanctum Token**: Akses REST API mobile diamankan dengan Bearer token.
- **Auto Re-check**: Saat aplikasi mobile dibuka kembali, token otomatis diverifikasi melalui `/api/v1/auth/me`.
- **Logout Aman**: Mencabut token di server dan membersihkan cache AsyncStorage di HP.

### 2. Jadwal Sesi Latihan Mingguan (`SesiLatihan`)
- Pencatatan jadwal latihan: nama sesi, tanggal, jam mulai/selesai, lokasi, jenis latihan, dan status (*Terjadwal*, *Berlangsung*, *Selesai*).
- **Deteksi Tanggal Hari Ini**: Menampilkan penanda `HARI INI` otomatis untuk sesi yang jatuh pada tanggal sekarang.
- **Paginasi Ketat 10 Item**: Seluruh daftar sesi mematuhi standar 10 item per halaman.
- **Detail Sesi**: Menampilkan metrik agregat (total peserta, skor tertinggi, rata-rata skor) serta ranking atlet per end.

### 3. Penilaian Lapangan Interaktif (`InputSkor`)
- **Keypad Target Panahan**: Tombol tembakan standar World Archery: `X`, `10`, `9`, `8`, `7`, `6`, `5`, `4`, `3`, `2`, `1`, `M (Miss/0)`.
- **Auto-Advance Arrow Slot**: Kursor otomatis bergeser ke slot anak panah berikutnya setelah ditekan (Arrow 1 s/d 6).
- **Perhitungan Realtime**: Menghitung subtotal per-end (End 1..6) dan total skor kumulatif secara langsung.
- **Multi-Athlete Scoring**: Pelatih dapat memilih satu atau beberapa atlet sekaligus dalam satu lembar penilaian.
- **Penyimpanan Draft Offline**: Dukungan penyimpanan lokal melalui `@react-native-async-storage/async-storage` agar data tidak hilang saat koneksi lapangan terputus.

### 4. Analisis Performa & Stabilitas Atlet (`Performa`)
- **Moving Average (4 Sesi Bergerak)**: Grafik dan daftar nilai rata-rata 4 sesi berurutan untuk melihat kestabilan tren.
- **Indeks Konsistensi (Standar Deviasi)**:
  - Deviasi $\le 2.2$ ➔ **Sangat Stabil**
  - Deviasi $2.3 - 4.0$ ➔ **Konsisten**
  - Deviasi $> 4.0$ ➔ **Perlu Perbaikan**
- **Catatan Evaluasi Pelatih**: Pelatih dapat memberikan masukan teknik per sesi latihan. Atlet menerima banner notifikasi baru dan tombol *"Tandai Dibaca"*.
- **Lencana Motivasi (Badges)**:
  - *Kehadiran*: Rajin Latihan (4 sesi), Disiplin Juara (8 sesi), Dedikasi Penuh (15+ sesi).
  - *Akurasi*: Tembus 250 Poin, Akurasi Tinggi (280+), Master Archer (300+ poin).
  - *Volume*: Menuju 300 Panah, 300+ Anak Panah.

### 5. Privasi Atlet & Papan Peringkat Publik
- Atlet dapat mengaktifkan atau menonaktifkan izin tampil di papan publik (*opt-in privacy*) melalui `PUT /api/v1/atlet/privasi`.
- Endpoint publik `/api/v1/public/leaderboard` hanya menampilkan skor atlet yang telah memberikan izin privasi.

---

## 📡 Referensi REST API v1 (`/api/v1`)

Format standar respons JSON:
```json
{
  "success": true,
  "message": "Pesan status keberhasilan",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 12,
    "last_page": 2,
    "has_more": true
  }
}
```

### Daftar Endpoint

| Method | Endpoint | Akses | Parameter / Body Utama |
|---|---|---|---|
| `POST` | `/api/v1/auth/login` | Publik | `{ username, password }` |
| `GET` | `/api/v1/auth/me` | Bearer Token | - |
| `POST` | `/api/v1/auth/logout` | Bearer Token | - |
| `GET` | `/api/v1/sesi-latihan` | Bearer Token | `page` (default 1), `status` (opsional) |
| `GET` | `/api/v1/sesi-latihan/{id}` | Bearer Token | `id` sesi latihan |
| `GET` | `/api/v1/atlet` | Bearer Token | `q` (opsional pencarian nama/NIA) |
| `POST` | `/api/v1/skor` | Bearer Token | `{ sesi_latihan_id, tanggal_sesi, jarak_meter, atlet_ids, skor }` |
| `GET` | `/api/v1/atlet/{id}/performa` | Bearer Token | `id` atlet |
| `GET` | `/api/v1/atlet/{id}/riwayat` | Bearer Token | `id` atlet, `page` |
| `POST` | `/api/v1/sesi/{sesi}/baca-catatan` | Bearer Token | `sesi` ID |
| `POST` | `/api/v1/sesi/{sesi}/catatan` | Pelatih/Admin | `{ catatan_pelatih }` |
| `PUT` | `/api/v1/atlet/privasi` | Atlet | `{ izinkan_tampil_publik: boolean }` |
| `GET` | `/api/v1/public/leaderboard` | Publik | `page` |
| `GET` | `/api/v1/public/sesi-latihan` | Publik | `page` |

---

## 🛠️ Riwayat Bug yang Ditemukan & Solusi Teknis (v1.0.0)

Berikut adalah ringkasan masalah teknis nyata yang telah diselesaikan pada versi 1.0.0:

1. **`Fetch request has been canceled` pada Expo Mobile**:
   - *Penyebab*: Server backend Laravel belum dijalankan di port 8000, atau HP fisik mencoba menghubungi `localhost` / `10.0.2.2`.
   - *Solusi*: Menjalankan backend dengan `--host=0.0.0.0 --port=8000` dan mengimplementasikan auto-detection host dev machine via `NativeModules.SourceCode.scriptURL` di `mobile/src/services/api.js` (mengarah ke IP WiFi laptop `192.168.1.4:8000`).
2. **`Skor atlet di luar pilihan checkbox` pada Validasi `StoreSkorRequest`**:
   - *Penyebab*: State `scores` di mobile menyimpan data atlet yang sempat dicentang lalu dibatalkan, sehingga payload `skor` mengandung kunci ID di luar array `atlet_ids`.
   - *Solusi*: Frontend menyaring `cleanSkor` hanya untuk atlet terpilih, dan backend menambahkan `prepareForValidation()` untuk menyaring kunci array `skor` secara otomatis.
3. **`Duplicate entry for key uniq_sesi_atlet`**:
   - *Penyebab*: Tabel `sesi` menggunakan `SoftDeletes`, namun constraint unik MySQL memperhitungkan baris yang berstatus soft-deleted saat `firstOrCreate()` dipanggil.
   - *Solusi*: Menggunakan `Sesi::withTrashed()->where(...)` dan melakukan `$sesi->restore()` pada `SkorService.php`, serta `forceDelete()` pada pembersihan seeder.
4. **Error Model `Pengaturan`**:
   - *Penyebab*: `PublicController` mengimpor `App\Models\Pengaturan` yang belum ada, seharusnya `App\Models\PengaturanSistem::getValue()`.
   - *Solusi*: Menyelaraskan pemanggilan model ke `PengaturanSistem`.
5. **Struktur Data Paginasi**:
   - *Penyebab*: Beberapa komponen mobile mengharapkan `res.data.data`, sementara `ApiResponse` langsung menaruh array data pada `res.data`.
   - *Solusi*: Standarisasi ekstraksi data menggunakan `Array.isArray(res?.data) ? res.data : (res?.data?.data || [])`.

---

## 🚀 Panduan Menjalankan Sistem Secara Lokal

### 1. Menjalankan Backend Laravel (`web/`)
Buka terminal pada direktori `web/`:
```bash
cd c:\xampp\htdocs\Non-Production\ArcheryPro\web
php artisan serve --host=0.0.0.0 --port=8000
```
> Server akan aktif di `http://127.0.0.1:8000` dan dapat diakses dari jaringan lokal di `http://192.168.1.4:8000`.

### 2. Menjalankan Aplikasi Mobile Expo (`mobile/`)
Buka terminal baru pada direktori `mobile/`:
```bash
cd c:\xampp\htdocs\Non-Production\ArcheryPro\mobile
npm start
```

Pilihan testing:
- **HP Fisik**: Scan QR code terminal dengan aplikasi **Expo Go**. Pastikan HP terhubung ke jaringan Wi-Fi yang sama dengan laptop.
- **Web Browser**: Tekan tombol **`w`** di terminal untuk membuka di browser PC.
- **Android Emulator**: Tekan tombol **`a`** di terminal untuk emulator Android Studio.

---

## 🎨 Standar Desain Sistem
- **Solid Green Forest**: `#2E7D32` (Utama), `#1B5E20` (Tua/Border), `#4CAF50` (Aksen Terang).
- **Zero Gradient**: Seluruh antarmuka web dan mobile menggunakan warna solid yang tajam dan konsisten.
- **Paginasi Konsisten**: Seluruh daftar data menggunakan paginasi seragam 10 baris per halaman.

---

<p align="center">
  <b>ArcheryPro v1.0.0</b> • Sistem Panahan Digital Berkinerja Tinggi
</p>
