# Car Rental App

Structure:

- `/car-rental` → Laravel backend / frontend html+jquery
- `/car-rental-vue` → Frontend Vue 3 (additional)

---

## Main Features

### Backend (Laravel 11)

- Car listings with server-side filtering and pagination
- Car booking with date validation and automatic price calculation
- Email notifications when booking status changes (via queue and Redis)
- Redis-based caching for available cars
- Artisan command for daily booking summary
- API integration for both jQuery and Vue.js frontends
- Unit testing with PHPUnit

### Frontend jQuery (inside `/car-rental/public`)

- Car table with server-side DataTables
- Filtering by brand, price range, and availability status
- Booking form with confirmation feature using `localStorage`
- Styled using Bootstrap 5
- **Access via:** `http://localhost:8000/index.html`

### Frontend Vite + Vue 3 _(in separate folder `/car-rental-vue`)_

- API integration to list cars with filters and sorting
- Reusable components using PrimeVue DataTable
- **Run with:**
  ```bash
  cd car-rental-vue
  npm install
  npm run dev
  ```
  Access via: `http://localhost:5173`

---

## API Structure

### `GET /api/cars`

- Used for jQuery DataTables and Vue
- Support: `brand`, `min_price`, `max_price`, `availability_status`, `search`, `page`, `per_page`, `sort_by`, `sort_order`

### `POST /api/bookings`

- Create a new booking
- Input: `user_id`, `car_id`, `start_date`, `end_date`

### `PATCH /api/bookings/{booking}/status`

- Update booking status (pending/confirmed/completed/canceled)
- Triggers email notification via queue

### `GET /api/users/search`

- Dropdown user (Select2 / Autocomplete)

---

## Local Setup

### Requirements

- PHP 8.3
- Composer
- MySQL
- Redis
- Node.js (optional for Vue)

### Create Databases

Manually create the following databases before running migrations:

```sql
CREATE DATABASE car_rental;
CREATE DATABASE car_rental_test;
```

### Backend Installation Steps

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

Ensure Redis is running and your .env file contains:

```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## Testing

```bash
php artisan test
```

- Booking: validations, conflict checking, and API response
- Daily booking summary command
- Email notification (mocked/tested)

---

## Author

Rovizal — 2025

---

## Lisensi

Free to use .
