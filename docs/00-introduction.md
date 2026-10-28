# Introduction

## Apa Itu LivewireDatatable?

LivewireDatatable adalah package modular untuk membuat **advanced datatable** di Laravel dengan **Livewire 4** dan **Flux UI 2**. Package ini dirancang untuk:

- ✅ **Modular** — aktifkan hanya fitur yang dibutuhkan via trait
- ✅ **Fluent API** — definisi kolom dengan chaining yang clean
- ✅ **Type-safe** — IDE autocomplete untuk semua method
- ✅ **Filament-inspired** — record actions yang mirip Filament
- ✅ **Production-ready** — support nested, actions, composite cells

## Kapan Menggunakan LivewireDatatable?

**Cocok untuk:**
- Admin panel dengan tabel data besar
- Dashboard dengan filter & sorting kompleks
- Report dengan nested relations (category → items)
- Data dengan action per row (edit, delete, dll)

**Tidak cocok untuk:**
- Tabel statis tanpa interaksi (pakai HTML biasa saja)
- Real-time collaboration (butuh Livewire poll + broadcast)
- Mobile-first UI (meski responsive, tetap lebih cocok untuk desktop)

## Filosofi Desain

### 1. Modular via Trait

Setiap fitur diisolasi dalam trait sendiri. User hanya meng-`use` trait yang diperlukan:

```php
class SimpleTable extends Component
{
    use HasColumns, HasSorting; // Hanya 2 fitur ini
}
```

---
Next: [Installation](01-installation.md)