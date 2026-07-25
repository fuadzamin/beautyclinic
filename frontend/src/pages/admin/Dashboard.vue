<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col gap-1">
      <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dashboard Overview</h1>
      <p class="text-muted-foreground">Selamat datang kembali, berikut ringkasan performa klinik hari ini.</p>
    </div>

    <!-- Stats Grid -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      <Card v-for="i in 4" :key="i" class="border-none shadow-sm">
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <div class="h-4 w-24 bg-slate-100 animate-pulse rounded" />
          <div class="h-4 w-4 bg-slate-100 animate-pulse rounded" />
        </CardHeader>
        <CardContent>
          <div class="h-8 w-16 bg-slate-100 animate-pulse rounded mb-1" />
          <div class="h-3 w-32 bg-slate-100 animate-pulse rounded" />
        </CardContent>
      </Card>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
      <Card v-for="stat in formattedStats" :key="stat.key" class="rounded-2xl border border-slate-100 bg-white shadow-md hover:shadow-xl transition-all duration-300 group">
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
            {{ stat.label }}
          </CardTitle>
          <div class="p-2.5 rounded-xl bg-gold-50 text-gold-600 group-hover:bg-gold-500 group-hover:text-white transition-colors duration-300">
            <component :is="stat.icon" class="h-4.5 w-4.5" />
          </div>
        </CardHeader>
        <CardContent>
          <div class="text-3xl font-extrabold text-slate-950 tracking-tight">{{ stat.value }}</div>
          <div class="flex items-center gap-1.5 mt-2 text-[11px] font-semibold text-emerald-600 bg-emerald-50/70 w-fit px-2 py-0.5 rounded-full">
            <TrendingUp class="w-3.5 h-3.5" />
            <span>Diperbarui {{ lastUpdated || '22.33' }}</span>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Quick Actions & Recent Activity -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
      <!-- Quick Actions -->
      <Card class="xl:col-span-2 border border-slate-100 shadow-md rounded-2xl overflow-hidden bg-white">
        <div class="bg-gold-500 h-1.5 w-full"></div>
        <CardHeader>
          <CardTitle class="text-lg flex items-center gap-2 font-bold text-slate-900">
            <Zap class="w-5 h-5 text-gold-500 fill-gold-500" /> 
            Aksi Cepat
          </CardTitle>
          <CardDescription class="text-slate-500">Akses fitur utama dalam satu klik</CardDescription>
        </CardHeader>
        <CardContent>
          <div class="flex gap-3 flex-wrap">
            <Button v-if="auth.hasRole(['owner', 'admin_klinik'])" as-child variant="default" class="bg-orange-500 hover:bg-orange-600 text-white rounded-xl h-11 px-6 shadow-md shadow-orange-500/20 font-bold border-none">
              <RouterLink to="/admin/appointments">
                <Calendar class="mr-2 h-4 w-4" /> Kelola Janji Temu
              </RouterLink>
            </Button>
            
            <Button v-if="auth.hasRole(['owner', 'admin_produk'])" as-child class="bg-slate-900 hover:bg-slate-800 text-white rounded-xl h-11 px-6 shadow-md font-bold border-none">
              <RouterLink to="/admin/orders">
                <ShoppingCart class="mr-2 h-4 w-4" /> Review Pesanan
              </RouterLink>
            </Button>

            <Button v-if="auth.hasRole(['owner', 'admin_produk'])" as-child class="bg-gold-500 hover:bg-gold-600 text-white rounded-xl h-11 px-6 border-none shadow-md font-bold">
              <RouterLink to="/admin/products">
                <Package class="mr-2 h-4 w-4" /> Update Inventori
              </RouterLink>
            </Button>
          </div>
        </CardContent>
      </Card>

      <!-- Welcome Card / Promotion -->
      <Card class="bg-slate-900 border-none shadow-xl text-white relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
          <Sparkles class="w-32 h-32" />
        </div>
        <CardHeader>
          <CardTitle class="text-white">Tips Hari Ini</CardTitle>
          <CardDescription class="text-slate-400">Tingkatkan efisiensi layanan</CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <p class="text-sm text-slate-300 leading-relaxed">
            Pastikan stok produk skincare tetap terjaga untuk menyambut lonjakan pengunjung di akhir pekan.
          </p>
          <Button variant="ghost" class="text-gold-400 hover:text-gold-300 hover:bg-white/5 p-0 h-auto font-semibold group/btn">
            Lihat Laporan Stok
            <ArrowRight class="ml-2 w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
          </Button>
        </CardContent>
      </Card>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import client from '@/api/client'
import { useAuthStore } from '@/stores/authStore'
import { 
  Calendar, ShoppingCart, Clock, Package, AlertCircle, 
  Activity, Zap, TrendingUp, Sparkles, ArrowRight
} from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Button } from '@/components/ui/button'

const auth = useAuthStore()
const stats = ref({})
const isLoading = ref(false)
const lastUpdated = ref(null)

const iconMap = {
  total_appointments: Calendar,
  total_orders: ShoppingCart,
  pending_appointments: Clock,
  pending_orders: Clock,
  today_appointments: Activity,
  pending_confirmations: Clock,
  no_show_count: AlertCircle,
  total_products: Package,
  low_stock_count: AlertCircle,
}

const formattedStats = computed(() => {
  return Object.entries(stats.value).map(([key, value]) => ({
    key,
    label: key.replaceAll('_', ' ').replace(/\b\w/g, c => c.toUpperCase()),
    value,
    icon: iconMap[key] || Activity
  }))
})

onMounted(async () => {
  isLoading.value = true
  try {
    const res = await client.get('/admin/dashboard')
    stats.value = res.data
    lastUpdated.value = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  } catch (err) {
    console.error('Failed to load dashboard stats', err)
  } finally {
    isLoading.value = false
  }
})
</script>
