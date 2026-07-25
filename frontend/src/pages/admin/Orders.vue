<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <ShoppingBag class="w-6 h-6" />
          </div>
          Kelola Pesanan
        </h1>
        <p class="text-slate-500 text-sm font-medium">Pantau pesanan produk, pengiriman, dan status transaksi.</p>
      </div>
      
      <div class="flex items-center gap-2">
        <Button variant="outline" class="rounded-2xl border-slate-200 font-bold gap-2" @click="fetchOrders(1)">
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': isLoading }" />
          Refresh
        </Button>
      </div>
    </div>

    <!-- Filters Panel -->
    <Card class="p-6 border-none shadow-sm bg-white/50 backdrop-blur-sm rounded-3xl">
      <div class="flex flex-col lg:flex-row gap-6 items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
          <!-- Search -->
          <div class="space-y-2">
            <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari Pesanan</Label>
            <div class="relative group">
              <Search class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-gold-500 transition-colors" />
              <Input 
                v-model="filters.search" 
                @keyup.enter="fetchOrders(1)" 
                placeholder="ID Pesanan / No. HP..." 
                class="h-11 pl-11 rounded-2xl bg-white border-slate-100 shadow-sm focus:ring-gold-500" 
              />
            </div>
          </div>
          <!-- Status -->
          <div class="space-y-2">
            <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</Label>
            <Select v-model="filters.status" @update:modelValue="fetchOrders(1)">
              <SelectTrigger class="h-11 rounded-2xl bg-white border-slate-100 shadow-sm">
                <SelectValue placeholder="Semua Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">Semua Status</SelectItem>
                <SelectItem value="pending">Pending</SelectItem>
                <SelectItem value="completed">Completed</SelectItem>
                <SelectItem value="cancelled">Cancelled</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <!-- Date Range -->
          <div class="space-y-2">
            <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dari Tanggal</Label>
            <Input type="date" v-model="filters.from_date" class="h-11 rounded-2xl bg-white border-slate-100 shadow-sm" @change="fetchOrders(1)" />
          </div>
          <div class="space-y-2">
            <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Sampai Tanggal</Label>
            <Input type="date" v-model="filters.to_date" class="h-11 rounded-2xl bg-white border-slate-100 shadow-sm" @change="fetchOrders(1)" />
          </div>
        </div>
        
        <Button 
          v-if="hasActiveFilters" 
          variant="ghost" 
          @click="resetFilters" 
          class="h-11 px-6 rounded-2xl text-rose-500 hover:text-rose-600 hover:bg-rose-50 font-black text-xs uppercase tracking-widest transition-all"
        >
          <X class="w-4 h-4 mr-2" /> Reset
        </Button>
      </div>
    </Card>

    <!-- Data Table -->
    <Card class="border-none shadow-sm bg-white overflow-hidden rounded-3xl">
      <div class="overflow-x-auto">
        <Table>
          <TableHeader class="bg-slate-50/50">
            <TableRow class="border-slate-100">
              <TableHead class="pl-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Detail Pesanan</TableHead>
              <TableHead class="py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pelanggan</TableHead>
              <TableHead class="py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Bayar</TableHead>
              <TableHead class="py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</TableHead>
              <TableHead class="pr-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-if="isLoading" v-for="i in 5" :key="i" class="animate-pulse">
              <TableCell colspan="5" class="py-6 px-8">
                <div class="h-12 bg-slate-100 rounded-2xl w-full"></div>
              </TableCell>
            </TableRow>
            
            <TableRow v-else-if="orders.length === 0">
              <TableCell colspan="5" class="py-20 text-center">
                <div class="flex flex-col items-center gap-4">
                  <div class="p-4 rounded-full bg-slate-50 text-slate-300">
                    <ShoppingBag class="w-12 h-12" />
                  </div>
                  <div class="space-y-1">
                    <p class="text-slate-900 font-black tracking-tight">Tidak Ada Pesanan</p>
                    <p class="text-sm text-slate-400 font-medium">Gunakan filter lain atau refresh halaman.</p>
                  </div>
                </div>
              </TableCell>
            </TableRow>

            <TableRow v-else v-for="order in orders" :key="order.id" class="group hover:bg-slate-50/50 transition-all border-slate-50">
              <TableCell class="pl-8 py-5">
                <div class="space-y-1">
                  <p class="font-black text-slate-900 tracking-tight text-base">#{{ order.order_number }}</p>
                  <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                    <Calendar class="w-3 h-3" />
                    {{ formatDate(order.order_date) }}
                  </div>
                  <Button variant="link" @click="viewOrderDetails(order)" class="p-0 h-auto text-[11px] font-black text-gold-600 uppercase tracking-widest hover:text-gold-700">
                    Lihat {{ order.items?.length }} Item <ArrowRight class="ml-1 w-3 h-3" />
                  </Button>
                </div>
              </TableCell>
              
              <TableCell class="py-5">
                <div class="flex items-center gap-4">
                  <Avatar class="h-10 w-10 border-2 border-white shadow-sm">
                    <AvatarFallback class="bg-gold-50 text-gold-600 font-black text-xs">
                      {{ (order.customer_name || 'WC').substring(0, 2).toUpperCase() }}
                    </AvatarFallback>
                  </Avatar>
                  <div class="space-y-1">
                    <p class="font-bold text-slate-900 text-sm tracking-tight">{{ order.customer_name || 'Walk-in Customer' }}</p>
                    <div class="flex items-center gap-3">
                      <div class="flex items-center gap-1 text-[11px] font-bold text-slate-400">
                        <Phone class="w-3 h-3" /> {{ order.customer_phone || '-' }}
                      </div>
                      <Badge variant="outline" class="text-[9px] font-black uppercase tracking-widest border-slate-200 rounded-lg h-5">
                        {{ order.delivery_method }}
                      </Badge>
                    </div>
                  </div>
                </div>
              </TableCell>

              <TableCell class="py-5">
                <div class="space-y-0.5">
                  <p class="text-slate-900 font-black tracking-tight text-base">Rp {{ formatPrice(order.total_amount) }}</p>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tunai / Transfer</p>
                </div>
              </TableCell>

              <TableCell class="py-5">
                <Badge :class="cn('rounded-xl px-3 py-1 font-black text-[10px] uppercase tracking-widest shadow-sm', statusBadgeClass(order.status))">
                  {{ order.status }}
                </Badge>
              </TableCell>

              <TableCell class="pr-8 py-5 text-right">
                <div class="flex items-center justify-end gap-3">
                  <Select :modelValue="order.status" @update:modelValue="(val) => updateStatus(order.id, val)" :disabled="order.isUpdating">
                    <SelectTrigger class="w-32 h-10 rounded-xl bg-white border-slate-100 font-bold text-xs shadow-sm">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="pending">Pending</SelectItem>
                      <SelectItem value="completed">Completed</SelectItem>
                      <SelectItem value="cancelled">Cancelled</SelectItem>
                    </SelectContent>
                  </Select>
                  
                  <Button 
                    variant="outline" 
                    size="icon" 
                    @click="sendWhatsApp(order.id)" 
                    class="h-10 w-10 rounded-xl bg-emerald-50 border-emerald-100 text-emerald-600 hover:bg-emerald-100 shadow-sm"
                  >
                    <MessageCircle class="w-5 h-5" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-8 py-6 border-t border-slate-50 flex items-center justify-between bg-slate-50/30">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
          Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
        </p>
        <div class="flex gap-2">
          <Button 
            variant="outline" 
            size="sm" 
            @click="changePage(pagination.current_page - 1)" 
            :disabled="pagination.current_page === 1"
            class="rounded-xl border-slate-200 font-bold px-4"
          >
            Previous
          </Button>
          <Button 
            variant="outline" 
            size="sm" 
            @click="changePage(pagination.current_page + 1)" 
            :disabled="pagination.current_page === pagination.last_page"
            class="rounded-xl border-slate-200 font-bold px-4"
          >
            Next
          </Button>
        </div>
      </div>
    </Card>

    <!-- Order Items Dialog -->
    <Dialog :open="!!selectedOrder" @update:open="(val) => !val && (selectedOrder = null)">
      <DialogContent v-if="selectedOrder" class="sm:max-w-xl p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <!-- Dialog Header -->
        <div class="p-8 border-b bg-slate-50/50 flex items-center justify-between">
          <div class="space-y-1">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Detail Pesanan #{{ selectedOrder.order_number }}</h2>
            <div class="flex items-center gap-3">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ formatDate(selectedOrder.order_date) }}</p>
              <Badge :class="cn('rounded-lg px-2 py-0.5 font-black text-[9px] uppercase tracking-widest', statusBadgeClass(selectedOrder.status))">
                {{ selectedOrder.status }}
              </Badge>
            </div>
          </div>
          <div class="p-3 rounded-2xl bg-white shadow-sm text-gold-500">
            <Package class="w-6 h-6" />
          </div>
        </div>
        
        <!-- Order Items List -->
        <div class="p-8 max-h-[60vh] overflow-y-auto no-scrollbar space-y-6">
          <div class="space-y-4">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Item yang dipesan</p>
            <div v-for="item in selectedOrder.items" :key="item.id" class="group flex items-center gap-5 p-4 rounded-2xl bg-slate-50/50 border border-slate-100 hover:bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all">
              <div class="w-16 h-16 rounded-xl bg-white border border-slate-100 overflow-hidden shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                <img :src="item.product?.image_url || '/placeholder-product.jpg'" class="w-full h-full object-cover" />
              </div>
              <div class="flex-1 space-y-1">
                <p class="font-black text-slate-900 tracking-tight">{{ item.product?.name || 'Unknown Product' }}</p>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                  <span class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-600">{{ item.quantity }}x</span>
                  <span>@ Rp {{ formatPrice(item.price_at_purchase ?? item.price) }}</span>
                </div>
              </div>
              <div class="text-right">
                <p class="font-black text-gold-600 text-base tracking-tight">Rp {{ formatPrice(item.quantity * (item.price_at_purchase ?? item.price)) }}</p>
              </div>
            </div>
          </div>

          <!-- Shipping Info -->
          <div v-if="selectedOrder.shipping_address" class="space-y-3 pt-4 border-t border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Alamat Pengiriman</p>
            <div class="p-5 rounded-2xl bg-blue-50/50 border border-blue-100 flex items-start gap-3">
              <MapPin class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
              <p class="text-sm text-slate-600 font-medium leading-relaxed">{{ selectedOrder.shipping_address }}</p>
            </div>
          </div>
        </div>

        <!-- Dialog Footer -->
        <div class="p-8 bg-slate-50/50 border-t border-slate-100">
          <div class="flex items-center justify-between">
            <div class="space-y-0.5">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Pembayaran</p>
              <p class="text-3xl font-black text-slate-900 tracking-tighter">Rp {{ formatPrice(selectedOrder.total_amount) }}</p>
            </div>
            <Button @click="sendWhatsApp(selectedOrder.id)" class="h-14 px-8 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black shadow-xl shadow-emerald-500/20 gap-3">
              <MessageCircle class="w-5 h-5" /> Kirim Invoice
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import client from '@/api/client'
import { 
  ShoppingBag, Calendar, Phone, Search, X, 
  MessageCircle, RefreshCw, ArrowRight, 
  Package, MapPin, Loader2 
} from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { 
  Table, TableBody, TableCell, TableHead, 
  TableHeader, TableRow 
} from '@/components/ui/table'
import { 
  Select, SelectContent, SelectItem, 
  SelectTrigger, SelectValue 
} from '@/components/ui/select'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { cn } from '@/lib/utils'
import { toast } from 'vue-sonner'

const orders = ref([])
const isLoading = ref(false)
const pagination = ref({ current_page: 1, last_page: 1 })
const selectedOrder = ref(null)

const filters = ref({
  search: '',
  status: 'all',
  from_date: '',
  to_date: '',
})

const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.status !== 'all' || filters.value.from_date || filters.value.to_date
})

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

const statusBadgeClass = (status) => {
  switch (status) {
    case 'pending':   return 'bg-amber-100 text-amber-700 border-amber-200'
    case 'completed': return 'bg-emerald-100 text-emerald-700 border-emerald-200'
    case 'cancelled': return 'bg-rose-100 text-rose-700 border-rose-200'
    default:          return 'bg-slate-100 text-slate-700 border-slate-200'
  }
}

const fetchOrders = async (page = 1) => {
  isLoading.value = true
  try {
    const params = { 
      page, 
      ...filters.value,
      status: filters.value.status === 'all' ? '' : filters.value.status
    }
    // Clean empty values
    Object.keys(params).forEach(k => !params[k] && delete params[k])

    const res = await client.get('/admin/orders', { params })
    orders.value = res.data.data.map(o => ({ ...o, isUpdating: false }))
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page
    }
  } catch (err) {
    toast.error('Gagal memuat data pesanan')
  } finally {
    isLoading.value = false
  }
}

const resetFilters = () => {
  filters.value = { search: '', status: 'all', from_date: '', to_date: '' }
  fetchOrders(1)
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchOrders(page)
  }
}

const updateStatus = async (id, newStatus) => {
  const order = orders.value.find(o => o.id === id)
  if (!order) return
  
  order.isUpdating = true
  const previousStatus = order.status
  order.status = newStatus // Optimistic update
  
  try {
    await client.put(`/admin/orders/${id}/status`, { status: newStatus })
    toast.success(`Status pesanan #${order.order_number} berhasil diperbarui`)
  } catch (err) {
    order.status = previousStatus
    toast.error('Gagal memperbarui status pesanan')
  } finally {
    order.isUpdating = false
  }
}

const sendWhatsApp = async (id) => {
  const promise = client.post(`/admin/orders/${id}/send-whatsapp`)
  
  toast.promise(promise, {
    loading: 'Menyiapkan invoice WhatsApp...',
    success: (res) => {
      if (res.data?.whatsapp_url) {
        window.open(res.data.whatsapp_url, '_blank')
        return 'Invoice WhatsApp berhasil dibuat'
      }
      return 'Gagal membuat link WhatsApp'
    },
    error: 'Terjadi kesalahan saat membuat link WhatsApp'
  })
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
}

onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
