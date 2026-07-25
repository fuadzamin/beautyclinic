# USER PROMPT TEMPLATES - Beauty Clinic System Development

## HOW TO USE THESE TEMPLATES

1. **Copy the relevant template** for the feature you're building
2. **Fill in the [BRACKETED PLACEHOLDERS]** with specific details
3. **Paste into your AI agent** (Claude Code, ChatGPT, etc.)
4. **AI will provide the code** following the SYSTEM PROMPT guidelines
5. **Review, test, and integrate** into your project

---

## TEMPLATE 1: PROJECT SETUP & SCAFFOLDING

**Use this for:** Initial project setup, creating folders, base files

```
I'm building a Beauty Clinic Management System (booking + e-commerce) using Laravel 11 + Vue 3 + Tailwind CSS. This is Phase 1 (MVP).

[SYSTEM PROMPT CONTEXT]:
- Client: Beauty Clinic in Kebumen
- Expected users: 500/day visitors, 100 appointments/day, 100 orders/day
- Tech: Laravel 11, Vue 3 (Composition API), MySQL 8, Tailwind CSS, Pinia
- Timeline: 2-3 weeks MVP

TASK: Set up the complete Laravel + Vue project structure

REQUIREMENTS:
1. Create Laravel project with migrations for these 8 core tables:
   - users (customers)
   - staff (admin/owner)
   - treatments (services)
   - appointments (bookings)
   - products (e-commerce items)
   - orders (customer orders)
   - order_items (items in orders)
   - loyalty_points (rewards)

2. Setup folder structure for both backend & frontend:
   Backend: app/Models, app/Http/Controllers, app/Http/Requests, routes/api.php
   Frontend: src/components, src/pages, src/stores, src/composables, src/api

3. Configure:
   - JWT authentication (laravel-sanctum or tymon/jwt-auth)
   - Pinia for state management
   - Axios for HTTP requests
   - Environment variables (.env)

4. Create base models with relationships:
   - User hasMany Appointments
   - User hasMany Orders
   - User hasMany LoyaltyPoints
   - Treatment hasMany Appointments
   - Order hasMany OrderItems
   - Product hasMany OrderItems

5. Generate example Eloquent models and migrations following Laravel best practices

OUTPUT:
- Complete .env.example file
- All 8 migration files
- All 8 Eloquent models with relationships
- Base folder structure for frontend/backend
- package.json and composer.json dependencies
- .gitignore configured

Please follow the SYSTEM PROMPT guidelines for Laravel/Vue coding standards.
```

---

## TEMPLATE 2: FEATURE - AUTHENTICATION

**Use this for:** User registration, login, JWT token management

```
FEATURE: User Authentication System (Phase 1 - No 2FA yet)

CONTEXT:
- Customer registration: email, password, phone
- Customer login: email + password → JWT token
- Admin login: email + password → JWT token (2FA will be added Phase 2)
- Password requirements: min 8 chars, at least 1 number + 1 letter
- Authentication method: JWT (Bearer token)

TASK: Implement complete authentication system

REQUIREMENTS:
1. Create AuthController with these methods:
   - register() - Customer self-registration
   - login() - Customer login
   - adminLogin() - Admin login (Phase 1: just email + password)
   - logout() - Clear JWT token
   - me() - Get current user info
   - refreshToken() - Refresh JWT token

2. Create form requests for validation:
   - RegisterRequest (email, password, phone)
   - LoginRequest (email, password)
   - ChangePasswordRequest

3. Password handling:
   - Hash passwords with bcrypt (Laravel's default)
   - Generate JWT token on successful login
   - Token TTL: 1440 minutes (24 hours)
   - Store JWT secret in .env as JWT_SECRET

4. API Endpoints:
   POST /api/v1/auth/register
   POST /api/v1/auth/login
   POST /api/v1/auth/admin-login
   POST /api/v1/auth/logout (protected)
   GET /api/v1/auth/me (protected)
   POST /api/v1/auth/change-password (protected)

5. Response format (following SYSTEM PROMPT standards):
   {
     "success": true,
     "data": {
       "user": {...},
       "token": "eyJhbGc..."
     },
     "message": "Login successful"
   }

6. Error handling:
   - 401 if invalid credentials
   - 422 if validation fails
   - 400 if email already registered
   - Custom error messages in Indonesian

7. Frontend Vue 3 components:
   - RegisterForm.vue (email, password, phone, submit)
   - LoginForm.vue (email, password, submit)
   - Store credentials & token in Pinia store
   - Setup axios interceptor to attach JWT to all requests
   - Handle token refresh & logout

8. Security:
   - Validate email format
   - Validate password strength
   - Hash passwords with bcrypt
   - Implement rate limiting on login endpoint (5 attempts/minute)

9. Testing:
   - Unit test for password hashing
   - Integration test: user can register → login → get token
   - Test invalid credentials return 401
   - Test protected endpoint requires valid token

OUTPUT:
- AuthController.php with all methods
- RegisterRequest.php, LoginRequest.php
- User model with password handling
- API routes setup
- Frontend auth store (Pinia)
- LoginForm.vue and RegisterForm.vue components
- Axios instance with JWT interceptor
- Error handling for all scenarios
- Unit + Integration tests

Follow SYSTEM PROMPT for error responses, validation, type hints.
```

---

## TEMPLATE 3: FEATURE - APPOINTMENT BOOKING

**Use this for:** Complete appointment booking system

```
FEATURE: Appointment Booking System (Phase 1)

CONTEXT:
- Customer selects treatment type (facial, skincare, etc.)
- Customer picks available date (max 7 days from now)
- Customer picks available time slot (auto-generated based on treatment duration)
- Customer provides: name, phone number (for WhatsApp follow-up), concern (optional)
- No login required (anonymous bookings allowed)
- Admin follow-up via WhatsApp after booking
- Flexible appointment duration per treatment (30min, 60min, 90min etc.)

TASK: Implement complete appointment booking flow

REQUIREMENTS:
1. Backend - AppointmentController methods:
   - getAvailableSlots() - GET /api/v1/appointments/slots
     Params: treatment_id, date (query string)
     Logic: Generate time slots excluding already booked appointments
     Returns: Array of available times ["09:00", "10:00", "11:00"...]
   
   - store() - POST /api/v1/appointments
     Accept: treatment_id, appointment_date, customer_name, customer_phone, customer_concern
     Create appointment record
     Validate phone format (Indonesian: 62xxx)
     Return: appointment details + order_number
   
   - show() - GET /api/v1/appointments/:id
   - update() - PUT /api/v1/appointments/:id (admin only, update notes/status)
   - cancel() - DELETE /api/v1/appointments/:id (customer can cancel 2+ hours before)
   - index() - GET /api/v1/appointments (admin only, list all)

2. Validation:
   - treatment_id must exist in treatments table
   - appointment_date must be today or future (max 7 days)
   - customer_phone must match Indonesian format (62...)
   - customer_name required, max 100 chars
   - customer_concern optional, max 500 chars

3. Database:
   - appointments table with fields:
     id, user_id (nullable), staff_id (nullable), treatment_id, appointment_date,
     customer_name, customer_phone, customer_concern, status, notes, created_at
   - status enum: pending, confirmed, completed, cancelled, no_show
   - Add indexes on: appointment_date, treatment_id, customer_phone

4. Business Logic:
   - Available slots: Generate from treatment duration_minutes
     Example: Treatment duration 60 min, clinic hours 09:00-18:00
     Slots: [09:00, 10:00, 11:00, ..., 17:00]
   - Exclude already booked times
   - Cancel rule: Only if appointment_date is 2+ hours away
   - No overbooking for same doctor at same time

5. API Responses:
   POST /api/v1/appointments returns:
   {
     "success": true,
     "data": {
       "id": 1,
       "treatment_id": 3,
       "appointment_date": "2025-05-10T14:00:00Z",
       "customer_name": "Siti Nurhaliza",
       "customer_phone": "62812345678",
       "status": "pending",
       "order_number": "APT-001-20250510",
       "message": "Admin akan follow-up melalui WhatsApp"
     }
   }

6. Frontend Components:
   - AppointmentBookingPage.vue (main booking page)
     Steps:
     1. Select Treatment (dropdown list all treatments)
     2. Select Date (calendar picker, max 7 days)
     3. Select Time (dropdown auto-populated from slots)
     4. Enter Details (name, phone, concern)
     5. Confirmation (show summary, submit button)
   
   - TreatmentSelector.vue (dropdown with treatment names + durations)
   - DatePicker.vue (calendar showing available dates)
   - TimeSlotSelector.vue (dropdown/buttons with available times)
   - AppointmentConfirmation.vue (show booking details + success message)

7. State Management (Pinia):
   - appointmentStore with:
     - state: selectedTreatment, selectedDate, selectedTime, customerName, customerPhone, customerConcern
     - actions: fetchTreatments(), fetchAvailableSlots(), submitBooking()
     - getters: isFormValid(), appointmentSummary()

8. Error Handling:
   - 422 if validation fails (show field-specific errors)
   - 409 if time slot already booked (suggest other slots)
   - Show user-friendly error messages in Indonesian
   - Success modal with appointment confirmation

9. Testing:
   - Test available slots generation (no overlap with existing bookings)
   - Test cancel appointment (only 2+ hours before)
   - Test form validation
   - Integration test: Book appointment → verify in database

OUTPUT:
- AppointmentController.php with all methods
- Appointment model with relationships
- AppointmentRequest validation
- Available slots generation logic
- Frontend booking pages (Vue 3 components)
- appointmentStore (Pinia)
- Error handling & validation
- Tests for booking flow

Follow SYSTEM PROMPT guidelines for API responses, error codes, type hints.
```

---

## TEMPLATE 4: FEATURE - PRODUCT CATALOG & SHOPPING CART

**Use this for:** E-commerce product listing, filtering, shopping cart

```
FEATURE: Product Catalog & Shopping Cart (Phase 1)

CONTEXT:
- 8 product categories: serum, sunscreen, moisturizer, cleanser, acne_treatment, mask, body_care, soap
- Mobile-first grid layout (1 column mobile, 2 tablet, 3 desktop)
- Auto track inventory (stock_quantity auto-decrements on order)
- No login required to buy (anonymous checkout)
- Products can be filtered by category & price
- Shopping cart stored in localStorage (Phase 1)

TASK: Implement product catalog + shopping cart system

REQUIREMENTS:
1. Backend - ProductController methods:
   - index() - GET /api/v1/products
     Params: category (optional), min_price (optional), max_price (optional), page (pagination)
     Returns: paginated products with: id, name, price, image_url, stock_quantity, category
   
   - show() - GET /api/v1/products/:id
     Returns: full product details (name, description, price, category, stock, ingredients, images)
   
   - store() - POST /api/v1/products (admin_produk only)
     Accept: name, description, price, category, stock_quantity, image, ingredients
   
   - update() - PUT /api/v1/products/:id (admin_produk only)
   
   - destroy() - DELETE /api/v1/products/:id (soft delete, admin_produk only)

2. Product Attributes:
   - id, name, description, price, category, stock_quantity
   - image_url (single image Phase 1, multiple Phase 2)
   - ingredients (text, for skincare products)
   - is_active (boolean, soft delete)
   - views (tracking popularity)
   - created_at, updated_at

3. Filtering & Search:
   - Filter by category (enum: serum, sunscreen, moisturizer, cleanser, acne_treatment, mask, body_care, soap)
   - Filter by price range (slider: min - max)
   - Pagination: 15 items per page
   - Order by: featured, newest, price_asc, price_desc (Phase 1: featured & newest)

4. Stock Management:
   - Show "Out of Stock" if stock_quantity = 0
   - Auto-decrement when order completed
   - Alert admin if stock < 5 units (Phase 2: notification)
   - Manual stock adjustment by admin_produk

5. API Responses:
   GET /api/v1/products returns:
   {
     "success": true,
     "data": {
       "items": [
         {
           "id": 1,
           "name": "Facial Serum",
           "price": 125000,
           "category": "serum",
           "stock_quantity": 10,
           "image_url": "https://...",
           "is_active": true
         }
       ],
       "pagination": {
         "current_page": 1,
         "last_page": 5,
         "total": 75
       }
     }
   }

6. Frontend Components:
   - ProductShop.vue (main shop page)
     Layout: filters on left (mobile: collapse), products grid on right
     - ProductFilter.vue (category pills, price slider)
     - ProductGrid.vue (responsive grid display)
     - ProductCard.vue (product image, name, price, "Add to Cart" button)
   
   - ProductDetail.vue (full product page)
     - Large product image
     - Name, price, stock status
     - Description + ingredients
     - Quantity selector + "Add to Cart" button
     - Back button
   
   - ShoppingCart.vue (cart page)
     - List cart items (image, name, quantity, price)
     - Quantity controls (-, +, remove)
     - Subtotal per item
     - Total price
     - "Checkout" button → checkout flow
     - "Continue Shopping" button

7. Cart State Management (Pinia):
   - cartStore with:
     - state: items (array with product details + quantity)
     - actions: addToCart(product, quantity), removeItem(productId), updateQuantity(productId, qty), clearCart()
     - getters: cartItemCount(), cartTotal(), cartItems()
   - Persist cart to localStorage on state change
   - Load cart from localStorage on app init

8. Checkout Flow:
   - Review cart items
   - Ask for customer name, phone (required), email (optional)
   - Generate WhatsApp order link
   - On checkout:
     POST /api/v1/orders {
       user_id (optional),
       items: [{product_id, quantity}],
       customer_name,
       customer_phone,
       total_price
     }
   - Return: order_number + WhatsApp link to send order
   - Admin receives notification with order details

9. Error Handling:
   - Product not found: 404
   - Validation error (invalid quantity): 422
   - Out of stock: Show message, disable "Add to Cart"
   - Cart items become unavailable: Warn user on checkout

10. Testing:
    - Test product filtering by category
    - Test price range filtering
    - Test add/remove from cart
    - Test cart persistence (localStorage)
    - Test checkout flow

OUTPUT:
- ProductController.php with filtering logic
- Product model
- ProductRequest validation
- Frontend shop pages (ProductShop, ProductDetail, ShoppingCart)
- Product card & grid components
- cartStore (Pinia)
- Filter components (category, price slider)
- Responsive Tailwind layout
- Tests for product filtering + cart logic

Follow SYSTEM PROMPT for API design, error handling, mobile-first approach.
```

---

## TEMPLATE 5: FEATURE - ADMIN DASHBOARD

**Use this for:** Admin dashboard with role-based widgets

```
FEATURE: Admin Dashboard (Phase 1)

CONTEXT:
- 3 admin roles: Owner, Admin Klinik, Admin Produk
- Each role sees different dashboard widgets
- Owner: Total revenue (appointments + products), bookings, orders, customers
- Admin Klinik: Today's appointments, pending confirmations, no-shows
- Admin Produk: Inventory value, low stock alerts, pending orders
- Summary cards with key metrics

TASK: Implement role-based admin dashboard

REQUIREMENTS:
1. Backend - DashboardController:
   - index() - GET /api/v1/admin/dashboard
     Auth: admin only (check role middleware)
     Return role-based data:
     - Owner: 
       {total_revenue_appointments, total_revenue_products, 
        total_appointments_month, total_orders_month,
        active_customers_month, top_treatments, top_products}
     - Admin Klinik:
       {appointments_today, pending_confirmations, no_shows_today,
        upcoming_appointments_week, recent_appointments}
     - Admin Produk:
       {total_inventory_value, low_stock_count, low_stock_products,
        pending_orders_count, top_products_month}

2. Metrics Calculations:
   - Revenue: Sum of all completed appointment prices + completed order totals
   - Appointments: Count where status = 'completed' (per time period)
   - Orders: Count all orders (per time period)
   - Low stock: Products where stock_quantity < 5
   - Pending orders: Orders where status = 'pending'

3. API Response:
   GET /api/v1/admin/dashboard returns:
   {
     "success": true,
     "data": {
       "role": "owner",
       "widgets": [
         {
           "id": "revenue",
           "label": "Total Revenue (Bulan Ini)",
           "value": "Rp 25.500.000",
           "change": "+12%",
           "icon": "trending-up"
         },
         {
           "id": "appointments",
           "label": "Appointments",
           "value": "127",
           "change": "+8"
         },
         ...
       ]
     }
   }

4. Frontend Components:
   - AdminDashboard.vue (main dashboard page)
     - Sidebar menu (Appointments, Products, Orders, Settings, Logout)
     - Top bar (clinic name, admin name, notifications icon)
     - Widgets grid (responsive 1/2/3 column layout)
     - DashboardWidget.vue (reusable widget card)
       Displays: label, value, change %, trend icon

5. Authorization:
   - Use middleware to check role:
     Route::middleware('role:owner')->group(...)
     Route::middleware('role:admin_klinik')->group(...)
     Route::middleware('role:admin_produk')->group(...)
   - Prevent unauthorized access to other admin sections

6. State Management:
   - adminStore (Pinia) with:
     - state: currentUser, dashboardData, role
     - actions: fetchDashboard(), logout()
     - getters: userRole(), dashboardWidgets()

7. Real-time Updates (Phase 2):
   - Phase 1: Static data loaded on page load
   - Phase 2: Auto-refresh every 5 minutes using WebSocket or polling

8. Error Handling:
   - 403 if user doesn't have permission
   - 401 if token expired
   - Show error message if data fetch fails

9. Testing:
   - Test owner sees owner widgets
   - Test admin_klinik sees klinik widgets
   - Test admin_produk sees produk widgets
   - Test unauthorized access returns 403

OUTPUT:
- DashboardController.php
- adminStore (Pinia)
- AdminDashboard.vue page
- DashboardWidget.vue component
- Role middleware
- Tests for authorization

Follow SYSTEM PROMPT for role-based access, API design.
```

---

## TEMPLATE 6: FEATURE - ADMIN MANAGEMENT PAGES

**Use this for:** Appointments, Products, Orders management

```
FEATURE: Admin Management Pages (Appointments, Products, Orders)

CONTEXT:
- Appointments page: Admin Klinik can view, update status, add notes
- Products page: Admin Produk can add, edit, delete, manage stock
- Orders page: Admin Produk & Owner can view, update status
- List views with filtering & pagination
- No calendar view Phase 1 (just list)

TASK: Implement all 3 admin management pages

REQUIREMENTS:

=== A. APPOINTMENTS MANAGEMENT ===

1. Endpoint: GET /api/v1/admin/appointments
   Params: status (filter), date_from, date_to, page
   Returns: paginated list {id, date, customer_name, phone, treatment, status, actions}

2. Update Status:
   PUT /api/v1/admin/appointments/:id/status
   Body: {status: "completed|cancelled|no_show", notes: "..."}

3. Frontend:
   - AppointmentsManagement.vue (list view)
     Filters: date range, status (pending/confirmed/completed/cancelled)
     Columns: Date, Customer, Treatment, Phone, Status, Actions
     Actions: View, Mark Complete, Mark No-show, Edit Notes, Cancel
   
   - AppointmentDetail.vue (modal/detail page)
     Show: all appointment details
     Edit: customer concern, admin notes
     Action buttons: Mark as Completed, Mark as No-show, Cancel

=== B. PRODUCTS MANAGEMENT ===

1. Endpoints:
   - GET /api/v1/admin/products (list)
   - POST /api/v1/admin/products (create)
   - PUT /api/v1/admin/products/:id (update)
   - DELETE /api/v1/admin/products/:id (soft delete)
   - PUT /api/v1/admin/products/:id/stock (update stock)

2. Frontend:
   - ProductsManagement.vue (list view)
     Table: Name, Category, Price, Stock, Status, Actions
     Filters: Category, Stock level
     Search: by name
     Actions: Edit, Delete, Restock, View Details
   
   - ProductForm.vue (create/edit form)
     Fields: Name, Description, Category, Price, Stock, Image upload, Ingredients
     Validation: required fields, price > 0, stock >= 0
     Image: single file upload (preview before submit)
     Submit: Create/Update button, Cancel button
   
   - StockManager.vue (quick stock adjustment)
     Show: current stock
     Input: new quantity
     Submit button

3. Image Upload (Phase 1):
   - Accept: JPG, PNG (max 2MB)
   - Save to: /public/products/
   - Return: URL for database storage
   - Phase 2: Cloud storage (Cloudinary, AWS S3)

=== C. ORDERS MANAGEMENT ===

1. Endpoint: GET /api/v1/admin/orders
   Params: status, date_from, date_to, page
   Returns: {order_number, customer_name, items_count, total, status, date}

2. Update Status:
   PUT /api/v1/admin/orders/:id/status
   Body: {status: "pending|completed|cancelled"}

3. Send WhatsApp Reminder:
   POST /api/v1/admin/orders/:id/send-whatsapp
   Returns: wa.me link or success message

4. Frontend:
   - OrdersManagement.vue (list view)
     Table: Order #, Customer, Items, Total, Status, Date, Actions
     Filters: Status, Date range
     Search: by order number or customer phone
     Actions: View, Mark Completed, Cancel, Send WhatsApp Reminder
   
   - OrderDetail.vue (detail modal)
     Show: order number, customer info, items list, total, status
     Actions: Mark Completed, Cancel, Send WhatsApp

=== COMMON FEATURES ===

4. List Features (all 3 pages):
   - Pagination: 15 items per page
   - Filters: dropdown/date pickers
   - Search: text input (name/phone/order number)
   - Sort: by date (newest first), by status
   - Bulk actions (Phase 2): select multiple → bulk status update

5. Authorization:
   - Admin Klinik: can only see appointments page
   - Admin Produk: can see products + orders pages
   - Owner: can see all pages

6. Error Handling:
   - 403 if trying to access page without permission
   - Show error message if action fails
   - Confirm delete/cancel before action

7. Success Messages:
   - Show toast/notification on successful action
   - Auto-refresh list after action
   - Show error message if action fails

8. Testing:
   - Test filtering & pagination
   - Test CRUD operations
   - Test authorization checks
   - Test form validation

OUTPUT:
- AppointmentsManagement.vue + AppointmentDetail.vue
- ProductsManagement.vue + ProductForm.vue
- OrdersManagement.vue + OrderDetail.vue
- Controllers for each (if not already done)
- Role-based access middleware
- Tests for each management page

Follow SYSTEM PROMPT for authorization, error handling, API design.
```

---

## TEMPLATE 7: FEATURE - CUSTOMER DASHBOARD

**Use this for:** Customer profile, appointment/order history, loyalty points

```
FEATURE: Customer Dashboard (Phase 1)

CONTEXT:
- Accessible after customer login
- Sections: Profile, Appointment History, Order History, Loyalty Points
- Customer can edit profile (name, email, address)
- Customer can change password
- Customer can reschedule appointment (if 2+ hours before)
- Customer can view loyalty points balance

TASK: Implement customer dashboard with all sections

REQUIREMENTS:
1. Endpoints:
   - GET /api/v1/user/profile (get current user)
   - PUT /api/v1/user/profile (update profile)
   - PUT /api/v1/user/change-password
   - GET /api/v1/user/appointments (my appointments)
   - GET /api/v1/user/orders (my orders)
   - GET /api/v1/user/loyalty-points (my loyalty points)
   - PUT /api/v1/appointments/:id (reschedule/cancel - customer)

2. Frontend Layout:
   - DashboardLayout.vue (sidebar + main content)
     Sidebar menu: Profile, Appointments, Orders, Loyalty Points, Notifications, Logout
     Mobile: Hamburger menu
   
   - ProfileSection.vue
     Display: Name, Email, Phone, Address
     Edit button → ProfileEditForm.vue
     Change Password button → ChangePasswordForm.vue
     Save & Cancel buttons with validation
   
   - AppointmentsSection.vue
     List upcoming & past appointments
     Columns: Date, Treatment, Doctor, Status, Actions
     Actions: 
     - If upcoming & 2+ hours away: Reschedule, Cancel
     - If completed: View details
     Show: treatment name, date/time, status badge
   
   - OrdersSection.vue
     List all orders
     Columns: Order #, Date, Items, Total, Status
     Status badge: pending, completed, cancelled
     Click to view order details
   
   - LoyaltyPointsSection.vue
     Display: Total points balance
     Show: points breakdown by source (appointments, purchases)
     Points history: date, amount, source, description
     Redeem button (Phase 2 functionality)

3. State Management (Pinia):
   - userStore with:
     - state: user (profile), appointments, orders, loyaltyPoints
     - actions: fetchProfile(), updateProfile(), changePassword(), fetchAppointments(), fetchOrders(), fetchLoyaltyPoints()
     - getters: userName(), userEmail(), pointsBalance()

4. Form Validation:
   - Profile edit: name (required), email (email format), phone (Indo format), address (optional)
   - Change password: current_password (required), new_password (min 8, number+letter), confirm

5. Appointment Actions:
   - Reschedule: Open date/time picker, show available slots, submit
   - Cancel: Show confirmation modal, allow cancellation only if 2+ hours away

6. Error Handling:
   - Show validation errors on forms
   - Show error message if API call fails
   - Handle unauthorized (token expired) - redirect to login

7. Mobile Responsive:
   - Sidebar collapses to hamburger menu
   - Forms stack vertically
   - Tables become cards on mobile

8. Testing:
   - Test user can view profile
   - Test user can edit profile with validation
   - Test user can change password
   - Test user can see appointment/order history
   - Test reschedule/cancel appointment validation

OUTPUT:
- DashboardLayout.vue (main dashboard layout)
- ProfileSection.vue with edit form
- AppointmentsSection.vue with action modals
- OrdersSection.vue
- LoyaltyPointsSection.vue
- userStore (Pinia)
- User model endpoints
- Validation & error handling
- Tests

Follow SYSTEM PROMPT for API design, state management, mobile-first approach.
```

---

## TEMPLATE 8: INTEGRATION - WHATSAPP

**Use this for:** WhatsApp order links & notifications

```
FEATURE: WhatsApp Integration (Phase 1)

CONTEXT:
- Phase 1: Generate WhatsApp message links (wa.me)
- Customer phones store with +62 prefix (Indonesian format)
- Order checkout generates WhatsApp link
- Admin can send appointment confirmations via WhatsApp link
- Phase 2: Real WhatsApp API integration

TASK: Implement WhatsApp message generation

REQUIREMENTS:
1. WhatsApp Link Generation:
   - Format: https://wa.me/[PHONE_NUMBER]?text=[ENCODED_MESSAGE]
   - Example: https://wa.me/62812345678?text=Pesanan%20APT-001
   - Phone format: 62xxx (ensure +62 prefix)

2. Messages to Generate:
   a) Appointment Confirmation (after booking)
      "Terima kasih telah booking appointment! 
       Treatment: [treatment_name]
       Tanggal: [date]
       Jam: [time]
       Silakan konfirmasi via pesan ini."
   
   b) Order Confirmation (after checkout)
      "Pesanan Anda telah dikirim! 
       Nomor Order: [order_number]
       Items: [items list]
       Total: Rp [amount]
       Pembayaran saat pengambilan. Terima kasih!"
   
   c) Appointment Reminder (Phase 2)
      "Reminder: Appointment Anda besok!
       Waktu: [time]
       Lokasi: [clinic address]"

3. Backend - WhatsAppService:
   ```php
   class WhatsAppService {
     public static function generateOrderLink($order) { ... }
     public static function generateAppointmentLink($appointment) { ... }
     public static function generateReminderMessage($appointment) { ... }
   }
   ```

4. Endpoints:
   - POST /api/v1/orders/:id/send-whatsapp
     Returns: {wa_link: "https://wa.me/..."}
   
   - GET /api/v1/appointments/:id/whatsapp-link
     Returns: {wa_link: "https://wa.me/..."}

5. Frontend:
   - Checkout page: Show "Send via WhatsApp" button
     Button opens WhatsApp link in new tab
     Message pre-filled with order details
     User confirms on WhatsApp directly
   
   - Admin appointments page: Show "Send WhatsApp" action
     Opens wa.me link with pre-filled message
     Admin can copy link or send directly

6. Phone Format Validation:
   - Accept both: 62xxx or 0xxx formats
   - Convert 0xxx → 62xxx (replace leading 0 with 62)
   - Validate: must be 10-13 digits after country code

7. Message Encoding:
   - Use URL encoding for special characters
   - Include: order number, items, total, date, time
   - Use emoji for visual appeal (✓, 📱, 🛍️, etc.)
   - Messages in Indonesian

8. Error Handling:
   - Invalid phone format: return error message
   - Missing required fields: return error message
   - Show user-friendly error in Indonesian

9. Phase 2 Real WhatsApp API:
   - Integrate with WhatsApp Business API
   - Auto-send messages (not just generate links)
   - Track delivery status
   - Handle webhook responses

OUTPUT:
- WhatsAppService.php with message generation logic
- Endpoints for order & appointment WhatsApp links
- Phone format validation utility
- Frontend buttons/actions to send WhatsApp
- Error handling & validation
- Tests for message generation

Follow SYSTEM PROMPT for service structure, error handling.
```

---

## TEMPLATE 9: INTEGRATION - LOYALTY POINTS

**Use this for:** Award and track loyalty points

```
FEATURE: Loyalty Points System (Phase 1)

CONTEXT:
- Rules: +1 point per Rp 10,000 spent
- Awarded on: Product purchase completion + Appointment completion
- Display in: Customer dashboard
- Calculation: Auto-award when order/appointment completed
- Phase 2: Redemption & tier system

TASK: Implement loyalty points system

REQUIREMENTS:
1. Loyalty Points Rules:
   - Product Purchase: points = floor(order_total / 10000)
     Example: Rp 125,000 → 12 points
   - Appointment: +10 points when completed
   - Minimum: 0 points, no negative

2. Backend - LoyaltyPointsService:
   ```php
   class LoyaltyPointsService {
     public static function awardForPurchase($userId, $amount) { ... }
     public static function awardForAppointment($userId) { ... }
     public static function getBalance($userId) { ... }
     public static function getHistory($userId) { ... }
   }
   ```

3. Endpoints:
   - GET /api/v1/user/loyalty-points
     Returns: {balance, history: [{date, amount, source, description}]}
   
   - GET /api/v1/loyalty-points/history?page=1
     Paginated history for logged-in user

4. Database:
   - LOYALTY_POINTS table:
     id, user_id, points_earned, total_points, source (appointment|product_purchase),
     source_id (appointment_id or order_id), created_at
   - Add column USERS.total_loyalty_points (denormalized for quick query)

5. Award Logic:
   - When order status changes to 'completed': Award points
   - When appointment status changes to 'completed': Award points
   - Create LOYALTY_POINTS record with source reference
   - Update USERS.total_loyalty_points (cumulative)

6. Frontend:
   - LoyaltyPointsSection.vue in dashboard
     Display: Total balance (large, prominent)
     History table: Date, Amount, Source, Description
     Pagination & sorting
     Visual: progress bar towards next tier (Phase 2)
   
   - Show points earned notifications:
     "Anda telah mendapatkan 12 poin!"

7. State Management:
   - userStore tracks: loyaltyPoints (balance), loyaltyHistory
   - fetchLoyaltyPoints() action to refresh

8. Error Handling:
   - Validate amount > 0
   - Validate user exists
   - Handle concurrent updates (use database transactions)

9. Testing:
   - Test points award on order completion
   - Test points award on appointment completion
   - Test balance calculation
   - Test points history retrieval

OUTPUT:
- LoyaltyPointsService.php
- LoyaltyPoint model with relationships
- Endpoints for fetching points
- Award logic in OrderController & AppointmentController
- LoyaltyPointsSection.vue component
- userStore loyalty points state
- Tests for points calculation & award

Follow SYSTEM PROMPT for service structure, transactions.
```

---

## TEMPLATE 10: TESTING & QA

**Use this for:** Unit tests, integration tests, QA checklist

```
FEATURE: Testing & Quality Assurance

CONTEXT:
- Unit tests for critical business logic
- Integration tests for complete user flows
- API tests to verify endpoints
- Frontend component tests
- Manual QA checklist before demo

TASK: Write comprehensive tests for Phase 1 features

REQUIREMENTS:
1. Unit Tests (PHP):
   - Appointment slot generation
   - Loyalty points calculation
   - Password validation
   - Phone number formatting
   - Stock management logic
   - Price calculation

2. Integration Tests:
   - User registration → login → update profile
   - Appointment booking flow (start to finish)
   - Product shopping → cart → checkout → order created
   - Admin login → dashboard → update appointment status
   - Admin add/edit/delete product

3. API Tests:
   - Test all REST endpoints
   - Verify response formats match SYSTEM PROMPT standards
   - Test error responses (400, 401, 403, 404, 422, 500)
   - Test authentication headers
   - Test invalid input validation

4. Frontend Tests:
   - Component rendering (mount, props, events)
   - Form validation
   - API call integration
   - State management (Pinia)
   - Navigation between pages

5. Manual QA Checklist:
   [ ] Mobile responsive (test on iPhone/iPad/Android)
   [ ] Form validation (test all error cases)
   [ ] Appointment booking (complete flow)
   [ ] Product purchase (complete flow)
   [ ] Admin login & operations
   [ ] WhatsApp links work
   [ ] Loyalty points award correctly
   [ ] Error messages display correctly
   [ ] UI layout correct on all screen sizes
   [ ] Performance acceptable (page load < 3s)
   [ ] No console errors or warnings
   [ ] Database queries optimized (no N+1)
   [ ] Security checks (no XSS, SQL injection, etc.)

6. Test Coverage Goals:
   - Critical business logic: 80%+
   - API endpoints: 100%
   - Components: 60%+

7. Test Commands:
   php artisan test (run all tests)
   npm run test (Vue component tests)
   npm run build (check for build errors)

OUTPUT:
- Unit tests for all services
- Integration tests for complete flows
- API endpoint tests
- Frontend component tests
- QA checklist document
- Test coverage report

Follow SYSTEM PROMPT for testing practices.
```

---

## TEMPLATE 11: DEPLOYMENT & LAUNCH CHECKLIST

**Use this for:** Final preparations before handing to client

```
FEATURE: Phase 1 Deployment & Launch

CONTEXT:
- Phase 1 completed & tested
- Ready for demo to client
- Local deployment (Phase 2: Cloud)
- Need launch checklist

TASK: Prepare Phase 1 for launch

REQUIREMENTS:
1. Pre-Launch Checklist:
   [ ] All Phase 1 features completed & tested
   [ ] Database migrations run successfully
   [ ] API endpoints responding correctly
   [ ] Frontend builds without errors
   [ ] Mobile responsive verified
   [ ] Performance acceptable
   [ ] Error handling complete
   [ ] Security review passed
   [ ] Documentation written

2. Environment Setup:
   - .env configured (local)
   - Database created & seeded
   - JWT secret generated
   - Storage permissions set
   - File uploads working
   - Logs writable

3. Demo Scenario Script:
   a) Show homepage
   b) Browse treatments & products
   c) Book appointment (walk-in)
   d) Purchase products → WhatsApp checkout
   e) Register account & login
   f) View dashboard (appointments, orders, points)
   g) Admin login → dashboard
   h) Manage appointments (mark complete)
   i) Manage products (add/edit)
   m) Manage orders (update status)

4. Sample Data:
   - 5-10 treatments
   - 15-20 products
   - 5-10 sample bookings
   - 3-5 sample orders
   - 3 sample admin accounts (owner, admin_klinik, admin_produk)

5. Documentation:
   - README.md (setup instructions)
   - API documentation (endpoints, params, responses)
   - Database schema diagram
   - User manual (for client)
   - Admin guide (for staff training)

6. Known Issues & Limitations:
   - Document Phase 1 limitations
   - List Phase 2 planned features
   - Note Phase 3 roadmap

7. Handover Package:
   - Source code (GitHub repo)
   - Database backup
   - .env template
   - Installation guide
   - Admin accounts (with credentials)
   - Demo video (optional)

8. Post-Launch (Phase 2):
   - Gather client feedback
   - Fix bugs/issues
   - Plan Phase 2 features
   - Set deployment date to cloud

OUTPUT:
- Pre-launch checklist
- Demo scenario & scripts
- Sample data SQL file
- User & admin guides
- Deployment documentation
- Known issues list
- Phase 2 planning document

Follow SYSTEM PROMPT for documentation standards.
```

---

## QUICK REFERENCE: USING THESE TEMPLATES

**Step 1:** Choose the relevant template based on feature
**Step 2:** Fill in placeholders [LIKE THIS]
**Step 3:** Add your SYSTEM PROMPT at the beginning
**Step 4:** Paste into Claude Code, ChatGPT, or your AI agent
**Step 5:** Review output & integrate into project
**Step 6:** Test thoroughly

Example for Phase 1, Template 1:
```
[SYSTEM PROMPT: Paste full SYSTEM_PROMPT_BEAUTY_CLINIC.md here]

[USER PROMPT: Paste TEMPLATE 1 - PROJECT SETUP here]
```

---

## TRACKING DEVELOPMENT PROGRESS

Use this to track which templates have been completed:

**Phase 1 MVP Checklist:**
- [ ] TEMPLATE 1: Project Setup
- [ ] TEMPLATE 2: Authentication
- [ ] TEMPLATE 3: Appointment Booking
- [ ] TEMPLATE 4: Product Catalog & Cart
- [ ] TEMPLATE 5: Admin Dashboard
- [ ] TEMPLATE 6: Admin Management Pages
- [ ] TEMPLATE 7: Customer Dashboard
- [ ] TEMPLATE 8: WhatsApp Integration
- [ ] TEMPLATE 9: Loyalty Points
- [ ] TEMPLATE 10: Testing & QA
- [ ] TEMPLATE 11: Deployment & Launch

**Estimated Timeline:**
- Templates 1-2: Days 1-2
- Templates 3-4: Days 3-7
- Templates 5-6: Days 8-12
- Templates 7-9: Days 13-16
- Template 10: Days 17-19
- Template 11: Day 20

---

**Document Version:** 1.0
**Last Updated:** 2025-05-04
**Status:** Ready for AI Agent Development
**Recommended AI Tools:** Claude Code, ChatGPT-4, or other advanced LLMs with code generation