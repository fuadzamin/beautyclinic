import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
4: // Lazy-load all pages
const Home            = () => import('@/pages/Home.vue')
const Treatments      = () => import('@/pages/Treatments.vue')
const Products        = () => import('@/pages/Products.vue')
const ProductDetail   = () => import('@/pages/ProductDetail.vue')
const Booking         = () => import('@/pages/Booking.vue')
const Cart            = () => import('@/pages/Cart.vue')
const About           = () => import('@/pages/About.vue')
const Contact         = () => import('@/pages/Contact.vue')
14: // Customer authenticated
const CustomerDashboard = () => import('@/pages/CustomerDashboard.vue')
const Login             = () => import('@/pages/Login.vue')
const Register          = () => import('@/pages/Register.vue')
19: const AdminLayout        = () => import('@/components/AdminLayout.vue')
const AdminDashboard     = () => import('@/pages/admin/Dashboard.vue')
const AdminAppointments  = () => import('@/pages/admin/Appointments.vue')
const AdminTreatments    = () => import('@/pages/admin/Treatments.vue')
const AdminPOS           = () => import('@/pages/admin/POS.vue')
const AdminProducts      = () => import('@/pages/admin/Products.vue')
const AdminOrders        = () => import('@/pages/admin/Orders.vue')
const AdminTransactions  = () => import('@/pages/admin/TransactionHistory.vue')
const AdminSettings      = () => import('@/pages/admin/Settings.vu
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
path: 'products',     name: 'admin-products',     component: AdminProducts },
{ path: 'orders',       name: 'admin-orders',       component: AdminOrders },
{ path: 'settings',     name: 'admin-settings',     component: AdminSettings },
{ path: 'staff',        name: 'admin-staff',        component: AdminStaff },
{ path: 'branches',     name: 'admin-branches',     component: AdminBranches },
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
106: const router = createRouter({
history: createWebHistory(),
routes,
scrollBehavior: () => ({ top: 0 }),
})
112: // Navigation guards
router.beforeEach((to) => {
const auth = useAuthStore()
116:   if (to.meta.requiresAuth && !auth.isAuthenticated) {
return { name: 'login', query: { redirect: to.fullPath } }
}
120:   if (to.meta.requiresAdmin && !auth.isAdmin) {
return { name: 'login', query: { redirect: to.fullPath } }
}
})
125: export default router
The above content shows the entire, complete file contents of the requested file.