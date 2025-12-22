# 🚀 PANDUAN LENGKAP SETUP SISTEM ABSENSI QR CODE
## PT. Arung Laut Nusantara

---

## 📋 DAFTAR ISI
1. [Requirements](#requirements)
2. [Instalasi Laravel](#instalasi-laravel)
3. [Konfigurasi Database](#konfigurasi-database)
4. [Setup File & Folder](#setup-file--folder)
5. [Migration & Seeding](#migration--seeding)
6. [Konfigurasi Email](#konfigurasi-email)
7. [Testing Sistem](#testing-sistem)
8. [Troubleshooting](#troubleshooting)

---

## ✅ REQUIREMENTS

Pastikan sistem Anda memiliki:
- PHP >= 8.2
- Composer >= 2.9
- MySQL >= 8.0
- Node.js & NPM (untuk asset compilation)
- Extension PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

---

## 📦 INSTALASI LARAVEL

### 1. Buat Project Laravel Baru
```bash
composer create-project laravel/laravel attendance-system "11.*"
cd attendance-system
```

### 2. Install Dependencies Tambahan
```bash
composer require simplesoftwareio/simple-qrcode
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

---

## 🗄️ KONFIGURASI DATABASE

### 1. Buat Database
Buka MySQL dan jalankan:
```sql
CREATE DATABASE attendance_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau import langsung file `attendance_system.sql` yang sudah saya buat.

### 2. Konfigurasi .env
Copy file `.env` yang saya berikan ke root project, atau edit manual:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_system
DB_USERNAME=root
DB_PASSWORD=

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yhnnasltnga07@gmail.com
MAIL_PASSWORD="sqil qwri rfar knlq"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yhnnasltnga07@gmail.com

# QR Code Settings
QR_CODE_EXPIRY_SECONDS=30
QR_CODE_BEFORE_MINUTES=30
QR_CODE_AFTER_MINUTES=45
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

---

## 📁 SETUP FILE & FOLDER

### 1. Struktur Folder yang Dibutuhkan

Buat folder-folder berikut jika belum ada:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── HRD/
│   │   └── Employee/
│   └── Middleware/
├── Models/
├── Mail/
└── Helpers/

database/
├── migrations/
└── seeders/

resources/
└── views/
    ├── auth/
    ├── admin/
    ├── hrd/
    ├── employee/
    ├── emails/
    └── layouts/

storage/
└── app/
    └── public/
        └── attachments/

public/
├── css/
├── js/
└── images/
```

### 2. Copy Semua File yang Sudah Saya Buat

#### **Migrations** (ke `database/migrations/`)
- `XXXX_XX_XX_000001_create_shifts_table.php`
- `XXXX_XX_XX_000002_create_users_table.php`
- `XXXX_XX_XX_000003_create_qr_codes_table.php`
- `XXXX_XX_XX_000004_create_attendances_table.php`
- `XXXX_XX_XX_000005_create_leave_requests_table.php`
- `XXXX_XX_XX_000006_create_activity_logs_table.php`
- `XXXX_XX_XX_000007_create_notifications_table.php`

**Catatan:** Ganti `XXXX_XX_XX` dengan format: `tahun_bulan_tanggal_urutan`
Contoh: `2025_01_15_000001_create_shifts_table.php`

#### **Seeders** (ke `database/seeders/`)
- `ShiftSeeder.php`
- `UserSeeder.php`
- `DatabaseSeeder.php`

#### **Models** (ke `app/Models/`)
- `User.php`
- `Shift.php`
- `QrCode.php`
- `Attendance.php`
- `LeaveRequest.php`
- `ActivityLog.php`
- `Notification.php`

#### **Middleware** (ke `app/Http/Middleware/`)
- `RoleMiddleware.php`
- `ActivityLogMiddleware.php`
- `CheckUserActive.php`

#### **Controllers** (ke folder masing-masing)
- `AuthController.php` → `app/Http/Controllers/`
- `QrCodeController.php` → `app/Http/Controllers/`
- `AttendanceController.php` → `app/Http/Controllers/`

#### **Mail** (ke `app/Mail/`)
- `AttendanceNotification.php`

#### **Helpers** (ke `app/Helpers/`)
- `helpers.php`

### 3. Register Helpers di composer.json

Edit file `composer.json`, tambahkan di section `autoload`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Helpers/helpers.php"
    ]
},
```

Lalu jalankan:
```bash
composer dump-autoload
```

### 4. Update bootstrap/app.php

Replace isi file `bootstrap/app.php` dengan yang sudah saya buat.

### 5. Update config/app.php

Tambahkan di `providers` (jika belum otomatis):
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    // ... providers lain
    SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
])->toArray(),
```

Tambahkan di `aliases`:
```php
'aliases' => Facade::defaultAliases()->merge([
    // ... aliases lain
    'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
    'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
])->toArray(),
```

---

## 🔄 MIGRATION & SEEDING

### 1. Jalankan Migrations
```bash
php artisan migrate
```

### 2. Jalankan Seeders
```bash
php artisan db:seed
```

Atau jalankan sekaligus:
```bash
php artisan migrate:fresh --seed
```

### 3. Verifikasi Data
Login ke MySQL dan cek:
```sql
USE attendance_system;

-- Cek shifts
SELECT * FROM shifts;

-- Cek users
SELECT id, name, email, role FROM users;
```

---

## 📧 KONFIGURASI EMAIL

### 1. Setup Gmail App Password

Sudah saya setup dengan email: `yhnnasltnga07@gmail.com`
App Password: `sqil qwri rfar knlq`

### 2. Test Email
Buat route test di `routes/web.php`:
```php
Route::get('/test-email', function() {
    try {
        Mail::raw('Test email dari sistem absensi', function($message) {
            $message->to('test@example.com')
                    ->subject('Test Email');
        });
        return 'Email berhasil dikirim!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
```

Akses: `http://localhost:8000/test-email`

---

## 🎯 TESTING SISTEM

### 1. Jalankan Development Server
```bash
php artisan serve
```

Akses: `http://localhost:8000`

### 2. Login Credentials Default

**Admin:**
- Email: `admin@arunglaut.com`
- Password: `admin123`

**HRD:**
- Email: `hrd@arunglaut.com`
- Password: `hrd123`

**Employee:**
- Email: `budi@arunglaut.com`
- Password: `employee123`

### 3. Test Flow

#### Test Admin:
1. Login sebagai Admin
2. Kelola data karyawan
3. Kelola shift
4. Generate QR Code
5. Monitor kehadiran

#### Test Employee:
1. Login sebagai Employee
2. Scan QR Code untuk check-in
3. Lihat status kehadiran hari ini
4. Scan QR Code untuk check-out
5. Lihat history kehadiran
6. Ajukan cuti

#### Test HRD:
1. Login sebagai HRD
2. Monitor semua kehadiran
3. Review pengajuan cuti
4. Approve/Reject cuti
5. Export laporan

---

## 🔧 TROUBLESHOOTING

### Error: "Class QrCode not found"
```bash
composer require simplesoftwareio/simple-qrcode
php artisan config:clear
php artisan cache:clear
```

### Error: "SQLSTATE[HY000] [1045]"
- Cek username/password MySQL di `.env`
- Pastikan MySQL service berjalan

### Error: "Failed to send email"
- Cek koneksi internet
- Verifikasi App Password Gmail
- Pastikan Less Secure Apps diaktifkan

### QR Code tidak generate
- Cek config QR_CODE_EXPIRY_SECONDS di `.env`
- Pastikan shift sudah dibuat
- Cek waktu sistem match dengan shift time

### Migration error: "Table already exists"
```bash
php artisan migrate:fresh --seed
```
**⚠️ Warning:** Ini akan menghapus semua data!

### Permission denied di storage/
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Error 500 setelah deploy
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## 📝 NEXT STEPS

### Yang Perlu Dibuat Selanjutnya:

1. **Controllers untuk Admin, HRD, Employee** (Dashboard & CRUD)
2. **Views (Blade Templates)** untuk semua halaman
3. **JavaScript** untuk QR Scanner & Auto-refresh
4. **CSS/Styling** dengan Bootstrap 5
5. **Export Functions** (Excel & PDF)
6. **Chart Integration** (Highcharts)

### Struktur Controller yang Perlu Dibuat:

**Admin Controllers:**
- `Admin/DashboardController.php`
- `Admin/EmployeeController.php`
- `Admin/ShiftController.php`

**HRD Controllers:**
- `HRD/DashboardController.php`
- `HRD/LeaveRequestController.php`
- `HRD/ReportController.php`

**Employee Controllers:**
- `Employee/DashboardController.php`
- `Employee/LeaveRequestController.php`

---

## 🎉 SELESAI!

Jika semua langkah di atas sudah dilakukan dengan benar, sistem dasar sudah siap digunakan.

**Untuk pertanyaan lebih lanjut:**
- Cek Laravel Documentation: https://laravel.com/docs
- Cek error log: `storage/logs/laravel.log`

**Good luck! 🚀**