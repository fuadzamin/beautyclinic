# POS Hardening - Implementation Plan

## Phase 1 — P0 Quick Wins
- [x] Konfirmasi modal sebelum proses bayar
- [x] Tampilkan transaction_number (bukan #id) di history
- [x] Auto-refresh antrian setiap 30 detik

## Phase 2 — Receipt Settings UI
- [x] Halaman admin /admin/receipt-settings
- [x] Form edit: nama klinik, alamat, logo, footer, auto_print
- [x] Per-branch settings

## Phase 3 — Backend Guards
- [x] Prevent stok negatif di TransactionController
- [x] Revenue summary endpoint dari backend
