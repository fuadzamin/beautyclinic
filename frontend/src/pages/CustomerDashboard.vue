<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Header Section -->
      <div class="card bg-gradient-to-r from-gold-600 to-gold-400 text-white border-0 shadow-lg mb-8 relative overflow-hidden">
        <!-- Decorative SVG -->
        <svg class="absolute right-0 top-0 h-full opacity-10 pointer-events-none" viewBox="0 0 100 100" preserveAspectRatio="none">
          <circle cx="80" cy="20" r="40" fill="white" />
          <circle cx="90" cy="80" r="30" fill="white" />
        </svg>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 p-2">
          <div>
            <h1 class="text-3xl font-bold mb-2">Hello, {{ auth.user?.name }} ✨</h1>
            <p class="text-gold-100 flex items-center gap-2">
              <Mail class="w-4 h-4" /> {{ auth.user?.email }}
            </p>
          </div>
          <div class="flex items-center gap-6 bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/20">
            <div class="text-center">
              <p class="text-gold-100 text-sm font-medium mb-1">Aura Points</p>
              <p class="text-3xl font-extrabold flex items-center gap-2 justify-center">
                <Award class="w-6 h-6 text-gold-200" />
                {{ auth.user?.loyalty_points || 0 }}
              </p>
            </div>
            <div class="w-px h-12 bg-white/20"></div>
            <button @click="auth.logout()" class="hover:bg-white/20 p-3 rounded-xl transition-colors text-white" title="Logout">
              <LogOut class="w-6 h-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex gap-4 border-b border-slate-200 mb-8 overflow-x-auto hide-scrollbar">
        <button 
          @click="activeTab = 'appointments'"
          :class="['px-6 py-4 font-semibold text-sm transition-colors whitespace-nowrap', 
                   activeTab === 'appointments' ? 'text-gold-600 border-b-2 border-gold-500' : 'text-slate-500 hover:text-slate-800']"
        >
          <div class="flex items-center gap-2">
            <Calendar class="w-4 h-4" /> My Appointments
          </div>
        </button>
        <button 
          @click="activeTab = 'orders'"
          :class="['px-6 py-4 font-semibold text-sm transition-colors whitespace-nowrap', 
                   activeTab === 'orders' ? 'text-gold-600 border-b-2 border-gold-500' : 'text-slate-500 hover:text-slate-800']"
        >
          <div class="flex items-center gap-2">
            <ShoppingBag class="w-4 h-4" /> Order History
          </div>
        </button>
        <button 
          @click="activeTab = 'loyalty'"
          :class="['px-6 py-4 font-semibold text-sm transition-colors whitespace-nowrap', 
                   activeTab === 'loyalty' ? 'text-gold-600 border-b-2 border-gold-500' : 'text-slate-500 hover:text-slate-800']"
        >
          <div class="flex items-center gap-2">
            <Award class="w-4 h-4" /> Aura Points History
          </div>
        </button>
      </div>

      <!-- Tab Content: Appointments -->
      <div v-if="activeTab === 'appointments'" class="space-y-4">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-slate-800">Your Appointments</h2>
          <RouterLink to="/booking" class="btn-primary text-sm">Book New</RouterLink>
        </div>

        <div v-if="appointments.length === 0" class="card text-center py-16">
          <Calendar class="w-12 h-12 text-slate-300 mx-auto mb-4" />
          <p class="text-slate-500 text-lg">No appointments found.</p>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="apt in appointments" :key="apt.id" class="card hover:border-gold-200 group">
            <div class="flex justify-between items-start mb-4">
              <span :class="['badge', `badge-${apt.status}`]">{{ formatStatus(apt.status) }}</span>
              <span class="text-slate-400 text-xs font-medium">{{ formatDate(apt.created_at) }}</span>
            </div>
            <h3 class="font-bold text-lg text-slate-800 mb-2">{{ apt.treatment?.name || 'Treatment' }}</h3>
            <div class="space-y-2 text-sm text-slate-600 mb-6">
              <p class="flex items-center gap-2"><Clock class="w-4 h-4 text-gold-500" /> {{ formatDateTime(apt.appointment_date) }}</p>
              <p class="flex items-center gap-2"><User class="w-4 h-4 text-gold-500" /> {{ apt.customer_name }}</p>
            </div>
            <button v-if="apt.status === 'pending' || apt.status === 'confirmed'" @click="cancelAppointment(apt.id)" class="text-xs font-bold text-rose-500 hover:text-rose-600 transition-colors mt-2">
              Cancel Appointment
            </button>
          </div>
        </div>
      </div>

      <!-- Tab Content: Orders -->
      <div v-if="activeTab === 'orders'" class="space-y-4">
        <!-- ... existing orders content ... -->
      </div>

      <!-- Tab Content: Loyalty Points -->
      <div v-if="activeTab === 'loyalty'" class="space-y-4 animate-fade-in">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-slate-800">Points History</h2>
          <p class="text-sm text-slate-500">History of your Aura Points</p>
        </div>

        <div v-if="loyaltyPoints.length === 0" class="card text-center py-16">
          <Award class="w-12 h-12 text-slate-300 mx-auto mb-4" />
          <p class="text-slate-500 text-lg">No points history yet.</p>
        </div>
        
        <div v-else class="card overflow-hidden border-0 shadow-sm">
          <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
              <tr>
                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Activity</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Date</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Points</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="log in loyaltyPoints" :key="log.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div :class="['w-8 h-8 rounded-lg flex items-center justify-center', 
                                  log.points_earned > 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600']">
                      <ArrowUpRight v-if="log.points_earned > 0" class="w-4 h-4" />
                      <ArrowDownLeft v-else class="w-4 h-4" />
                    </div>
                    <div>
                      <p class="font-semibold text-slate-800 text-sm capitalize">{{ log.source.replace('_', ' ') }}</p>
                      <p class="text-xs text-slate-400">#{{ log.source_id }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-slate-500">{{ formatDate(log.created_at) }}</td>
                <td :class="['px-6 py-4 text-sm font-bold text-right', log.points_earned > 0 ? 'text-emerald-600' : 'text-rose-600']">
                  {{ log.points_earned > 0 ? '+' : '' }}{{ log.points_earned }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { 
  Mail, Award, LogOut, Calendar, ShoppingBag, Clock, User, 
  ArrowUpRight, ArrowDownLeft 
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { useAuthStore } from '@/stores/authStore'
import { useAppointmentStore } from '@/stores/appointmentStore'
import { useOrderStore } from '@/stores/orderStore'
import client from '@/api/client'

const auth = useAuthStore()
const appointmentStore = useAppointmentStore()
const orderStore = useOrderStore()
const router = useRouter()

const activeTab = ref('appointments')
const appointments = ref([])
const orders = ref([])
const loyaltyPoints = ref([])

const cancelAppointment = async (id) => {
  if (!confirm('Yakin ingin membatalkan janji temu ini?')) return
  try {
    await client.delete(`/appointments/${id}`, { data: { reason: 'Cancelled by customer' } })
    appointments.value = appointments.value.filter(a => a.id !== id)
    toast.success('Janji temu berhasil dibatalkan')
  } catch (err) {
    toast.error('Gagal membatalkan janji temu')
  }
}

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)
const formatDate = (dateStr) => new Date(dateStr).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
const formatDateTime = (dateStr) => new Date(dateStr).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
const formatStatus = (s) => s ? s.replace('_', ' ').toUpperCase() : ''

onMounted(async () => {
  // Fetch data directly using store methods
  try {
    const aptsResponse = await appointmentStore.fetchMyAppointments()
    appointments.value = aptsResponse.data
  } catch (e) {
    console.error("Failed to load appointments", e)
  }

  try {
    const ordersResponse = await orderStore.fetchMyOrders()
    orders.value = ordersResponse.data
  } catch (e) {
    console.error("Failed to load orders", e)
  }

  try {
    const response = await client.get('/user/loyalty-points')
    loyaltyPoints.value = response.data.data
  } catch (e) {
    console.error("Failed to load loyalty points", e)
  }
})
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
