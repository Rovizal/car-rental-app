# Car Rental App

Struktur:

- `/car-rental` → Laravel backend / frontend html+jquery
- `/car-rental-vue` → Frontend Vue 3 (additional)

Lanjutkan baca instruksi di bawah ini 👇

---

## 🚀 Fitur Utama

### Backend (Laravel 11)

- Listing mobil dengan server-side filter dan pagination
- Booking mobil dengan validasi tanggal dan perhitungan harga otomatis
- Notifikasi email saat status booking berubah (dengan queue dan Redis)
- Caching mobil tersedia menggunakan Redis
- Artisan command untuk rekap booking harian
- API untuk frontend jQuery & Vue.js (terintegrasi)
- Unit testing dengan PHPUnit

### Frontend jQuery (di dalam `/car-rental/public`)

- Tabel mobil dengan DataTables (server-side)
- Filter brand, harga, dan status
- Booking form dengan konfirmasi (localStorage)
- Styling menggunakan Bootstrap 5
- **Akses melalui:** `http://localhost:8000/index.html`

### Frontend Vite + Vue 3 _(dikelola terpisah di folder `/car-rental-vue`)_

- Integrasi API untuk list mobil dengan filter dan sorting
- Komponen reusable dengan PrimeVue DataTable
- **Jalankan dengan:**
  ```bash
  cd car-rental-vue
  npm install
  npm run dev
  ```
  Akses melalui: `http://localhost:5173`

---

## 🧱 Struktur API

### `GET /api/cars`

- Untuk jQuery DataTables dan Vue
- Mendukung: `brand`, `min_price`, `max_price`, `availability_status`, `search`, `page`, `per_page`, `sort_by`, `sort_order`

### `POST /api/bookings`

- Membuat booking baru
- Input: `user_id`, `car_id`, `start_date`, `end_date`

### `PATCH /api/bookings/{booking}/status`

- Update status booking (pending/confirmed/completed/canceled)
- Trigger notifikasi email via queue

### `GET /api/users/search`

- Dropdown user (Select2 / Autocomplete)

---

## ⚙️ Setup Lokal

### Persyaratan

- PHP 8.3
- Composer
- MySQL
- Redis
- Node.js (opsional untuk Vue)

### Pembuatan Database

Buat dua database secara manual sebelum menjalankan migrasi:

```sql
CREATE DATABASE car_rental;
CREATE DATABASE car_rental_test;
```

### Langkah Instalasi Backend

```bash
cd car-rental
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Queue Worker

```bash
php artisan queue:work
```

### Redis

Pastikan Redis aktif dan `.env`:

```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 🧪 Testing

```bash
php artisan test
```

- Booking: validasi, tumpang tindih, response
- Booking summary command
- Notifikasi email (mocked)

---

## 👤 Author

Rovizal — 2025

---

## 📁 Lisensi

Free to use for assessment purpose.
