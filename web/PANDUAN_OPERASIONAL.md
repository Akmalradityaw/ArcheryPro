# PANDUAN OPERASIONAL & LEMBAR UAT LAPANGAN
## ArcheryPro — Platform Pencatatan Skor Latihan Mingguan

---

## 🎯 1. Ringkasan Singkat Peran Pengguna (Quick Start Guide 1 Lembar)

```
[ Admin ] ──( 1-Klik Salin Jadwal )──> [ Jadwal Sesi Latihan ]
                                               │
                                               ▼
[ Petugas Scoring ] ──( Input Multi-Atlet & Offline Auto-Save )──> [ Nilai & End ]
                                                                          │
       ┌──────────────────────────────────────────────────────────────────┴───────────────┐
       ▼                                                                                  ▼
[ Pelatih ]                                                                         [ Atlet ]
- Evaluasi Catatan Teknik                                                           - Cek Skor & Grafik Linier
- Pantau Stabilitas (Standar Deviasi)                                               - Terima Catatan Saran Pelatih
- Analisis Tren 4 Sesi (Moving Average)                                             - Tandai Catatan Sudah Dibaca
- Ekspor Laporan Rekap Mingguan (Excel/PDF)                                         - Buka Badge Motivasi & Atur Privasi
```

---

### 🛡️ A. Alur Admin (Jadwal Latihan & Pengaturan Sistem)
1. **Membuat Jadwal Latihan**:
   - Buka menu **Jadwal Latihan** (`/admin/sesi-latihan`).
   - Klik **Tambah Jadwal Baru**, isi nama sesi (contoh: *Latihan Rutin Sabtu Pagi*), tanggal, jam, lokasi, tipe latihan, dan target jarak.
2. **Fitur 1-Klik "Copy Jadwal Pekan Lalu"**:
   - Untuk menghemat waktu tanpa mengetik ulang setiap minggu, klik tombol **"Salin Jadwal ke Pekan Depan"** pada baris jadwal yang diinginkan.
   - Sistem otomatis membuat jadwal baru untuk tanggal 7 hari berikutnya (H+7) dengan status *Terjadwal*.
3. **Privasi Papan Skor Global**:
   - Di menu **Pengaturan** (`/admin/pengaturan`), atur opsi **Akses Papan Skor Publik** (*Aktif* atau *Nonaktif*).

---

### 📱 B. Alur Petugas Lapangan (Scoring Multi-Atlet & Offline Draft)
1. **Memilih Sesi Latihan**:
   - Buka menu **Input Skor** (`/scoring/input`).
   - Pilih jadwal latihan yang sedang berlangsung (misal: *Latihan Rutin Sabtu*).
2. **Input Multi-Atlet Simultan**:
   - Centang atlet-atlet yang berada di bantalan tembak (nomor target).
   - Masukkan nilai tiap anak panah (0–10) untuk masing-masing atlet.
3. **Keamanan Data di Ruang Terbuka (Offline-First Draft)**:
   - Setiap ketikan angka otomatis tersimpan di perangkat lokal (`localStorage`).
   - Jika koneksi internet terputus atau browser ter-refresh di lapangan tembak, sistem menampilkan banner: **"Draf Masih Tersimpan"** dan tombol **"Pulihkan Draf"**.
   - Setelah koneksi stabil, klik **Simpan & Konfirmasi**.

---

### 🏹 C. Alur Pelatih (Analisis Tren & Evaluasi Bimbingan)
1. **Evaluasi Sesi Latihan**:
   - Buka menu **Jadwal Latihan** (`/pelatih/sesi-latihan`).
   - Pilih sesi latihan hari ini, lihat hasil skor peserta, dan klik tombol **Catatan** pada atlet yang ingin diberikan koreksi teknik (misal: *"Perbaiki sudut siku saat ekspansi kliker"*).
2. **Membaca Analisis Performa Atlet**:
   - Buka menu **Analisis** (`/pelatih/analisis/{id}`).
   - **Standar Deviasi per End**: Nilai $\le 2.2$ menandakan tembakan *Sangat Stabil/Konsisten*. Nilai $> 4.0$ mengindikasikan fluktuasi akurasi tembakan.
   - **Tren 4 Sesi (Moving Average)**: Garis putus-putus pada grafik memperlihatkan apakah performa 4 minggu terakhir meningkat atau menurun.
   - **Volume Panah**: Pantau total tembakan panah atlet bulan ini untuk mencegah kelelahan berlebih.
3. **Ekspor Laporan**:
   - Buka menu **Ekspor** (`/pelatih/ekspor`), pilih sesi latihan mingguan atau seluruh sesi, lalu pilih format **Excel** atau **PDF**.

---

### 🎯 D. Alur Atlet (Dashboard Progres, Badge & Privasi)
1. **Membaca Catatan Pelatih**:
   - Setelah pelatih mengirimkan evaluasi, dashboard atlet (`/atlet/dashboard`) akan memunculkan banner alert: **"Catatan Pelatih Baru"**.
   - Setelah membaca saran instruksi, klik tombol **"Tandai Sudah Dibaca"**.
2. **Lencana Motivasi (Badges)**:
   - Atlet secara otomatis membuka lencana saat mencapai milestone:
     - *Kehadiran*: Rajin Latihan (4 sesi), Disiplin Juara (8 sesi), Dedikasi Penuh (15+ sesi).
     - *Akurasi*: Tembus 250 Poin, Akurasi Tinggi (280+), Master Archer (300+).
     - *Volume*: Target 300 Anak Panah.
3. **Kendali Privasi Skor**:
   - Di menu **Profil** (`/atlet/profil`), atlet dapat mencentang opsi **"Tampilkan skorku di papan peringkat publik"**.
   - Jika tidak dicentang, hasil latihan atlet bersifat rahasia dan hanya dapat diakses oleh dirinya sendiri, pelatih, dan admin.

---

## 📋 2. Lembar Ceklis Pengujian Lapangan (UAT Test Script - Sabtu)

| No | Skenario Pengujian | Aktor | Hasil yang Diharapkan | Status |
|:--:|:-------------------|:-----:|:----------------------|:------:|
| 1 | Admin membuat jadwal sesi latihan baru & mencoba tombol "Salin Jadwal ke Pekan Depan" | Admin | Jadwal terduplikasi ke H+7 secara instan tanpa error | [ ] |
| 2 | Petugas scoring input nilai 3 atlet bersamaan di smartphone | Scoring | Total skor dan rata-rata per end terhitung otomatis secara real-time | [ ] |
| 3 | Uji offline draft: matikan koneksi internet ponsel, refresh browser, klik "Pulihkan Draf" | Scoring | Angka skor yang baru diisi pulih kembali tanpa data hilang | [ ] |
| 4 | Pelatih membuka detail sesi latihan dan menuliskan saran teknik untuk atlet | Pelatih | Catatan tersimpan dan status baca atlet ter-reset ke belum dibaca | [ ] |
| 5 | Pelatih memeriksa grafik garis ganda (Skor Aktual vs Moving Average) | Pelatih | Garis Chart.js tampil rapi dengan warna solid hijau (#2E7D32) | [ ] |
| 6 | Atlet login dan melihat alert catatan pelatih di dashboard | Atlet | Banner catatan tampil jelas, tombol "Tandai Sudah Dibaca" berfungsi | [ ] |
| 7 | Atlet memeriksa badge pencapaian latihan | Atlet | Lencana terbuka sesuai rekor skor dan jumlah kehadiran latihan | [ ] |
| 8 | Atlet menonaktifkan centang privasi "izinkan_tampil_publik" | Atlet | Skor atlet tidak muncul di halaman publik `/sesi-latihan/{id}` bagi tamu | [ ] |
| 9 | Pelatih mengunduh laporan PDF dan Excel rekap mingguan | Pelatih | Berkas terunduh cepat tanpa beban memori tinggi (chunking) | [ ] |
