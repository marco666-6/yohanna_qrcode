# Dokumentasi Revisi Sidang: Validasi Lokasi Absensi QR

## 1. Latar Belakang Revisi

Masukan sidang menyatakan bahwa QR Code yang tampil di halaman employee berpotensi dipakai dari rumah atau lokasi lain. Risiko ini muncul karena pada revisi sebelumnya QR aktif dibuat tersedia juga di akun employee sebagai shortcut, padahal rancangan awal menempatkan QR dari akun admin di lokasi fisik seperti pos sekuriti atau pintu masuk ruang kerja.

Karena aplikasi tidak boleh di-reset dan shortcut employee tetap harus dipertahankan, solusi yang diterapkan adalah menambahkan location guard. Artinya QR tetap bekerja seperti sebelumnya, tetapi proses check-in/check-out hanya disetujui server apabila perangkat employee berada di radius lokasi yang sudah dikonfigurasi.

## 2. Konsep Solusi

Sebelum revisi ini, validasi absensi berisi:

1. QR harus ada.
2. QR harus aktif dan belum expired.
3. QR harus sesuai shift employee.
4. QR harus berada dalam attendance window.

Setelah revisi ini, ditambahkan validasi kelima:

5. Lokasi perangkat harus berada dalam radius titik absensi yang ditentukan.

Dengan pendekatan ini, shortcut QR di halaman employee tidak perlu dihapus. Walaupun employee bisa melihat QR dari rumah, request absensi akan ditolak karena koordinat perangkat berada di luar radius kantor.

## 3. Cara Kerja Teknis

Alur check-in/check-out baru:

1. Employee membuka halaman `Absensi Saya`.
2. Jika QR aktif tersedia, employee menekan tombol check-in/check-out, scan QR, atau input kode manual.
3. Browser meminta izin lokasi perangkat melalui Web Geolocation API.
4. Frontend mengirim `code`, `location_latitude`, `location_longitude`, dan `location_accuracy` ke endpoint `/attendance/scan`.
5. Backend memvalidasi QR seperti sebelumnya.
6. Backend menghitung jarak perangkat terhadap titik absensi menggunakan rumus Haversine.
7. Jika jarak lebih kecil atau sama dengan radius, absensi diproses.
8. Jika jarak lebih besar dari radius, absensi ditolak dengan pesan bahwa lokasi berada di luar area yang diizinkan.

Validasi lokasi berada di backend, sehingga user tidak bisa hanya memodifikasi tampilan frontend untuk melewati pengecekan utama.

## 4. Konfigurasi Lokasi

Konfigurasi ditambahkan di `.env.example` dan dibaca melalui `config/attendance.php`.

```env
ATTENDANCE_LOCATION_ENABLED=false
ATTENDANCE_LOCATION_LATITUDE=
ATTENDANCE_LOCATION_LONGITUDE=
ATTENDANCE_LOCATION_RADIUS_METERS=100
ATTENDANCE_LOCATION_MAX_ACCURACY_METERS=150
ATTENDANCE_LOCATION_LABEL="Lokasi kantor"
```

Penjelasan:

- `ATTENDANCE_LOCATION_ENABLED`: mengaktifkan atau mematikan validasi lokasi.
- `ATTENDANCE_LOCATION_LATITUDE`: koordinat latitude titik absensi.
- `ATTENDANCE_LOCATION_LONGITUDE`: koordinat longitude titik absensi.
- `ATTENDANCE_LOCATION_RADIUS_METERS`: radius area absensi yang diterima.
- `ATTENDANCE_LOCATION_MAX_ACCURACY_METERS`: batas maksimum akurasi GPS yang diterima dari perangkat.
- `ATTENDANCE_LOCATION_LABEL`: nama lokasi yang tampil di pesan UI dan error.

Contoh konfigurasi:

```env
ATTENDANCE_LOCATION_ENABLED=true
ATTENDANCE_LOCATION_LATITUDE=-6.200000
ATTENDANCE_LOCATION_LONGITUDE=106.816666
ATTENDANCE_LOCATION_RADIUS_METERS=100
ATTENDANCE_LOCATION_MAX_ACCURACY_METERS=150
ATTENDANCE_LOCATION_LABEL="Pos Sekuriti Kantor"
```

Setelah mengubah `.env`, jalankan:

```powershell
php artisan config:clear
php artisan optimize:clear
```

## 5. File yang Diubah

### `app/Services/AttendanceLocationService.php`

File baru untuk memisahkan logika lokasi dari controller. Isi utamanya:

- `isEnabled()` mengecek apakah validasi lokasi aktif.
- `getConfiguredLocation()` membaca latitude, longitude, radius, akurasi, dan label dari config.
- `validateScanLocation()` memvalidasi request absensi berdasarkan lokasi perangkat.
- `calculateDistanceMeters()` menghitung jarak dua koordinat dengan rumus Haversine.

### `app/Http/Controllers/AttendanceController.php`

Endpoint `/attendance/scan` sekarang menerima data lokasi:

- `location_latitude`
- `location_longitude`
- `location_accuracy`

Setelah QR valid, controller memanggil `AttendanceLocationService`. Jika lokasi tidak valid, controller mengembalikan response error dan tidak membuat data attendance. Jika valid, proses check-in/check-out berjalan seperti sebelumnya.

Activity log juga menambahkan informasi jarak lokasi ketika validasi lokasi aktif, misalnya:

```text
Check-in successful at 08:01:20 within 24.5m from configured location
```

### `resources/views/employee/scanner.blade.php`

Halaman employee sekarang:

- menampilkan panel status validasi lokasi,
- meminta izin lokasi sebelum submit absensi,
- mengirim koordinat dan akurasi ke backend,
- menampilkan jarak perangkat pada popup sukses dan popup gagal jika koordinat sudah terbaca,
- menampilkan akurasi lokasi perangkat agar admin/user tahu apakah GPS/browser sedang cukup presisi,
- tetap mendukung tombol langsung, scanner kamera, dan input manual.

### `resources/views/admin/qr-code.blade.php`

Halaman QR admin sekarang menampilkan ringkasan status validasi lokasi. Jika aktif, admin dapat melihat radius yang digunakan. Jika belum aktif, halaman memberi sinyal bahwa konfigurasi server belum mengunci lokasi.

### `config/attendance.php`

Ditambahkan blok konfigurasi:

```php
'location' => [
    'enabled' => (bool) env('ATTENDANCE_LOCATION_ENABLED', false),
    'latitude' => env('ATTENDANCE_LOCATION_LATITUDE') !== null && env('ATTENDANCE_LOCATION_LATITUDE') !== '' ? (float) env('ATTENDANCE_LOCATION_LATITUDE') : null,
    'longitude' => env('ATTENDANCE_LOCATION_LONGITUDE') !== null && env('ATTENDANCE_LOCATION_LONGITUDE') !== '' ? (float) env('ATTENDANCE_LOCATION_LONGITUDE') : null,
    'radius_meters' => (int) env('ATTENDANCE_LOCATION_RADIUS_METERS', 100),
    'max_accuracy_meters' => (int) env('ATTENDANCE_LOCATION_MAX_ACCURACY_METERS', 150),
    'label' => env('ATTENDANCE_LOCATION_LABEL', 'Lokasi kantor'),
],
```

### `.env.example`

Ditambahkan contoh variabel environment agar client/developer tahu parameter apa yang harus diisi ketika deploy.

## 6. Dampak ke Rancangan Aplikasi

Revisi ini mengembalikan prinsip desain awal secara fungsional:

- QR tetap dapat ditampilkan oleh admin di lokasi fisik.
- Shortcut QR employee tetap dipertahankan karena sudah menjadi bagian revisi sebelumnya.
- Absensi tidak lagi hanya bergantung pada kepemilikan kode QR.
- Employee harus berada di lokasi aktual yang ditentukan agar absensi diterima.

Dengan kata lain, yang dikunci bukan tampilnya QR, tetapi proses absensinya.

## 7. Batasan dan Catatan Keamanan

Validasi lokasi browser bergantung pada izin lokasi dan kualitas GPS/perangkat. Karena itu ada konfigurasi `ATTENDANCE_LOCATION_MAX_ACCURACY_METERS` agar lokasi dengan akurasi terlalu buruk bisa ditolak.

Untuk kebutuhan produksi yang lebih ketat, validasi ini bisa dikombinasikan dengan:

- jaringan Wi-Fi kantor,
- device binding,
- audit log lokasi,
- multi titik lokasi untuk kantor/cabang berbeda,
- penyimpanan koordinat check-in/check-out ke tabel attendance.

Namun untuk kebutuhan revisi sidang saat ini, validasi radius lokasi sudah menjawab risiko utama: QR yang terlihat dari luar kantor tidak otomatis bisa dipakai untuk check-in/check-out.
