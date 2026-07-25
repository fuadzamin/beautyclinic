<template>
  <div class="space-y-8 animate-in fade-in duration-500 pb-12">
    <!-- Header & Filters -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <PieChart class="w-6 h-6" />
          </div>
          Laporan & Analitik
        </h1>
        <p class="text-slate-500 text-sm font-medium">Pantau performa bisnis, demografi pelanggan, dan tren layanan secara real-time.</p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-sm focus-within:ring-2 focus-within:ring-gold-500/10 transition-all">
          <Calendar class="w-4 h-4 text-slate-400" />
          <input 
            type="date" 
            v-model="filters.start_date" 
            class="text-sm font-bold border-none focus:ring-0 p-0 text-slate-600 bg-transparent"
          />
          <span class="text-slate-300 font-bold">/</span>
          <input 
            type="date" 
            v-model="filters.end_date" 
            class="text-sm font-bold border-none focus:ring-0 p-0 text-slate-600 bg-transparent"
          />
        </div>

        <Select v-if="userRole === 'owner'" v-model="filters.branch_id">
          <SelectTrigger class="w-[180px] h-11 rounded-2xl bg-white border-slate-200 font-bold">
            <SelectValue placeholder="Semua Cabang" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Semua Cabang</SelectItem>
            <SelectItem v-for="b in branches" :key="b.id" :value="b.id.toString()">{{ b.name }}</SelectItem>
          </SelectContent>
        </Select>

        <Button 
          @click="fetchData"
          class="h-11 px-6 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-xl shadow-slate-900/10 gap-2"
          :disabled="loading"
        >
          <RefreshCcw class="w-4 h-4" :class="{ 'animate-spin': loading }" />
          Update Data
        </Button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <Card class="p-6 border-none shadow-sm bg-white group hover:shadow-md transition-all duration-300">
        <div class="flex items-start justify-between mb-4">
          <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600 group-hover:scale-110 transition-transform">
            <DollarSign class="w-6 h-6" />
          </div>
          <Badge variant="secondary" class="bg-emerald-50 text-emerald-600 border-none font-black rounded-full">
            <TrendingUp class="w-3 h-3 mr-1" /> 12.5%
          </Badge>
        </div>
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
        <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ formatCurrency(salesData.total_revenue) }}</h3>
      </Card>

      <Card class="p-6 border-none shadow-sm bg-white group hover:shadow-md transition-all duration-300">
        <div class="flex items-start justify-between mb-4">
          <div class="p-3 bg-blue-50 rounded-2xl text-blue-600 group-hover:scale-110 transition-transform">
            <ShoppingBag class="w-6 h-6" />
          </div>
          <Badge variant="secondary" class="bg-blue-50 text-blue-600 border-none font-black rounded-full">
            <TrendingUp class="w-3 h-3 mr-1" /> 5.2%
          </Badge>
        </div>
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Transaksi</p>
        <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ salesData.total_transactions }}</h3>
      </Card>

      <Card class="p-6 border-none shadow-sm bg-white group hover:shadow-md transition-all duration-300">
        <div class="flex items-start justify-between mb-4">
          <div class="p-3 bg-gold-50 rounded-2xl text-gold-600 group-hover:scale-110 transition-transform">
            <Target class="w-6 h-6" />
          </div>
          <Badge variant="secondary" class="bg-gold-50 text-gold-600 border-none font-black rounded-full">
            OPTIMAL
          </Badge>
        </div>
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Rata-rata Transaksi</p>
        <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ formatCurrency(averageTransaction) }}</h3>
      </Card>
    </div>

    <!-- Charts & Lists -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Revenue Trend -->
      <Card class="lg:col-span-2 p-8 border-none shadow-sm bg-white">
        <div class="flex items-center justify-between mb-8">
          <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <BarChart3 class="w-5 h-5 text-gold-500" />
              Tren Pendapatan Harian
            </h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Visualisasi Performa Penjualan</p>
          </div>
        </div>
        
        <div class="h-64 flex items-end justify-between gap-1.5 px-2 border-b border-slate-100 pb-2">
          <div 
            v-for="day in salesData.daily_revenue" 
            :key="day.date"
            class="flex-1 bg-slate-100 hover:bg-gold-500 transition-all rounded-t-lg relative group cursor-pointer"
            :style="{ height: (day.revenue / maxRevenue * 100) + '%' }"
          >
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 hidden group-hover:block z-20">
              <div class="bg-slate-900 text-white text-[10px] font-black py-2 px-3 rounded-xl shadow-2xl whitespace-nowrap animate-in fade-in slide-in-from-bottom-2">
                <p class="text-slate-400 uppercase tracking-tighter">{{ day.date }}</p>
                {{ formatCurrency(day.revenue) }}
              </div>
              <div class="w-2 h-2 bg-slate-900 rotate-45 mx-auto -mt-1"></div>
            </div>
          </div>
        </div>
        <div v-if="salesData.daily_revenue.length === 0" class="py-20 text-center text-slate-300">
          <BarChart3 class="w-12 h-12 mx-auto mb-3 opacity-20" />
          <p class="text-sm font-bold">Belum ada data harian untuk periode ini.</p>
        </div>
      </Card>

      <!-- Top Items Grid -->
      <Card class="p-8 border-none shadow-sm bg-white">
        <div class="flex items-center justify-between mb-8">
          <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <Package class="w-5 h-5 text-gold-500" />
              Produk Terlaris
            </h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Skincare & Produk Klinik</p>
          </div>
        </div>
        <div class="space-y-6">
          <div v-for="(item, idx) in performanceData.top_products" :key="idx" class="flex items-center justify-between group">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center font-black text-slate-400 group-hover:bg-gold-500 group-hover:text-white transition-colors">
                {{ idx + 1 }}
              </div>
              <div>
                <p class="font-black text-slate-900 leading-tight">{{ item.item_name }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">{{ formatCurrency(item.total_revenue) }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-lg font-black text-slate-900">{{ item.total_sold }}</p>
              <p class="text-[10px] font-black text-slate-400 uppercase">Unit Terjual</p>
            </div>
          </div>
          <div v-if="performanceData.top_products.length === 0" class="py-12 text-center opacity-30">
            <p class="text-sm font-bold">Tidak ada data produk.</p>
          </div>
        </div>
      </Card>

      <Card class="p-8 border-none shadow-sm bg-white">
        <div class="flex items-center justify-between mb-8">
          <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <Sparkles class="w-5 h-5 text-gold-500" />
              Layanan Terpopuler
            </h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Layanan Treatment & Konsultasi</p>
          </div>
        </div>
        <div class="space-y-6">
          <div v-for="(item, idx) in performanceData.top_treatments" :key="idx" class="flex items-center justify-between group">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center font-black text-slate-400 group-hover:bg-rose-500 group-hover:text-white transition-colors">
                {{ idx + 1 }}
              </div>
              <div>
                <p class="font-black text-slate-900 leading-tight">{{ item.name }}</p>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">{{ formatCurrency(item.total_revenue) }}</p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-lg font-black text-slate-900">{{ item.total_bookings }}</p>
              <p class="text-[10px] font-black text-slate-400 uppercase">Sesi Treatment</p>
            </div>
          </div>
          <div v-if="performanceData.top_treatments.length === 0" class="py-12 text-center opacity-30">
            <p class="text-sm font-bold">Tidak ada data layanan.</p>
          </div>
        </div>
      </Card>

      <!-- Demographics -->
      <Card class="p-8 border-none shadow-sm bg-white">
        <div class="flex items-center justify-between mb-8">
          <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <Users class="w-5 h-5 text-gold-500" />
              Distribusi Gender
            </h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Karakteristik Pelanggan</p>
          </div>
        </div>
        <div class="space-y-8">
          <div v-for="g in demographics.gender_distribution" :key="g.gender" class="space-y-3">
            <div class="flex justify-between items-end">
              <div class="flex items-center gap-2">
                <span class="p-1 rounded-md bg-slate-50 text-slate-400">
                  <User v-if="g.gender === 'pria'" class="w-3 h-3" />
                  <User2 v-else class="w-3 h-3" />
                </span>
                <span class="text-xs font-black text-slate-700 uppercase tracking-wide capitalize">{{ g.gender }}</span>
              </div>
              <span class="text-sm font-black text-slate-900">{{ g.count }} <span class="text-[10px] text-slate-400 uppercase font-bold tracking-normal ml-0.5">User</span></span>
            </div>
            <Progress :value="(g.count / totalUsers * 100)" class="h-2.5 rounded-full bg-slate-50 overflow-hidden shadow-inner" />
          </div>
          <div v-if="demographics.gender_distribution.length === 0" class="py-10 text-center opacity-20">
            <p class="text-xs font-bold">Data gender tidak tersedia.</p>
          </div>
        </div>
      </Card>

      <Card class="p-8 border-none shadow-sm bg-white">
        <div class="flex items-center justify-between mb-8">
          <div class="space-y-1">
            <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <Clock class="w-5 h-5 text-gold-500" />
              Rentang Usia
            </h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Target Pasar & Demografi Usia</p>
          </div>
        </div>
        <div class="space-y-8">
          <div v-for="a in demographics.age_distribution" :key="a.age_group" class="space-y-3">
            <div class="flex justify-between items-end">
              <span class="text-xs font-black text-slate-700 uppercase tracking-wide">{{ a.age_group }} Tahun</span>
              <span class="text-sm font-black text-slate-900">{{ a.count }} <span class="text-[10px] text-slate-400 uppercase font-bold tracking-normal ml-0.5">User</span></span>
            </div>
            <Progress :value="(a.count / totalUsers * 100)" class="h-2.5 rounded-full bg-slate-50 overflow-hidden shadow-inner" />
          </div>
          <div v-if="demographics.age_distribution.length === 0" class="py-10 text-center opacity-20">
            <p class="text-xs font-bold">Data usia tidak tersedia.</p>
          </div>
        </div>
      </Card>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { 
  Calendar, DollarSign, ShoppingBag, Users, User, User2,
  RefreshCcw, Package, Sparkles, PieChart, TrendingUp, 
  Target, BarChart3, Clock 
} from 'lucide-vue-next'
import client from '@/api/client'
import { useAuthStore } from '@/stores/authStore'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { Progress } from '@/components/ui/progress'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const userRole = computed(() => auth.user?.role)

const loading = ref(false)
const branches = ref([])
const filters = ref({
  start_date: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  branch_id: 'all'
})

const salesData = ref({
  total_revenue: 0,
  total_transactions: 0,
  daily_revenue: []
})

const performanceData = ref({
  top_products: [],
  top_treatments: []
})

const demographics = ref({
  gender_distribution: [],
  age_distribution: []
})

const totalUsers = computed(() => {
  return demographics.value.gender_distribution.reduce((acc, g) => acc + g.count, 0) || 1
})

const averageTransaction = computed(() => {
  if (salesData.value.total_transactions === 0) return 0
  return salesData.value.total_revenue / salesData.value.total_transactions
})

const maxRevenue = computed(() => {
  if (salesData.value.daily_revenue.length === 0) return 1
  return Math.max(...salesData.value.daily_revenue.map(d => d.revenue))
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value)
}

const fetchBranches = async () => {
  try {
    const res = await client.get('/admin/branches')
    branches.value = res.data || []
  } catch (err) {
    console.error('Failed to fetch branches', err)
  }
}

const fetchData = async () => {
  loading.value = true
  try {
    const params = { ...filters.value }
    if (params.branch_id === 'all') delete params.branch_id

    const [salesRes, perfRes, demoRes] = await Promise.all([
      client.get('/admin/reports/sales', { params }),
      client.get('/admin/reports/top-performing', { params }),
      client.get('/admin/reports/demographics', { params })
    ])
    salesData.value = salesRes.data || salesData.value
    performanceData.value = perfRes.data || performanceData.value
    demographics.value = demoRes.data || demographics.value
    toast.success('Laporan diperbarui')
  } catch (err) {
    toast.error('Gagal memuat data laporan')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (userRole.value === 'owner') fetchBranches()
  fetchData()
})
</script>
