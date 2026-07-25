# Beauty Clinic Management — Future Roadmap

## Status Terkini: Refactoring & POS Hardening (Completed)
- [x] **Modular POS Architecture**: Pemisahan komponen Kasir, Antrean, dan Riwayat.
- [x] **Service Layer Implementation**: Konsolidasi logika bisnis di `app/Services`.
- [x] **Standardized API**: Penggunaan `ApiResponse` trait untuk konsistensi response.
- [x] **Branch Isolation**: Data terisolasi berdasarkan `branch_id` staff.

---

### Phase 2: Inventory & Analytics (Completed ✅)
- [x] Product Stock Management (Multiple Branches)
- [x] POS Module (Checkout treatment + products)
- [x] Reports (Sales, Top Products, Demographics)
- [x] Receipt Customization

### Phase 3: Security & Loyalty (Completed ✅)
- [x] Staff Role-Based Access Control (RBAC)
- [x] 2FA for Admin/Staff Security
- [x] Customer Loyalty System (Points Earning & Redemption)
- [x] Activity Logs for Admin Audit Trail

## Tahap 4: Communication & Automation
Otomatisasi pengingat dan notifikasi untuk meningkatkan kehadiran pelanggan.

- [ ] **WhatsApp API Integration**: Pengiriman otomatis pengingat janji temu (H-1) dan struk digital via WA.
- [ ] **Automated Stock Alerts**: Notifikasi ke admin produk jika stok di cabang tertentu di bawah ambang batas (*threshold*).
- [ ] **Automated Database Backups**: Penjadwalan backup harian untuk keamanan data.

---

## Prioritas Pertanyaan (Brainstorming)
Dari tahap-tahap di atas, mana yang ingin Anda prioritaskan untuk segera diimplementasikan?
1. **Analytics** (Visualisasi grafik pendapatan & tren).
2. **Security & Loyalty** (2FA dan penggunaan poin di kasir).
3. **Automation** (Notifikasi WhatsApp).
