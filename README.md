# Yohnna Project Attendance

Sistem absensi karyawan berbasis Laravel 12 dengan QR Code, multi-role `admin`, `hrd`, dan `employee`, dilengkapi dashboard analitik, pengajuan cuti, notifikasi, ekspor laporan, pengiriman email, serta auto background QR generation berdasarkan attendance window.

Dokumentasi ini disusun dalam urutan:

1. Cara menjalankan project dari awal di Windows 11
2. Semua workflow, alur, dan fungsionalitas sistem
3. Penjelasan teknis, pelajaran, teori, dan informasi lengkap tentang project dan kode

---

## 1. Cara Menjalankan Project Dari Awal

Bagian ini ditulis untuk environment berikut:

- Windows 11
- PHP 8.2
- XAMPP
- Laravel 12
- Node.js 20
- VS Code
- MySQL dari XAMPP

### 1.1. Persiapan awal

Pastikan `php`, `composer`, `node`, dan `npm` bisa dipanggil dari terminal:

```powershell
php -v
composer -V
node -v
npm -v
```

Target minimal:

- PHP `8.2.x`
- Node `20.x`

### 1.2. Clone project ke Documents

Buka PowerShell lalu masuk ke folder `Documents`:

```powershell
cd $HOME\Documents
```

Clone project ke folder bernama `YohnnaProjectAttendance`:

```powershell
git clone <repository-url> YohnnaProjectAttendance
```

Hasil akhirnya idealnya ada di:

```text
C:\Users\<nama-user>\Documents\YohnnaProjectAttendance
```

### 1.3. Buka di VS Code

Masuk ke folder project:

```powershell
cd $HOME\Documents\YohnnaProjectAttendance
```

Lalu buka VS Code:

```powershell
code .
```

### 1.4. Install dependency backend dan frontend

Backend:

```powershell
composer install
```

Frontend:

```powershell
npm install
```

### 1.5. Buat file `.env`

PowerShell:

```powershell
Copy-Item .env.example .env
```

CMD:

```cmd
copy .env.example .env
```

### 1.6. Atur `.env`

Sesuaikan minimal konfigurasi berikut:

```env
APP_NAME="YohnnaProjectAttendance"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yohnna_project_attendance
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

QR_CODE_EXPIRY_SECONDS=30
QR_CODE_BEFORE_MINUTES=30
QR_CODE_AFTER_MINUTES=45
DEFAULT_LATE_TOLERANCE=15
WORKING_HOURS_MINIMUM=8

ATTENDANCE_LOCATION_ENABLED=false
ATTENDANCE_LOCATION_LATITUDE=
ATTENDANCE_LOCATION_LONGITUDE=
ATTENDANCE_LOCATION_RADIUS_METERS=100
ATTENDANCE_LOCATION_MAX_ACCURACY_METERS=150
ATTENDANCE_LOCATION_LABEL="Lokasi kantor"
```

Buat database MySQL terlebih dahulu:

```sql
CREATE DATABASE yohnna_project_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 1.7. Generate application key

```powershell
php artisan key:generate
```

### 1.8. Migration dan seed

Untuk setup baru dari nol:

```powershell
php artisan migrate:fresh --seed
```

Kalau muncul konfirmasi:

```text
Are you sure you want to run this command? (yes/no)
```

ketik:

```text
yes
```

Kalau tidak ingin menghapus data lama, gunakan:

```powershell
php artisan migrate
php artisan db:seed
```

### 1.9. Buat storage link

```powershell
php artisan storage:link
```

### 1.10. Clear cache

```powershell
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 1.11. Jalankan project

Cara paling praktis:

```powershell
composer run dev
```

Script ini akan menjalankan:

- `php artisan serve`
- `php artisan queue:listen --tries=1`
- `php artisan pail --timeout=0`
- `php artisan schedule:work`
- `npm run dev`

Kalau mau manual, jalankan di terminal terpisah:

```powershell
php artisan serve
```

```powershell
npm run dev
```

```powershell
php artisan schedule:work
```

Opsional:

```powershell
php artisan queue:listen --tries=1
```

### 1.12. Akses aplikasi

Buka:

```text
http://127.0.0.1:8000
```

### 1.13. Akun bawaan hasil seeder

Admin:

- Email: `admin@arunglaut.com`
- Password: `admin123`

HRD:

- Email: `hrd@arunglaut.com`
- Password: `hrd123`

Employee:

- `budi@arunglaut.com` / `employee123`
- `siti@arunglaut.com` / `employee123`
- `ahmad@arunglaut.com` / `employee123`

### 1.14. Jam attendance window default

Shift hasil seed:

- `Shift Pagi`: `08:00 - 16:00`
- `Shift Siang`: `14:00 - 22:00`
- `Shift Malam`: `22:00 - 06:00`

Dengan config default:

- `QR_CODE_BEFORE_MINUTES=30`
- `QR_CODE_AFTER_MINUTES=45`

Maka window menjadi:

- `Shift Pagi` check-in: `07:30 - 08:45`
- `Shift Pagi` check-out: `15:30 - 16:45`
- `Shift Siang` check-in: `13:30 - 14:45`
- `Shift Siang` check-out: `21:30 - 22:45`
- `Shift Malam` check-in: `21:30 - 22:45`
- `Shift Malam` check-out: `05:30 - 06:45`

### 1.15. Cara cek background QR scheduler

```powershell
php artisan schedule:list
```

Kalau muncul task:

```text
php artisan attendance:generate-active-qr --quiet-output
```

berarti scheduler QR sudah terdaftar.

Kalau output command menunjukkan:

```text
QR aktif dipastikan untuk 0 window.
```

artinya pada saat command dijalankan memang belum ada window absensi yang terbuka.

### 1.16. Troubleshooting singkat

Jika route atau tampilan terasa aneh:

```powershell
php artisan optimize:clear
```

Jika attachment tidak tampil:

```powershell
php artisan storage:link
```

Jika QR tidak otomatis muncul saat window terbuka:

```powershell
php artisan schedule:work
```

atau gunakan:

```powershell
composer run dev
```

---

## 2. Semua Workflow, Alur, dan Fungsionalitas Sistem

Project ini memiliki 3 role:

- `admin`
- `hrd`
- `employee`

Setiap role memiliki dashboard, menu, dan hak akses yang berbeda.

### 2.1. Workflow login

Alur login:

1. User membuka `/`
2. Jika belum login, tampil halaman login
3. User memasukkan email dan password
4. Sistem memvalidasi akun
5. Sistem mengecek `is_active`
6. User diarahkan ke dashboard sesuai role

Redirect role:

- `admin` ke `/admin/dashboard`
- `hrd` ke `/hrd/dashboard`
- `employee` ke `/employee/dashboard`

### 2.2. Konsep absensi QR

Sistem ini memakai QR Code per shift, bukan QR pribadi per karyawan.

Artinya:

- QR dibuat untuk suatu shift
- QR dibedakan menjadi `check_in` dan `check_out`
- karyawan tetap diidentifikasi dari akun login
- QR berfungsi sebagai token absensi sementara untuk shift terkait
- proses absensi dapat dikunci ke radius lokasi kantor melalui validasi geolocation

### 2.2.1. Validasi lokasi absensi

Untuk menjawab revisi sidang tentang potensi QR dipakai dari rumah atau lokasi lain, sistem sekarang mendukung location guard.

Saat employee melakukan check-in/check-out, browser mengirim koordinat perangkat ke server. Server menghitung jarak koordinat tersebut dari titik absensi yang dikonfigurasi di `.env`. Jika perangkat berada di luar radius yang diizinkan, absensi ditolak walaupun QR masih aktif dan sesuai shift.

Dokumentasi detail revisi ini ada di:

```text
docs/REVISION_LOCATION_QR_ATTENDANCE.md
```

### 2.3. Workflow check-in

1. Sistem membuka attendance window check-in
2. Scheduler menjaga QR check-in aktif selama window terbuka
3. Employee login dan membuka scanner
4. Employee scan QR
5. Sistem mengecek:
   - QR ada
   - QR aktif
   - QR belum expired
   - QR cocok dengan shift employee
   - QR masih dalam attendance window
   - lokasi perangkat berada dalam radius absensi yang diizinkan
6. Sistem menyimpan `check_in`
7. Sistem menentukan status `on_time` atau `late`
8. Sistem membuat activity log
9. Sistem membuat notifikasi dan mencoba kirim email

### 2.4. Workflow check-out

1. Sistem membuka attendance window check-out
2. Scheduler menjaga QR check-out aktif
3. Employee scan QR check-out
4. Sistem mencari attendance yang masih terbuka
5. Sistem memastikan employee sudah check-in
6. Sistem memastikan lokasi perangkat berada dalam radius absensi yang diizinkan
7. Sistem menyimpan `check_out`
8. Sistem menghitung `total_hours`
9. Sistem memperbarui status attendance
10. Sistem membuat log dan notifikasi

### 2.5. Workflow admin

Admin bisa:

- melihat dashboard operasional
- melihat QR aktif
- melihat statistik hadir hari ini
- mengelola data karyawan
- melihat detail karyawan
- export employee ke Excel
- mengelola shift
- mengelola attendance
- force add attendance
- update attendance
- membuka halaman QR Code

### 2.6. Workflow employee

Employee bisa:

- melihat dashboard pribadi
- scan QR attendance
- melihat status attendance hari ini
- melihat riwayat attendance
- filter riwayat berdasarkan bulan, tahun, status
- mengatur jumlah data per halaman
- membuat pengajuan cuti
- upload lampiran cuti
- melihat notifikasi
- melihat profil sendiri

### 2.7. Workflow HRD

HRD bisa:

- melihat dashboard monitoring
- melihat laporan attendance
- filter laporan attendance
- export laporan ke Excel
- export laporan ke PDF
- menambah catatan attendance
- melihat daftar pengajuan cuti
- approve cuti
- reject cuti
- memberi review notes
- melihat halaman statistik

### 2.8. Workflow pengajuan cuti

Employee:

1. Membuka form cuti
2. Memilih jenis cuti
3. Mengisi tanggal dan alasan
4. Mengunggah lampiran jika perlu
5. Sistem menyimpan status `pending`
6. HRD menerima notifikasi

HRD:

1. Membuka daftar pengajuan cuti
2. Membaca detail pengajuan
3. Menyetujui atau menolak
4. Menambahkan catatan review
5. Employee menerima notifikasi dan email

### 2.9. Dashboard yang tersedia

Dashboard Admin menampilkan:

- total employee
- employee aktif dan nonaktif
- total shift
- attendance hari ini
- on-time, late, incomplete, absent
- tren mingguan
- breakdown department
- breakdown shift
- recent activities
- active QR codes

Dashboard Employee menampilkan:

- attendance hari ini
- statistik bulanan pribadi
- recent attendance
- notifikasi terbaru
- pending leave
- upcoming leave
- tren hadir dan jam kerja

Dashboard HRD menampilkan:

- total employee aktif
- hadir hari ini
- tidak hadir
- terlambat
- incomplete
- pending leave
- statistik bulanan
- chart mingguan
- department breakdown
- daftar keterlambatan tertinggi

### 2.10. Fitur performa untuk data besar

Untuk mendukung data `10000+`, project ini sudah menggunakan:

- server-side pagination
- filter berbasis query database
- parameter `per_page`
- pencarian data
- tabel yang tidak memuat semua record sekaligus

---

## 3. Penjelasan Teknis, Pelajaran, Ilmu, dan Informasi Lengkap Tentang Project

### 3.1. Stack teknologi

Backend:

- PHP 8.2
- Laravel 12
- Eloquent ORM
- Laravel Scheduler
- Laravel Mail

Frontend:

- Blade
- Bootstrap 5 style ecosystem
- Bootstrap Icons
- Chart.js
- jQuery/AJAX
- Vite

Package tambahan:

- `simplesoftwareio/simple-qrcode`
- `maatwebsite/excel`
- `barryvdh/laravel-dompdf`

### 3.2. Arsitektur aplikasi

Project ini mengikuti pola MVC:

- `Model` untuk data dan relasi database
- `View` untuk Blade
- `Controller` untuk request, validasi, query, dan response

Di luar MVC, project ini juga memakai:

- `Middleware`
- `Service`
- `Helper`
- `Mail`
- `Export`

### 3.3. Struktur folder penting

Controller utama:

- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/EmployeeController.php`
- `app/Http/Controllers/HrdController.php`
- `app/Http/Controllers/AttendanceController.php`
- `app/Http/Controllers/QrCodeController.php`

Model utama:

- `app/Models/User.php`
- `app/Models/Shift.php`
- `app/Models/QrCode.php`
- `app/Models/Attendance.php`
- `app/Models/LeaveRequest.php`
- `app/Models/Notification.php`
- `app/Models/ActivityLog.php`

Service:

- `app/Services/QrCodeService.php`

Helper:

- `app/Helpers/helpers.php`

Mail:

- `app/Mail/AttendanceNotification.php`
- `app/Mail/LeaveRequestNotification.php`

Export:

- `app/Exports/EmployeesExport.php`
- `app/Exports/AttendanceExport.php`

Route:

- `routes/web.php`
- `routes/console.php`

View:

- `resources/views/layouts/`
- `resources/views/admin/`
- `resources/views/employee/`
- `resources/views/hrd/`

Config:

- `config/attendance.php`

### 3.4. Penjelasan model inti

`User` menyimpan akun, role, identitas employee, shift, dan status aktif.

`Shift` menyimpan nama shift, jam mulai, jam selesai, toleransi telat, dan status aktif. Model ini juga mendukung overnight shift seperti `22:00 - 06:00`.

`QrCode` menyimpan token QR yang aktif sementara untuk suatu shift dan type tertentu.

`Attendance` menyimpan data kehadiran harian: tanggal, check-in, check-out, total jam kerja, status, dan relasi ke QR.

`LeaveRequest` menyimpan pengajuan cuti, lampiran, reviewer, dan hasil review.

`Notification` menyimpan notifikasi internal aplikasi.

`ActivityLog` menyimpan jejak aktivitas penting sebagai audit ringan.

### 3.5. Penjelasan route dan middleware

Route utama ada di `routes/web.php`.

Struktur route:

- guest route untuk login
- auth route untuk user yang sudah login
- subgroup admin
- subgroup employee
- subgroup hrd
- route attendance umum
- route QR API

Middleware yang dipakai:

- `auth`
- `guest`
- `role`
- `user.active`

Pelajaran penting:

- pembatasan akses harus dilakukan di backend
- route group membuat URL dan permission lebih rapi
- named route membuat redirect dan link lebih stabil

### 3.6. Penjelasan controller

`AuthController` menangani login, logout, ganti password, dan redirect role.

`AdminController` menangani dashboard admin, CRUD employee, CRUD shift, attendance management, dan halaman QR.

`EmployeeController` menangani dashboard employee, scanner, history attendance, cuti, notifikasi, dan profil.

`HrdController` menangani dashboard HRD, report attendance, review cuti, export, notes attendance, dan statistik.

`AttendanceController` menangani proses scan QR, check-in, check-out, attendance hari ini, dan notifikasi attendance.

`QrCodeController` menangani generate QR manual, auto-generate QR, validasi QR, ambil QR aktif, dan halaman QR admin.

### 3.7. Penjelasan service `QrCodeService`

File:

- `app/Services/QrCodeService.php`

Service ini dipakai untuk logika background QR:

- `ensureCurrentWindowQRCodes()`
- `regenerateForShift()`
- `ensureActiveCode()`
- `createCode()`

Fungsinya:

- mengecek shift aktif
- mengecek window `check_in` dan `check_out`
- mematikan QR aktif yang sudah di luar window
- menjaga agar selama window terbuka ada QR aktif yang valid

Alasan memakai service:

- logic tidak tercecer di controller
- lebih mudah dirawat
- lebih mudah dikembangkan

### 3.8. Penjelasan helper global

File:

- `app/Helpers/helpers.php`

Contoh helper penting:

- `formatDate()`
- `formatTime()`
- `formatDateTime()`
- `getStatusBadge()`
- `getStatusText()`
- `getLeaveTypeText()`
- `getLeaveStatusText()`
- `getRoleText()`
- `calculateWorkingDays()`
- `isWithinAttendanceWindow()`
- `getAttendanceWindow()`
- `attendancePercentage()`
- `attendanceStatusIcon()`

Helper cocok untuk fungsi utilitas kecil yang sering dipakai berulang.

### 3.9. Attendance window dan status

Konsep penting project ini adalah attendance window.

`check_in` memakai target `shift->start_time`.

`check_out` memakai target `shift->end_time`.

Window default:

- 30 menit sebelum jam target
- 45 menit sesudah jam target

Status utama attendance:

- `on_time`
- `late`
- `incomplete`
- `absent`

Maknanya:

- `on_time`: check-in masih dalam batas toleransi
- `late`: check-in lewat dari toleransi
- `incomplete`: sudah check-in tetapi belum checkout lengkap
- `absent`: tidak hadir

### 3.10. Overnight shift

Project ini mendukung overnight shift seperti:

```text
22:00 - 06:00
```

Masalah umum overnight shift:

- checkout terlihat lebih awal dari check-in
- total jam kerja salah
- record attendance sulit dicari jika hanya melihat tanggal hari ini

Perbaikan yang dipakai:

- shift malam diizinkan
- kalkulasi jam kerja memakai penyesuaian tanggal
- lookup checkout memakai attendance terbuka yang masih relevan

### 3.11. Scheduler dan command

Scheduler ada di:

- `routes/console.php`

Command ada di:

- `app/Console/Commands/GenerateActiveQrCodesCommand.php`

Command yang dipakai:

```powershell
php artisan attendance:generate-active-qr
```

Scheduler menjalankannya tiap 10 detik agar QR aktif selalu tersedia saat window terbuka.

Pelajaran penting:

- fitur berbasis waktu cocok memakai `command + schedule`
- di lokal butuh `php artisan schedule:work`
- di server butuh scheduler environment yang berjalan stabil

### 3.12. Query, filter, pagination, dan skalabilitas

Beberapa halaman besar memakai pola:

- filter dari request
- `when()` untuk query dinamis
- `paginate($perPage)`
- `withQueryString()`

Contoh manfaat:

- data tidak dimuat sekaligus
- filter tetap terbawa saat pindah halaman
- lebih aman untuk data besar

Ini penting untuk skenario `10000+` row.

### 3.13. Email system

Project memakai Laravel Mail untuk:

- notifikasi check-in/check-out
- notifikasi approve/reject cuti

Class mail:

- `AttendanceNotification`
- `LeaveRequestNotification`

Pola yang dipakai:

1. event utama terjadi
2. notifikasi database dibuat
3. sistem mencoba kirim email
4. jika email gagal, error dicatat dan flow utama tidak langsung rusak

### 3.14. Export Excel dan PDF

Package:

- `maatwebsite/excel`
- `barryvdh/laravel-dompdf`

Class:

- `EmployeesExport`
- `AttendanceExport`

HRD dapat ekspor attendance ke Excel dan PDF. Admin dapat ekspor employee ke Excel.

### 3.15. Syntax Laravel yang sering muncul

Validasi request:

```php
$validated = $request->validate([
    'email' => ['required', 'email'],
    'password' => ['required'],
]);
```

Query kondisional:

```php
$query->when($request->filled('status'), function ($builder) use ($request) {
    $builder->where('status', $request->status);
});
```

Pagination:

```php
$items = $query->paginate($perPage)->withQueryString();
```

Eager loading:

```php
Attendance::with(['user', 'shift'])->get();
```

Update atau create:

```php
Attendance::updateOrCreate(
    ['user_id' => $userId, 'date' => $date],
    $data
);
```

Redirect ke route bernama:

```php
return redirect()->route('admin.dashboard');
```

### 3.16. Penjelasan desain UI

Refactor UI project ini mengikuti pendekatan layout referensi yang lebih modern lalu disesuaikan dengan identitas visual Yohnna:

- soft warm coral
- dusty salmon
- warm reddish-pink
- calm dan sedikit vintage

Prinsip desain yang dipakai:

- shell layout konsisten
- dashboard informatif
- kartu statistik lebih jelas
- tabel siap data besar
- filter dan `per_page` tersedia di halaman penting

### 3.17. Seeder dan data awal

Seeder utama:

- `database/seeders/ShiftSeeder.php`
- `database/seeders/UserSeeder.php`
- `database/seeders/DatabaseSeeder.php`

Data awal yang disediakan:

- 3 shift
- 1 admin
- 1 HRD
- 3 employee

Seeder membantu project langsung bisa dites setelah `migrate:fresh --seed`.

### 3.18. Maintenance dan hal penting yang perlu dijaga

Kalau project ini dilanjutkan lagi nanti, hal-hal yang penting dijaga:

- konsistensi route dan middleware
- akurasi attendance window
- perhitungan overnight shift
- performa tabel saat data besar
- keamanan kredensial di `.env`
- kestabilan scheduler
- validasi cuti dan lampiran
- konsistensi status attendance

### 3.19. Pelajaran besar dari project ini

Pelajaran yang paling terasa dari project ini:

- aplikasi absensi harus jelas soal waktu, shift, dan status
- QR lebih tepat dipahami sebagai token shift, bukan identitas employee
- dashboard harus membantu keputusan, bukan hanya cantik
- pagination dan filter wajib untuk data besar
- scheduler penting untuk fitur yang time-based
- shift malam selalu membutuhkan perhatian khusus

### 3.20. File yang paling sering disentuh

- `routes/web.php`
- `routes/console.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/EmployeeController.php`
- `app/Http/Controllers/HrdController.php`
- `app/Http/Controllers/AttendanceController.php`
- `app/Http/Controllers/QrCodeController.php`
- `app/Services/QrCodeService.php`
- `app/Helpers/helpers.php`
- `app/Models/Attendance.php`
- `app/Models/QrCode.php`
- `app/Models/Shift.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/admin/`
- `resources/views/employee/`
- `resources/views/hrd/`
- `config/attendance.php`
- `composer.json`

---

## Penutup

Project ini sekarang sudah jauh lebih siap dipakai:

- setup lebih jelas
- workflow absensi lebih masuk akal
- QR bisa dijaga aktif lewat background scheduler
- dashboard lebih kaya informasi
- tabel lebih siap untuk data besar
- dokumentasi operasional dan teknis sudah lebih rapi

README ini bisa dipakai sebagai pegangan untuk setup, testing, demo, maintenance, dan pengembangan lanjutan.
