# Beauty Clinic Management System - PRD

## Project Overview
**Client:** Beauty Clinic (Kecantikan)
**Timeline:** Phase 1: 2-3 minggu (MVP)
**Target Launch:** Demo to client dalam 3-4 minggu
**Tech Stack:** Laravel + Vue.js + MySQL + Tailwind CSS (Mobile-first, fully responsive)

---

## 1. DATABASE SCHEMA SUMMARY

### Core Tables:

#### USERS (Customers)
```
- id (PK)
- name (required)
- phone (UK, required) - untuk WhatsApp
- email (UK)
- password_hash
- address
- role = 'customer' (default)
- loyalty_points (default 0)
- created_at
- last_login
```

#### STAFF (Admin/Owner)
```
- id (PK)
- name (required)
- email (UK, required)
- phone
- password_hash
- role (enum: 'owner' | 'admin_klinik' | 'admin_produk')
- is_active (boolean)
- 2FA_enabled (boolean)
- created_at
```

#### TREATMENTS
```
- id (PK)
- name (e.g., "Facial Treatment", "Skincare Consultation")
- description (detailed benefits)
- price (currency)
- duration_minutes (flexible, e.g., 30, 45, 60, 90)
- category
- benefits (text)
- image_url
- is_active (boolean)
- created_at
```

#### APPOINTMENTS
```
- id (PK)
- user_id (FK) - can be NULL jika walk-in customer
- staff_id (FK) - doctor/therapist yang handle
- treatment_id (FK)
- appointment_date (datetime)
- status (enum: pending | confirmed | completed | cancelled | no_show)
- customer_name (required)
- customer_phone (required, untuk follow-up)
- customer_concern (text - keluhan/kebutuhan)
- notes (admin notes)
- cancelled_at (timestamp jika cancelled)
- cancellation_reason (text)
- created_at
```

#### PRODUCTS
```
- id (PK)
- name
- description (long text)
- price (currency)
- category (enum: serum | sunscreen | moisturizer | cleanser | acne_treatment | mask | body_care | soap)
- stock_quantity (integer, auto-managed)
- image_url (single image)
- ingredients (text)
- volume/size (string, e.g., "50ml", "100ml") - optional
- views (tracking)
- is_active (boolean)
- created_at
- updated_at
```

#### ORDERS
```
- id (PK)
- order_number (UK, auto-generated)
- user_id (FK) - can be NULL jika anonymous
- total_price (currency)
- status (enum: pending | completed | cancelled)
- payment_status (enum: pending | cash_on_delivery)
- order_date (datetime)
- pickup_date (datetime, setelah diambil)
- notes (delivery/pickup notes)
- created_at
```

#### ORDER_ITEMS
```
- id (PK)
- order_id (FK)
- product_id (FK)
- quantity (integer)
- price_at_purchase (currency - untuk history)
```

#### LOYALTY_POINTS
```
- id (PK)
- user_id (FK)
- points_earned (integer)
- total_points (integer, cumulative)
- source (enum: appointment | product_purchase)
- created_at
```

#### NOTIFICATIONS
```
- id (PK)
- staff_id (FK) - untuk admin
- title
- message (text)
- type (enum: appointment | order | system)
- is_read (boolean)
- created_at
```

---

## 2. PAGES & SITE STRUCTURE

### PUBLIC PAGES (No Login Required)

#### 1. **Homepage** (`/`)
Components:
- Navigation bar (Logo, Menu, CTA Login/Book)
- Hero Banner (Promotional image + CTA)
- "Why Choose Us" section (3-5 benefits)
- Featured Products carousel (4-6 best-sellers)
- Featured Treatments section (show 3-4 popular services)
- Testimonials/Reviews section
- Newsletter signup form (optional Phase 2)
- Footer (contact info, social media, links)

#### 2. **Treatments/Services Page** (`/treatments`)
- List all treatments
- Filter by category/type
- Each treatment card shows:
  - Treatment name
  - Description + benefits
  - Duration
  - Price
  - "Book Now" CTA button
- Detail modal/page when clicked

#### 3. **Products Shop** (`/products`)
- Grid view (3 columns on desktop, 2 on tablet, 1 on mobile)
- Product filtering:
  - By category (dropdown or pills)
  - By price range (slider)
- Product card shows:
  - Product image
  - Name
  - Price
  - Rating/reviews count
  - "Add to Cart" button
- Product detail page (`/products/:id`):
  - Large image
  - Full description + ingredients
  - Price
  - Stock status
  - Add to cart with quantity selector

#### 4. **Shopping Cart** (`/cart`)
- List items with quantity controls
- Subtotal per item
- Total price
- "Checkout" button → WhatsApp order link
- "Continue Shopping" button

#### 5. **Booking Page** (`/booking`)
- Step 1: Select treatment (or show doctor/staff dropdown jika ada multiple)
- Step 2: Select appointment date & time
  - Calendar picker (max 7 hari ke depan)
  - Available time slots (auto-generate dari duration_minutes)
- Step 3: Enter customer details
  - Name (required)
  - Phone number (required, untuk WhatsApp)
  - Concern/keluhan (optional)
  - Email (optional)
- Step 4: Confirmation
  - Admin akan follow-up via WhatsApp
- Success message dengan order number

#### 6. **About Page** (`/about`)
- Clinic history
- Team gallery
- Before-after showcase
- Why choose us section

#### 7. **Contact/Follow-up** (`/contact`)
- Social media links (Instagram, WhatsApp, TikTok)
- Direct WhatsApp link
- Email form (optional)

---

### AUTHENTICATED PAGES (Customer Login Required)

#### 8. **Customer Dashboard** (`/dashboard`)
- Sidebar/Menu (Profile, History, Loyalty Points, Logout)
- **Profile Section**:
  - Name, phone, email, address
  - Edit button
  - Change password button
  - Notification preferences toggle
- **Appointment History**:
  - List of past & upcoming appointments
  - Status badge (completed, upcoming, cancelled)
  - Option to reschedule/cancel (jika minimal 2 jam sebelum jadwal)
- **Order History**:
  - List of orders
  - Status (pending, completed, cancelled)
  - Items detail
  - Download receipt (optional Phase 2)
- **Loyalty Points**:
  - Total points balance
  - Points history log
  - Redemption options (TBD)

---

### ADMIN PAGES (Role-based Access)

#### 9. **Admin Login** (`/admin/login`)
- Email + password + 2FA code
- "Forgot password" link

#### 10. **Admin Dashboard** (`/admin/dashboard`)
**Accessible to:** Owner, Admin Klinik, Admin Produk

Widgets:
- **Summary Cards** (show different data per role):
  - Owner: Total revenue (appointment + products), Total bookings, Total orders, Active customers
  - Admin Klinik: Today's appointments count, Pending confirmations, No-show count
  - Admin Produk: Total inventory value, Low stock alerts, Total orders pending
- **Charts** (Phase 2):
  - Appointment volume trend (weekly)
  - Revenue trend
  - Top products

#### 11. **Appointments Management** (`/admin/appointments`)
**Accessible to:** Owner, Admin Klinik

Features:
- **Calendar view** showing all appointments (color-coded by status)
  - Switch between calendar, list, and day view
- **List view**:
  - Filter by status, date range, treatment type
  - Columns: Date, Customer name, Phone, Treatment, Status, Actions
  - Action buttons: View details, Mark as completed, Mark as no-show, Edit notes
- **Add appointment** (admin dapat langsung input appointment):
  - Treat customer info, treatment, date/time, notes
- **Edit appointment**:
  - Change date/time jika belum confirmed
  - Update customer info
  - Add/edit notes
- **Notifications**:
  - Upcoming appointments (hari ini, besok)
  - Pending confirmations (yang perlu follow-up WhatsApp)

#### 12. **Products Management** (`/admin/products`)
**Accessible to:** Admin Produk

Features:
- **Product list**:
  - Table view: Name, Category, Price, Stock, Status, Actions
  - Filter by category, stock level
  - Search by name
- **Add new product**:
  - Form: Name, Description, Category, Price, Stock, Image upload, Ingredients
  - Submit button
- **Edit product**:
  - Same form, pre-filled
  - Update stock manually (jika perlu recount)
  - Deactivate product (soft delete)
- **Stock management**:
  - Real-time stock display
  - Alert when stock < 5 units
  - Manual stock adjustment (jika ada retur/damage)

#### 13. **Orders Management** (`/admin/orders`)
**Accessible to:** Owner, Admin Produk

Features:
- **Order list**:
  - Columns: Order #, Customer name/phone, Items, Total, Status, Date, Actions
  - Filter by status (pending, completed, cancelled)
  - Filter by date range
  - Search by order number or customer phone
- **Order detail**:
  - Customer info + phone
  - Items list (product name, quantity, price)
  - Total amount
  - Status (pending → completed → cancelled)
  - Update status button (Completed, Cancelled)
  - Send WhatsApp reminder (jika pending)
  - Notes/special requests

#### 14. **Settings** (`/admin/settings`)
**Accessible to:** Owner

Features:
- **Clinic Information**:
  - Name, address, phone, email
  - Hours of operation (open-close time)
  - Booking cutoff time (max 7 hari)
  - Appointment duration default (untuk treatments baru)
- **Business Settings**:
  - Currency
  - Tax settings (optional)
- **Staff Management**:
  - List of staff members (with role, email, is_active)
  - Add new staff (email, password, role)
  - Edit staff (change role, deactivate)
  - Delete staff (soft delete)
- **Backup & Export** (nice-to-have Phase 2):
  - Export appointments (CSV)
  - Export orders (CSV)
  - Database backup option

#### 15. **Staff List** (`/admin/staff`) - Owner only
- View all staff
- Add/edit/delete staff
- Role management

#### 16. **Reports** (`/admin/reports`) - Owner only
- Sales report (appointment revenue + product revenue)
- Customer report (total customers, repeat rate)
- Product report (best sellers, low stock)
- Export to PDF/CSV (Phase 2)

---

## 3. CORE FEATURES BREAKDOWN

### Feature 1: Appointment Booking
**Flow:**
1. Customer select treatment → see available slots (auto-generated dari duration_minutes)
2. Pick date (max 7 hari) & time
3. Enter name, phone, concern
4. Submit → confirmation
5. Admin dapat notifikasi
6. Admin follow-up via WhatsApp

**Database Operations:**
- Insert into APPOINTMENTS (user_id dapat null jika anonymous)
- Create NOTIFICATION untuk admin

---

### Feature 2: Product Catalog & Shopping
**Flow:**
1. Browse products (grid view)
2. Filter by category/price
3. Click product → detail page
4. Add to cart (increment quantity)
5. View cart
6. Checkout → WhatsApp order link generated
7. Customer send order confirmation via WhatsApp
8. Admin process order (mark as completed setelah cash)

**Database Operations:**
- Read from PRODUCTS
- Insert into ORDERS
- Insert into ORDER_ITEMS
- Update PRODUCTS.stock_quantity (auto-decrement)
- Insert LOYALTY_POINTS (jika customer login)

---

### Feature 3: User Authentication
**Customer Registration:**
- Email, phone, password (min 8 chars, contain number + letter)
- Verify email (optional Phase 2)
- Auto-login after register

**Admin Login:**
- Email + password
- 2FA (TOTP app) required
- Session token (JWT)
- Logout clears token

---

### Feature 4: Loyalty Points System
**Rules:**
- +1 point per Rp 10,000 spent on products
- +10 points per completed appointment
- Display in customer dashboard
- Redemption TBD (Phase 2)

**Database Operations:**
- Insert/update LOYALTY_POINTS after order completed or appointment completed

---

### Feature 5: Admin Notifications
**Types:**
1. **Appointment notifications**:
   - New appointment booked (customer name, date, time)
   - Appointment reminder (30 min before)
   - Upcoming appointments today
2. **Order notifications**:
   - New order received
   - Payment pending reminder
3. **System notifications**:
   - Low stock alert (< 5 units)
   - Database backup completed

**Delivery:** In-app notification badge (inbox icon) + optional WhatsApp/email

---

## 4. API ENDPOINTS (REST)

### Authentication Endpoints
```
POST   /api/auth/register          - Customer register
POST   /api/auth/login             - Customer login
POST   /api/auth/admin-login       - Admin login (email + password + 2FA)
POST   /api/auth/logout            - Logout (clear token)
POST   /api/auth/refresh-token     - Refresh JWT token
POST   /api/auth/change-password   - Change password
```

### Appointment Endpoints
```
GET    /api/treatments             - List all treatments
GET    /api/treatments/:id         - Get treatment detail
GET    /api/appointments/available-slots  - Get available time slots (query: date, treatment_id)
POST   /api/appointments           - Book new appointment
GET    /api/appointments/:id       - Get appointment detail
GET    /api/user/appointments      - Get my appointments (logged in user)
PUT    /api/appointments/:id       - Update appointment (reschedule, add notes)
DELETE /api/appointments/:id       - Cancel appointment

# Admin only
GET    /api/admin/appointments    - List all appointments (with filters)
PUT    /api/admin/appointments/:id/status - Update appointment status
```

### Product Endpoints
```
GET    /api/products               - List products (with category & price filters)
GET    /api/products/:id           - Get product detail
POST   /api/products               - Create product (admin only)
PUT    /api/products/:id           - Update product (admin only)
DELETE /api/products/:id           - Delete product (admin only)
PUT    /api/products/:id/stock     - Update stock (admin only)
```

### Order Endpoints
```
POST   /api/orders                 - Create order (from cart)
GET    /api/orders/:id             - Get order detail
GET    /api/user/orders            - Get my orders (logged in user)
PUT    /api/orders/:id/status      - Update order status (admin only)
POST   /api/orders/:id/send-whatsapp - Send order link via WhatsApp
```

### Cart Endpoints (Client-side, no API needed - localStorage)
```
GET    /api/cart                   - Get cart items
POST   /api/cart/add-item          - Add to cart
PUT    /api/cart/update-item       - Update quantity
DELETE /api/cart/remove-item       - Remove from cart
DELETE /api/cart/clear             - Clear all items
```

### User Endpoints
```
GET    /api/user/profile           - Get my profile (logged in)
PUT    /api/user/profile           - Update my profile
GET    /api/user/loyalty-points    - Get my loyalty points
GET    /api/user/notifications     - Get my notifications
```

### Admin Endpoints
```
GET    /api/admin/dashboard        - Dashboard summary data (role-based)
GET    /api/admin/staff            - List all staff (owner only)
POST   /api/admin/staff            - Add new staff (owner only)
PUT    /api/admin/staff/:id        - Update staff (owner only)
DELETE /api/admin/staff/:id        - Delete staff (owner only)

# Settings
GET    /api/admin/settings         - Get clinic settings
PUT    /api/admin/settings         - Update clinic settings

# Reports
GET    /api/admin/reports/sales    - Sales report (date range filter)
GET    /api/admin/reports/products - Product performance report
GET    /api/admin/reports/customers - Customer report
```

---

## 5. FEATURE PRIORITIES (MVP PHASES)

### Phase 1 (2-3 weeks) - MVP DEMO
✅ **MUST-HAVE:**
- Homepage (banner, why choose us, featured products & treatments, testimonials)
- Treatments listing page
- Product shop (grid view, filtering)
- Shopping cart
- Booking page
- Appointment confirmation
- Customer registration/login
- Customer dashboard (appointment history, order history, loyalty points)
- Admin login (with 2FA)
- Admin dashboard (summary widgets)
- Appointments management (list view, status update)
- Products management (CRUD)
- Orders management (list, status update)
- Admin settings (clinic info, staff management, hours)
- WhatsApp integration (send order link, appointment reminder)

### Phase 2 (Optional, After Client Feedback)
🔄 **NICE-TO-HAVE:**
- Payment gateway (Midtrans/GoPay)
- Email notifications (appointment confirmation, order update)
- SMS notifications (alternative to WhatsApp)
- Appointment calendar view (admin)
- Advanced reporting (sales charts, customer analytics)
- Product reviews/ratings
- Email verification for registration
- Password reset via email
- Appointment reminders (SMS/WhatsApp 30 min before)
- Loyalty points redemption
- Inventory auto-sync with warehouse
- Multi-branch support
- Customer loyalty tier system

### Phase 3 (Future)
🚀 **OUT OF SCOPE (For Later):**
- Mobile app (React Native / Flutter)
- Video consultation feature
- AI-based recommendation engine
- Franchise management system
- Advanced analytics dashboard
- CRM integration
- Inventory barcode scanning
- Automated SMS/WhatsApp campaigns

---

## 6. TECHNICAL SPECIFICATIONS

### Frontend Architecture
```
src/
├── components/          # Reusable Vue components
│   ├── Layout/
│   │   ├── Navbar.vue
│   │   ├── Footer.vue
│   │   └── Sidebar.vue
│   ├── Common/
│   │   ├── Button.vue
│   │   ├── Card.vue
│   │   └── Modal.vue
│   └── Features/
│       ├── AppointmentBooking/
│       ├── ProductCatalog/
│       ├── ShoppingCart/
│       └── Dashboard/
├── pages/               # Route pages
│   ├── Home.vue
│   ├── Treatments.vue
│   ├── Products.vue
│   ├── Booking.vue
│   └── Dashboard.vue
├── api/                 # API calls (axios)
├── store/               # State management (Pinia or Vuex)
└── utils/               # Helpers, formatters
```

### Backend Architecture
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── AppointmentController.php
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   └── AdminController.php
│   └── Requests/        # Form validation
├── Models/
│   ├── User.php
│   ├── Appointment.php
│   ├── Product.php
│   ├── Order.php
│   └── Staff.php
├── Services/            # Business logic
│   ├── AppointmentService.php
│   ├── OrderService.php
│   └── WhatsAppService.php
└── Middleware/
    ├── AuthenticateJWT.php
    ├── RoleBasedAccess.php
    └── 2FAVerification.php
```

---

## 7. DEPLOYMENT & ENVIRONMENT

### Local Development (Current Phase)
- Database: MySQL (local)
- Server: Laravel development server
- Frontend: Vue dev server
- File uploads: Local storage

### Phase 2 - Production (After Client Approval)
- Database: MySQL (hosted on VPS)
- Server: Laravel (VPS - AWS, DigitalOcean, atau shared hosting)
- Frontend: Built Vue (static files on same VPS)
- File uploads: Cloud storage (Cloudinary, AWS S3, atau local server)
- SSL: Let's Encrypt (free)
- Domain: Custom domain setup

---

## 8. SECURITY CONSIDERATIONS

✅ **Implemented:**
- Password hashing (bcrypt)
- JWT token for API auth
- 2FA for admin (TOTP)
- CSRF protection
- SQL injection prevention (parameterized queries)
- XSS prevention (Vue auto-escapes)
- Role-based access control (RBAC)

✅ **To Add (Phase 2):**
- Rate limiting on API
- API key for external integrations
- Audit logging (track admin actions)
- Data encryption for sensitive info
- Regular security audits

---

## 9. SUCCESS METRICS (FOR CLIENT MEETING)

**Demo should show:**
✅ Responsive design (mobile-first, fully works on phone)
✅ Smooth user flow (booking → dashboard)
✅ Admin can manage appointments & products
✅ WhatsApp integration working
✅ Loyalty points tracking
✅ Clean, professional UI with Tailwind CSS

**Performance targets:**
- Page load < 3 seconds
- API response < 500ms
- 99.9% uptime (after Phase 1)

---

## 10. NEXT STEPS

1. **Approve this PRD** ✓
2. **Finalize database schema** - Create migrations
3. **Setup project structure** - Laravel + Vue scaffold
4. **Start development** - Component by component
5. **Weekly demos** - Show progress to client
6. **Gather feedback** - Adjust scope if needed
7. **Deploy MVP** - Show to actual users
8. **Plan Phase 2** - Based on client feedback

---

**Document Version:** 1.0
**Last Updated:** 2025-05-04
**Status:** Ready for Development
