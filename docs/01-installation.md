# Installation

## Requirements

| Package | Version |
|---|---|
| PHP | ^8.2 |
| Laravel | ^11.0 atau ^12.0 |
| Livewire | ^4.0 |
| Flux UI | ^2.0 |
| Tailwind CSS | ^4.0 |

## Step 1: Install via Composer

```bash
composer require kataknyulink/livewire-datatable
```

## Step 2: Publish Config (Opsional)

```bash
php artisan vendor:publish --tag=datatable-config
```

Ini akan men-copy ```config/datatable.php``` ke aplikasi Anda.

## Step 3: Publish Views (Opsional)
Jika ingin customize Blade views:
```bash
php artisan vendor:publish --tag=datatable-views
```
View akan tersedia di ```resources/views/kataknyulink/datatable/```.

### Step 4: Setup Flux UI 2 + Tailwind 4
## 4.1 Install Flux UI (Jika Belum)
Ikuti [panduan instalasi Flux UI](https://fluxui.dev).

## 4.2 Update resources/css/app.css
```css
@import "tailwindcss";
@import '../../vendor/livewire/flux/dist/flux.css';

@custom-variant dark (&:where(.dark, .dark *));
```

## 4.3 Update Layout Utama
```resources/views/components/layouts/app.blade.php```:
```HTML
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'MyDatatable' }}</title>

    @fluxAppearance
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white dark:bg-zinc-900">
    {{ $slot }}
</body>
</html>
```

### Step 5: Build Assets
```bash
npm run dev   # atau npm run build untuk production
```
### Step 6: Clear Cache
```bash
php artisan optimize:clear
php artisan view:clear
```
Verifikasi Instalasi
Buat test component sederhana:

```bash
php artisan make:livewire TestTable
```
Edit ```app/Livewire/TestTable.php```:

```php
<?php

namespace App\Livewire;

use App\Models\User;
use KatakNyulink\LivewireDatatable\Livewire\DataTable;
use KatakNyulink\LivewireDatatable\Support\Column;

class TestTable extends DataTable
{
    public string $model = User::class;

    protected function columns(): array
    {
        return [
            Column::make('id')->label('#'),
            Column::make('name')->label('Nama')->sortable(),
        ];
    }
}
```

Render di blade:

```blade
<livewire:test-table />
```

Jika muncul tabel tanpa error, instalasi berhasil! ✅

Troubleshooting Instalasi
Error: Class 'KatakNyulink\LivewireDatatable\Livewire\DataTable' not found
```bash
composer dump-autoload
```
Error: View [datatable::livewire.data-table] not found
Cek ```composer.json``` aplikasi Anda:

```json
"extra": {
    "laravel": {
        "dont-discover": []
    }
}
```
Jika Anda pakai ```dont-discover```, tambahkan manual di ```config/app.php```:

```php
'providers' => [
    Vendor\MyDatatable\MyDatatableServiceProvider::class,
],
```
Error: Tailwind tidak mendeteksi class Flux
Pastikan ```resources/css/app.css``` import ```flux.css```. Lihat Step 4.2.

### Dark mode tidak bekerja
Pastikan ```@fluxAppearance``` ada di ```<head>```.

Assets tidak ter-build
```bash
npm run dev
```
Biarkan terminal tetap berjalan selama development.

---

Next: [Quick Start →](02-quick-start.md)