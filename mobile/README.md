# ArcheryPro Mobile (React Native + REST API)

Aplikasi mobile ArcheryPro dibangun menggunakan **React Native (Expo SDK 57)** terhubung secara penuh dengan **Laravel REST API v1** menggunakan autentikasi Bearer Token Sanctum.

---

## 📱 Fitur Utama Aplikasi Mobile

1. **Autentikasi & Role Management**:
   - Login untuk akun **Pelatih** (`coach_budi` / `coach_api`) dan **Atlet** (`atlet_dimas` / `atlet_api`).
   - Token Sanctum disimpan aman via `@react-native-async-storage/async-storage`.
   - Konfigurasi alamat IP server dinamis (mendukung Android Emulator `10.0.2.2`, iOS/Web `localhost`, dan IP WiFi HP fisik).

2. **Dashboard Interaktif**:
   - Kartu ringkasan aktivitas (Total Sesi, Rata-rata Skor, Rekor Terbaik, Indeks Konsistensi).
   - Banner alert catatan evaluasi baru dari pelatih (khusus atlet) dengan tombol "Tandai Dibaca".
   - Carousel & daftar sesi latihan terkini dengan badge status solid green.
   - Papan peringkat publik top atlet.

3. **Jadwal & Detail Sesi Latihan (`SesiLatihanScreen`)**:
   - Filter tab: `Semua`, `Berlangsung`, `Terjadwal`, `Selesai`.
   - **Paginasi wajib 10 data per halaman** (`page=1, 2, ...`).
   - Modal detail sesi dengan ringkasan statistik (peserta, skor tertinggi, rata-rata) dan tabel peringkat atlet per end.
   - Tombol langsung input skor untuk sesi terkait (khusus pelatih).

4. **Input Skor Panahan Lapangan (`InputSkorScreen`)**:
   - Papan keypad panahan interaktif (X, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, M / Miss 0).
   - Input tembakan per-end (End 1 s/d 6, masing-masing 6 anak panah).
   - Perhitungan otomatis running total, total end, dan jumlah X / 10.
   - Pilihan multi-atlet untuk pelatih saat melatih rombongan.
   - **Offline Draft Storage**: Fitur simpan draft lokal & pulihkan draft jika koneksi internet terputus di lapangan.

5. **Analisis Performa Atlet (`PerformaScreen`)**:
   - **Moving Average**: Rata-rata bergerak 4 sesi latihan.
   - **Stabilitas & Konsistensi**: Standar deviasi tembakan per end (Sangat Stabil, Konsisten, Perlu Perbaikan).
   - **Riwayat Sesi**: Paginasi ketat 10 data per halaman lengkap dengan catatan evaluasi pelatih.
   - **Badges Motivasi**: Pencapaian kehadiran, akurasi skor, dan akumulasi volume panah.
   - Pemberian & revisi catatan evaluasi langsung oleh pelatih.

6. **Pengaturan & Profil (`ProfilScreen`)**:
   - Data keanggotaan atlet (NIA, Sekolah, Kategori, Nomor Target).
   - Toggle privasi `izinkan_tampil_publik` via endpoint REST API `PUT /atlet/privasi`.
   - Pengujian koneksi server API & ganti URL host secara realtime.
   - Fitur logout aman.

---

## 🎨 Standar Desain
- **Palette**: Solid Forest Green (`#2E7D32`), Deep Green (`#1B5E20`), Light Accent (`#4CAF50`), Background (`#F8FAFC`).
- **Nol Gradien**: Sesuai instruksi, seluruh elemen menggunakan warna solid yang rapi dan konsisten.
- **Paginasi**: Seluruh daftar data menggunakan paginasi ketat 10 item per halaman.

---

## 🚀 Cara Menjalankan

### 1. Jalankan Backend Laravel REST API
Buka terminal pada folder `web/`:
```bash
cd web
php artisan serve --host=0.0.0.0 --port=8000
```
> Menggunakan `--host=0.0.0.0` memungkinkan perangkat Android Emulator dan HP fisik di jaringan WiFi yang sama untuk mengakses API.

### 2. Jalankan Mobile App (Expo)
Buka terminal pada folder `mobile/`:
```bash
cd mobile
npm start
```

Pilihan testing:
- **Android Emulator**: Tekan `a` pada terminal Expo (otomatis mengarah ke `http://10.0.2.2:8000/api/v1`).
- **Web Browser**: Tekan `w` pada terminal Expo (mengarah ke `http://localhost:8000/api/v1`).
- **HP Fisik (Expo Go)**: Scan QR code dengan aplikasi Expo Go di HP Anda, pastikan setting URL di halaman login diarahkan ke IP laptop Anda (misal `http://192.168.1.5:8000/api/v1`).
