import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

// Lazy-load all pages
const Home            = () => import('@/pages/Home.vue')
const Treatments      = () => import('@/pages/Treatments.vue')
const Products        = () => import('@/pages/Products.vue')
const ProductDetail   = () => import('@/pages/ProductDetail.vue')
const Booking         = () => import('@/pages/Booking.vue')
const Cart            = () => import('@/pages/Cart.vue')
const About           = () => import('@/pages/About.vue')
const Contact         = () => import('@/pages/Contact.vue')

// Customer authenticated
const CustomerDashboard = () => import('@/pages/CustomerDashboard.vue')
const Login             = () => import('@/pages/Login.vue')
const Register          = () => import('@/pages/Register.vue')

const AdminLayout        = () => import('@/components/AdminLayout.vue')
const AdminDashboard     = () => import('@/pages/admin/Dashboard.vue')
const AdminAppointments  = () => import('@/pages/admin/Appointments.vue')
const AdminTreatments    = () => import('@/pages/admin/Treatments.vue')
const AdminPOS           = () => import('@/pages/admin/POS.vue')
const AdminProducts      = () => import('@/pages/admin/Products.vue')
const AdminOrders        = () => import('@/pages/admin/Orders.vue')
const AdminTransactions  = () => import('@/pages/admin/TransactionHistory.vue')
const AdminSettings      = () => import('@/pages/admin/Settings.vue')
const AdminStaff         = () => import('@/pages/admin/Staff.vue')
const AdminBranches      = () => import('@/pages/admin/Branches.vue')
const AdminReceiptSettings = () => import('@/pages/admin/ReceiptSettings.vue')

const routes = [
  // Public
  { path: '/',          name: 'home',        component: Home },
  { path: '/treatments',name: 'treatments',  component: Treatments },
  { path: '/products',  name: 'products',    component: Products },
  { path: '/products/:id', name: 'product-detail', component: ProductDetail },
  { path: '/booking',   name: 'booking',     component: Booking },
  { path: '/cart',      name: 'cart',        component: Cart },
  { path: '/about',     name: 'about',       component: About },
  { path: '/contact',   name: 'contact',     component: Contact },
  { path: '/login',     name: 'login',       component: Login },
  { path: '/register',  name: 'register',    component: Register },

  // Customer (requires auth)
  {
    path: '/dashboard',
    name: 'dashboard',
    component: CustomerDashboard,
    meta: { requiresAuth: true },
  },

  // Admin
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAdmin: true },
    children: [
      { path: 'dashboard',    name: 'admin-dashboard',    component: AdminDashboard, meta: { roles: ['owner', 'branch_manager', 'admin_klinik', 'admin_produk'] } },
      {
          path: 'appointments',
          name: 'admin-appointments',
          component: AdminAppointments,
          meta: { roles: ['owner', 'branch_manager', 'admin_klinik'] }
        },
        {
          path: 'treatments',
          name: 'admin-treatments',
          component: AdminTreatments,
          meta: { roles: ['owner', 'branch_manager', 'admin_klinik'] }
        },
        {
          path: 'pos',
          name: 'admin-pos',
          component: AdminPOS,
          meta: { roles: ['owner', 'branch_manager', 'admin_klinik', 'admin_produk'] }
        },
        {
          path: 'transactions',
          name: 'admin-transactions',
          component: AdminTransactions,
          meta: { roles: ['owner', 'branch_manager', 'admin_klinik', 'admin_produk'] }
        },
        {
          path: 'products',
          name: 'admin-products',
          component: AdminProducts,
          meta: { roles: ['owner', 'branch_manager', 'admin_produk'] }
        },
        {
          path: 'orders',
          name: 'admin-orders',
          component: AdminOrders,
          meta: { roles: ['owner', 'branch_manager', 'admin_produk'] }
        },
      { path: 'settings',     name: 'admin-settings',     component: AdminSettings, meta: { roles: ['owner'] } },
      { path: 'staff',        name: 'admin-staff',        component: AdminStaff, meta: { roles: ['owner', 'branch_manager'] } },
      { path: 'branches',     name: 'admin-branches',     component: AdminBranches, meta: { roles: ['owner'] } },
      { 
        path: 'receipt-settings', 
        name: 'admin-receipt-settings', 
        component: AdminReceiptSettings,
        meta: { roles: ['owner', 'branch_manager'] }
      },
      {
        path: 'reports',
        name: 'admin-reports',
        component: () => import('@/pages/admin/Reports.vue'),
        meta: { roles: ['owner', 'branch_manager'] }
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

// Navigation guards
router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // Role-based access
  if (to.meta.roles && !auth.hasRole(to.meta.roles)) {
    return { name: 'admin-dashboard' }
  }
})

export default router
