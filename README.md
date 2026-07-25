# Beauty Clinic Management System

Laravel 13 + Vue 3 + Tailwind CSS | Phase 1 MVP

## Tech Stack
- **Backend:** Laravel 13, PHP 8.3, MySQL 8, JWT Auth (`php-open-source-saver/jwt-auth`)
- **Frontend:** Vue 3 (Composition API), Vite, Tailwind CSS v4, Pinia, Axios, Vue Router 4

## Local Setup

### Prerequisites
- PHP 8.3+
- Composer 2.x
- Node.js 20+ / npm 10+
- MySQL 8.0+ running locally

### 1. Backend (Laravel)

```bash
# Install PHP dependencies
composer install

# Copy env file and generate keys
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

# Create MySQL database (in MySQL client):
# CREATE DATABASE beauty_clinic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Run migrations
php artisan migrate

# Start Laravel server
php artisan serve
# → API available at http://localhost:8000/api/v1/
```

### 2. Frontend (Vue)

```bash
cd frontend
npm install
npm run dev
# → UI available at http://localhost:5173/
```

## Folder Structure

```
web klinik/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # AuthController, AppointmentController, ProductController, OrderController, AdminController, TreatmentController
│   │   ├── Middleware/      # CheckRole (RBAC)
│   │   └── Requests/        # Form validation classes (8 total)
│   ├── Models/              # User, Staff, Treatment, Appointment, Product, Order, OrderItem, LoyaltyPoint, Notification
│   └── Services/            # AppointmentService, OrderService, WhatsAppService
├── database/
│   └── migrations/          # 9 migration files
├── routes/
│   └── api.php              # Versioned /api/v1/ routes
├── frontend/
│   └── src/
│       ├── api/             # client.js (Axios)
│       ├── components/      # (next phase: shared components)
│       ├── pages/           # Home, Treatments, Products, Booking, Cart, Login, Register, Dashboard + admin/
│       ├── router/          # Vue Router with auth guards
│       └── stores/          # authStore, productStore, cartStore, appointmentStore, orderStore
├── .env.example
├── .gitignore
└── README.md
```

## API Endpoints (Base: `/api/v1/`)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/register` | Public | Customer register |
| POST | `/auth/login` | Public | Customer login |
| POST | `/auth/admin-login` | Public | Staff login |
| GET | `/treatments` | Public | List treatments |
| GET | `/products` | Public | List products |
| GET | `/appointments/available-slots` | Public | Get available slots |
| POST | `/appointments` | Public | Book appointment |
| POST | `/orders` | Public | Place order |
| GET | `/admin/dashboard` | Admin | Dashboard stats |
| GET | `/admin/appointments` | Admin Klinik | List appointments |
| GET | `/admin/orders` | Admin Produk | List orders |

## Default Admin Roles
- `owner` — Full access to all admin features
- `admin_klinik` — Appointments management
- `admin_produk` — Products & orders management

## Next Steps (Phase 1 Features)
1. Homepage UI with hero banner, featured products/treatments
2. Full appointment management UI (admin)
3. Full product CRUD UI (admin)
4. Full order management UI (admin)
5. Customer dashboard (appointment history, order history, loyalty points)
6. Navbar, Footer, Sidebar components
