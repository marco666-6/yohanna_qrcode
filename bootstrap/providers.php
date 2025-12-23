<?php

// composer require simplesoftwareio/simple-qrcode
// composer require maatwebsite/excel
// composer require barryvdh/laravel-dompdf

return [
    App\Providers\AppServiceProvider::class,
    SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
];