# EXECUTION GUIDE - Using System & User Prompts for AI Agent Development

## OVERVIEW

Anda punya 2 documents utama:
1. **SYSTEM_PROMPT_BEAUTY_CLINIC.md** - Instruksi untuk AI agent tentang konteks, standards, best practices
2. **USER_PROMPTS_TEMPLATES.md** - Panduan task-by-task untuk setiap fitur yang akan dibangun

**Alur kerja:**
1. Persiapkan task (gunakan template dari USER PROMPTS)
2. Berikan SYSTEM PROMPT + USER PROMPT ke AI agent
3. AI akan generate code sesuai standards
4. Review, test, integrate
5. Ulangi untuk task berikutnya

---

## STEP-BY-STEP WORKFLOW

### PHASE 1: SETUP (Hari 1-2)

#### Task 1: Project Scaffolding

**What to do:**
1. Buka `USER_PROMPTS_TEMPLATES.md` → cari **TEMPLATE 1: PROJECT SETUP**
2. Copy template 1 sepenuhnya
3. Buat prompt lengkap:

```
[Paste full SYSTEM_PROMPT_BEAUTY_CLINIC.md here]

---

[Paste TEMPLATE 1 from USER_PROMPTS_TEMPLATES.md here]
```

4. **Paste ke Claude Code atau ChatGPT:**
   - Open Claude Code di terminal: `claude-code .`
   - Atau gunakan ChatGPT-4 web interface
   - Paste prompt lengkap
   - Wait for code output

5. **What AI will provide:**
   - Complete Laravel project structure
   - All 8 migrations
   - Models with relationships
   - Base configuration files
   - Frontend folder setup

6. **Integration steps:**
   ```bash
   # Create Laravel project
   composer create-project laravel/laravel beauty-clinic
   cd beauty-clinic
   
   # Copy migrations dari AI output
   cp [AI-generated-migrations]* database/migrations/
   
   # Copy models dari AI output
   cp [AI-generated-models]* app/Models/
   
   # Run migrations
   php artisan migrate
   
   # Setup Vue 3 + Tailwind
   npm install
   npm install -D tailwindcss postcss autoprefixer
   npx tailwindcss init -p
   ```

7. **Verify:**
   ```bash
   php artisan tinker
   > App\Models\User::count() // Should be 0
   > App\Models\Treatment::count() // Should be 0
   ```

---

#### Task 2: Authentication System

**What to do:**
1. Buka `USER_PROMPTS_TEMPLATES.md` → **TEMPLATE 2: AUTHENTICATION**
2. Prepare prompt:

```
[SYSTEM PROMPT]

---

[TEMPLATE 2]
```

3. Paste ke AI agent
4. AI will provide:
   - AuthController.php
   - Form Requests (RegisterRequest, LoginRequest)
   - JWT configuration
   - Frontend components (LoginForm, RegisterForm)
   - Pinia auth store
   - Axios interceptor

5. **Integration:**
   ```bash
   # Copy controller
   cp AuthController.php app/Http/Controllers/
   
   # Copy requests
   cp *Request.php app/Http/Requests/
   
   # Copy components
   cp LoginForm.vue src/components/
   cp RegisterForm.vue src/components/
   
   # Copy store
   cp authStore.js src/stores/
   
   # Add routes to routes/api.php
   Route::post('auth/register', [AuthController::class, 'register']);
   Route::post('auth/login', [AuthController::class, 'login']);
   ```

6. **Test:**
   ```bash
   # Test registration
   curl -X POST http://localhost:8000/api/v1/auth/register \
     -H "Content-Type: application/json" \
     -d '{"email":"test@test.com","password":"Test1234","phone":"62812345678"}'
   
   # Test login
   curl -X POST http://localhost:8000/api/v1/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"test@test.com","password":"Test1234"}'
   ```

---

### PHASE 2: CORE FEATURES (Hari 3-7)

#### Task 3: Appointment Booking

**Follow same pattern as Task 2:**
1. Use TEMPLATE 3: APPOINTMENT BOOKING
2. Prompt = SYSTEM + TEMPLATE 3
3. AI provides: Controller, Model, Requests, Frontend components, Store
4. Integrate into project
5. Test full booking flow

**Specific testing:**
```bash
# Test available slots endpoint
curl http://localhost:8000/api/v1/appointments/slots?treatment_id=1&date=2025-05-15

# Test booking appointment
curl -X POST http://localhost:8000/api/v1/appointments \
  -H "Content-Type: application/json" \
  -d '{
    "treatment_id": 1,
    "appointment_date": "2025-05-15T14:00:00Z",
    "customer_name": "Siti",
    "customer_phone": "62812345678"
  }'
```

---

#### Task 4: Product Catalog & Shopping

**Use TEMPLATE 4:**
1. Prompt = SYSTEM + TEMPLATE 4
2. Includes: ProductController, Product model, Frontend shop components
3. Implement shopping cart (localStorage based Phase 1)
4. Test filtering, cart operations

**Testing:**
```bash
# Test product listing
curl http://localhost:8000/api/v1/products?category=serum

# Test adding to cart (client-side, localStorage)
# Open browser → shop page → add item → verify localStorage
```

---

### PHASE 3: ADMIN FEATURES (Hari 8-12)

#### Task 5: Admin Dashboard

**Use TEMPLATE 5:**
1. Prompt = SYSTEM + TEMPLATE 5
2. Role-based dashboard widgets
3. Owner sees revenue, Admin Klinik sees appointments, Admin Produk sees inventory
4. Test each role separately

```bash
# Admin login
curl -X POST http://localhost:8000/api/v1/auth/admin-login \
  -d '{"email":"owner@clinic.com","password":"Admin1234"}'

# Fetch dashboard (with JWT token)
curl http://localhost:8000/api/v1/admin/dashboard \
  -H "Authorization: Bearer [TOKEN]"
```

---

#### Task 6: Admin Management Pages

**Use TEMPLATE 6 (covers 3 pages):**
1. Appointments management
2. Products management (CRUD)
3. Orders management
4. Each with filters, list view, detail modal

**Testing:**
```bash
# Admin create product
curl -X POST http://localhost:8000/api/v1/admin/products \
  -H "Authorization: Bearer [TOKEN]" \
  -F "name=Facial Serum" \
  -F "price=125000" \
  -F "image=@serum.jpg"

# Admin mark appointment complete
curl -X PUT http://localhost:8000/api/v1/admin/appointments/1/status \
  -H "Authorization: Bearer [TOKEN]" \
  -d '{"status":"completed"}'
```

---

### PHASE 4: USER FEATURES (Hari 13-16)

#### Task 7: Customer Dashboard

**Use TEMPLATE 7:**
1. Profile section
2. Appointment history + reschedule/cancel
3. Order history
4. Loyalty points display

```bash
# Customer view their appointments
curl http://localhost:8000/api/v1/user/appointments \
  -H "Authorization: Bearer [CUSTOMER_TOKEN]"
```

---

#### Task 8: WhatsApp Integration

**Use TEMPLATE 8:**
1. Generate wa.me links for orders
2. Generate wa.me links for appointments
3. Admin can send WhatsApp confirmations

**Testing:**
```bash
# Generate WhatsApp link
curl http://localhost:8000/api/v1/orders/1/send-whatsapp

# Should return: {"wa_link": "https://wa.me/62...?text=..."}
# Click link → WhatsApp opens with pre-filled message
```

---

#### Task 9: Loyalty Points

**Use TEMPLATE 9:**
1. Award points on product purchase
2. Award points on appointment completion
3. Display in customer dashboard

```bash
# Check loyalty points
curl http://localhost:8000/api/v1/user/loyalty-points \
  -H "Authorization: Bearer [TOKEN]"
```

---

### PHASE 5: TESTING & DEPLOYMENT (Hari 17-20)

#### Task 10: Testing & QA

**Use TEMPLATE 10:**
1. Write unit tests for critical logic
2. Write integration tests for complete flows
3. Test all API endpoints
4. Manual QA on all browsers/devices

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Vue component tests
npm run test
```

---

#### Task 11: Deployment & Launch

**Use TEMPLATE 11:**
1. Pre-launch checklist
2. Seed sample data
3. Create demo script
4. Prepare documentation
5. Hand off to client

```bash
# Seed database with sample data
php artisan db:seed --class=DemoSeeder

# Create API documentation
php artisan scribe:generate

# Build for production
npm run build
```

---

## BEST PRACTICES WHEN USING AI AGENT

### DO ✅

**1. Use structured prompts**
```
Always include:
- SYSTEM PROMPT (full context, standards, best practices)
- USER PROMPT (specific task with requirements)
- Optional: Code snippets for reference
```

**2. Break down into small tasks**
```
❌ BAD: "Build the entire system in one prompt"
✅ GOOD: Task 1 (setup), Task 2 (auth), Task 3 (booking), etc.
```

**3. Review AI output before integrating**
```
Checklist:
- [ ] Code follows SYSTEM PROMPT standards
- [ ] Type hints present
- [ ] Error handling implemented
- [ ] No hardcoded values
- [ ] Comments for complex logic
```

**4. Maintain consistency**
```
When modifying AI output:
- Keep same naming conventions
- Use same error handling pattern
- Follow same folder structure
- Use same styling approach (Tailwind)
```

**5. Version control**
```bash
git add .
git commit -m "Add: [feature name] (Task N)"
git push
```

**6. Test immediately**
```
After integration:
1. Manual API testing (curl/Postman)
2. Frontend manual testing (browser)
3. Mobile responsive check
4. Error case testing
```

### DON'T ❌

**1. Don't use outdated system prompts**
```
Always use latest SYSTEM_PROMPT_BEAUTY_CLINIC.md
Update if standards change
```

**2. Don't skip validation**
```
❌ Skip testing "to save time"
✅ Test thoroughly to catch bugs early
```

**3. Don't modify without understanding**
```
❌ Copy-paste code without reading
✅ Read & understand before integrating
```

**4. Don't commit broken code**
```
❌ git push without testing
✅ Test locally, then commit
```

**5. Don't deviate from PRD**
```
❌ Add features beyond Phase 1 scope
✅ Stick to defined features
```

---

## TROUBLESHOOTING COMMON ISSUES

### Issue 1: AI generates code that doesn't match your DB schema

**Solution:**
- Pass your exact database schema in the prompt
- Include migration code in the prompt for reference
- Ask AI to use specific field names

Example:
```
Our appointments table has:
- id (PK)
- user_id (FK to users, nullable)
- appointment_date (datetime)
- customer_phone (string)
- status (enum: pending, confirmed, completed)

Please generate the Appointment model with correct relationships...
```

---

### Issue 2: API response format doesn't match expected format

**Solution:**
- Include expected response format in USER PROMPT
- Ask AI to follow response standard from SYSTEM PROMPT

Example:
```
API response must follow this format:
{
  "success": true,
  "data": {...},
  "message": "string",
  "timestamp": "ISO8601"
}
```

---

### Issue 3: Frontend component doesn't integrate with API

**Solution:**
- Pass API endpoint details in prompt
- Include example API response
- Ask for complete integration with error handling

Example:
```
API Endpoint: GET /api/v1/appointments
Expected Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "appointment_date": "2025-05-15T14:00:00Z",
      "customer_name": "Siti"
    }
  ]
}

Please generate Vue component that fetches this endpoint...
```

---

### Issue 4: AI forgets previous context

**Solution:**
- Always include full SYSTEM PROMPT
- Include relevant previous code snippets
- Remind AI of standards in the USER PROMPT

Example:
```
Remember these standards from earlier:
- Use Pinia for state management
- Use Tailwind for styling
- Error handling with try-catch

Now build the product component...
```

---

## TEMPLATE USAGE REFERENCE

| Task | Template | Files Generated |
|------|----------|-----------------|
| Project Setup | 1 | Migrations, Models, Controllers, Config |
| Authentication | 2 | AuthController, Requests, Frontend, Store |
| Appointments | 3 | AppointmentController, Frontend forms, Store |
| Products | 4 | ProductController, Frontend shop, Cart logic |
| Admin Dashboard | 5 | DashboardController, Frontend dashboard |
| Admin Pages | 6 | 3x Controllers, 6x Frontend pages |
| Customer Dashboard | 7 | Frontend dashboard sections, Store |
| WhatsApp | 8 | WhatsAppService, Endpoints, Frontend |
| Loyalty Points | 9 | LoyaltyPointsService, Endpoints |
| Testing | 10 | Unit tests, Integration tests |
| Deployment | 11 | Setup scripts, Documentation |

---

## QUICK COMMAND REFERENCE

### Laravel Commands
```bash
# Create new migration
php artisan make:migration create_[table]_table

# Create new model
php artisan make:model [ModelName]

# Create new controller
php artisan make:controller [ControllerName]

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Run tests
php artisan test

# Check routes
php artisan route:list
```

### Vue/NPM Commands
```bash
# Install dependencies
npm install

# Dev server
npm run dev

# Build for production
npm run build

# Run tests
npm run test

# Lint check
npm run lint
```

### Git Commands
```bash
# Stage changes
git add .

# Commit
git commit -m "Add: [feature]"

# Push
git push

# View log
git log --oneline
```

---

## SAMPLE DEVELOPMENT DAY SCHEDULE

**Day 1:**
- Morning: Setup project (TEMPLATE 1)
- Afternoon: Authentication (TEMPLATE 2)
- End: Test registration & login

**Day 2:**
- Morning: Appointment booking (TEMPLATE 3)
- Afternoon: Product catalog (TEMPLATE 4)
- End: Test booking & shopping flows

**Day 3:**
- Morning: Admin dashboard (TEMPLATE 5)
- Afternoon: Admin management pages (TEMPLATE 6)
- End: Test admin functionality

**Day 4:**
- Morning: Customer dashboard (TEMPLATE 7)
- Afternoon: WhatsApp + Loyalty points (TEMPLATES 8-9)
- End: Test user features

**Day 5:**
- Morning: Testing & QA (TEMPLATE 10)
- Afternoon: Fix bugs, documentation
- Evening: Deployment prep (TEMPLATE 11)

---

## SUCCESS METRICS

✅ **By end of Phase 1, you should have:**
- Complete Laravel + Vue project
- All 11 features implemented
- 80%+ test coverage
- Mobile-responsive UI
- Working WhatsApp integration
- Ready for client demo

✅ **Quality checklist:**
- No console errors
- API tests passing
- Unit tests passing
- Mobile responsive tested
- All Phase 1 features working
- Documentation complete

---

## NEXT STEPS AFTER PHASE 1

1. **Week 3-4: Client Demo & Feedback**
   - Show demo to client
   - Gather feedback
   - Document improvements
   - Start Phase 2 planning

2. **Phase 2: Payment Gateway & Notifications**
   - Use same System + User Prompt methodology
   - Create new templates for Phase 2 features
   - Integrate Midtrans/GoPay
   - Add email notifications
   - Implement 2FA for admin

3. **Phase 3: Mobile App & Advanced Features**
   - Create React Native templates
   - AI-based recommendations
   - Multi-branch support

---

## FINAL CHECKLIST

Before each development session:
- [ ] Have latest SYSTEM_PROMPT_BEAUTY_CLINIC.md
- [ ] Have TEMPLATE for current task
- [ ] Know exact requirements from PRD
- [ ] Understand current project state
- [ ] Have test plan ready
- [ ] Git committed & pushed

During development:
- [ ] Follow SYSTEM PROMPT standards
- [ ] Test as you build
- [ ] Review AI output before integrating
- [ ] Keep code consistent
- [ ] Commit frequently

After completion:
- [ ] All tests passing
- [ ] Mobile responsive verified
- [ ] Documentation updated
- [ ] Code reviewed
- [ ] Ready for next task

---

**This guide ensures:**
✅ Consistent, high-quality development
✅ Rapid feature building with AI
✅ Professional, maintainable codebase
✅ Successful client handoff
✅ Scalability for Phase 2 & 3

**Questions?** Refer to SYSTEM_PROMPT_BEAUTY_CLINIC.md or USER_PROMPTS_TEMPLATES.md

**Last Updated:** 2025-05-04
**Version:** 1.0
**Status:** Ready for Development
