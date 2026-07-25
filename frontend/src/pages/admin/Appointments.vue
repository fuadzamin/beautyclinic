<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <CalendarCheck2 class="w-6 h-6" />
          </div>
          Kelola Janji Temu
        </h1>
        <p class="text-slate-500 text-sm font-medium">Lihat dan perbarui status janji temu pelanggan.</p>
      </div>
    </div>
      
    <Card class="p-6 border-none shadow-sm bg-white/50 backdrop-blur-sm">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 items-end">
        <!-- Search -->
        <div class="space-y-2">
          <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cari Pelanggan</Label>
          <div class="relative">
            <Search class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
            <Input 
              v-model="searchQuery" 
              @keyup.enter="fetchAppointments(1)" 
              placeholder="Nama atau Telepon..." 
              class="h-11 pl-11 rounded-2xl bg-white border-slate-100 shadow-sm focus:ring-gold-500" 
            />
          </div>
        </div>

        <!-- Date -->
        <div class="space-y-2">
          <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tanggal</Label>
          <Input type="date" v-model="filterDate" class="h-11 rounded-2xl bg-white border-slate-100 shadow-sm" @change="fetchAppointments(1)" />
        </div>

        <!-- Status -->
        <div class="space-y-2">
          <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Status</Label>
          <Select v-model="filterStatus" @update:model-value="fetchAppointments(1)">
            <SelectTrigger class="h-11 rounded-2xl bg-white border-slate-100 shadow-sm">
              <SelectValue placeholder="Semua Status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Semua Status</SelectItem>
              <SelectItem value="pending">Pending</SelectItem>
              <SelectItem value="confirmed">Confirmed</SelectItem>
              <SelectItem value="completed">Completed</SelectItem>
              <SelectItem value="cancelled">Cancelled</SelectItem>
              <SelectItem value="no_show">No Show</SelectItem>
            </SelectContent>
          </Select>
        </div>
        
        <!-- Actions -->
        <div class="flex gap-2">
          <Button 
            v-if="filterStatus || filterDate || searchQuery" 
            variant="ghost"
            @click="resetFilters" 
            class="h-11 rounded-2xl font-bold text-rose-500 hover:bg-rose-50 hover:text-rose-600 px-4"
          >
            <X class="w-4 h-4 mr-2" /> Reset
          </Button>
          <Button 
            @click="fetchAppointments(1)"
            class="h-11 flex-1 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-xl shadow-slate-900/10"
          >
            Terapkan Filter
          </Button>
        </div>
      </div>
    </Card>

    <!-- Table -->
    <Card class="border-none shadow-sm overflow-hidden bg-white">
      <div class="overflow-x-auto w-full">
        <Table>
          <TableHeader class="bg-slate-50/50">
            <TableRow>
            <TableHead class="font-bold text-slate-900 py-5 pl-6">Pelanggan</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Treatment & Cabang</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Jadwal</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-center">Status</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-right pr-6">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="isLoading">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
                <p class="text-sm font-medium text-slate-500">Memuat data janji temu...</p>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-else-if="appointments.length === 0">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-4 opacity-40">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                  <CalendarCheck2 class="w-10 h-10" />
                </div>
                <p class="font-bold text-slate-900">Tidak ada janji temu ditemukan.</p>
              </div>
            </TableCell>
          </TableRow>

          <TableRow v-else v-for="app in appointments" :key="app.id" class="group hover:bg-slate-50/50 transition-colors">
            <TableCell class="py-4 pl-6">
              <div class="space-y-0.5">
                <p class="font-black text-slate-900 group-hover:text-gold-600 transition-colors">
                  {{ app.customer_name || app.user?.name }}
                </p>
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                  <Phone class="w-3 h-3" />
                  {{ app.customer_phone || app.user?.phone }}
                </div>
              </div>
            </TableCell>
            <TableCell>
              <div class="space-y-1">
                <p class="font-black text-gold-600">{{ app.treatment?.name }}</p>
                <Badge variant="secondary" class="rounded-xl px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-600 border-none">
                  <Building2 class="w-3 h-3 mr-1 opacity-50" />
                  {{ app.branch?.name }}
                </Badge>
              </div>
            </TableCell>
            <TableCell>
              <div class="space-y-0.5">
                <p class="text-sm font-black text-slate-900">{{ formatDate(app.appointment_date) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                  <Clock class="w-3 h-3" />
                  {{ formatTime(app.appointment_date) }}
                </div>
              </div>
            </TableCell>
            <TableCell class="text-center">
              <Badge 
                class="rounded-full px-3 py-1 text-[10px] font-black uppercase shadow-sm"
                :class="statusBadgeClass(app.status)"
              >
                {{ app.status }}
              </Badge>
            </TableCell>
            <TableCell class="text-right pr-6">
              <div class="flex items-center justify-end gap-2">
                <Select 
                  :model-value="app.status" 
                  @update:model-value="updateStatus(app.id, $event, app.branch_id)"
                  :disabled="app.isUpdating"
                >
                  <SelectTrigger class="h-9 w-[140px] rounded-xl text-xs font-bold bg-slate-50 border-slate-100">
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="pending">Set Pending</SelectItem>
                    <SelectItem value="confirmed">Set Confirmed</SelectItem>
                    <SelectItem value="completed" :disabled="!app.transaction">
                      Set Completed
                    </SelectItem>
                    <SelectItem value="cancelled">Set Cancelled</SelectItem>
                    <SelectItem value="no_show">Set No Show</SelectItem>
                  </SelectContent>
                </Select>

                <Button
                  v-if="['confirmed', 'completed'].includes(app.status) && !app.transaction"
                  @click="goToPOS(app)"
                  class="h-9 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black uppercase shadow-lg shadow-emerald-500/20 gap-2"
                >
                  <Receipt class="w-3.5 h-3.5" />
                  Bayar
                </Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t bg-slate-50/50 flex items-center justify-between">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
          Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
        </p>
        <div class="flex gap-2">
          <Button 
            variant="outline"
            size="sm"
            @click="changePage(pagination.current_page - 1)" 
            :disabled="pagination.current_page === 1"
            class="rounded-xl font-bold h-8"
          >
            <ChevronLeft class="w-4 h-4 mr-1" /> Prev
          </Button>
          <Button 
            variant="outline"
            size="sm"
            @click="changePage(pagination.current_page + 1)" 
            :disabled="pagination.current_page === pagination.last_page"
            class="rounded-xl font-bold h-8"
          >
            Next <ChevronRight class="w-4 h-4 ml-1" />
          </Button>
        </div>
      </div>
    </Card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import client from '@/api/client'
import { 
  CalendarCheck2, Clock, Phone, X, Search, Receipt, 
  Building2, Loader2, ChevronLeft, ChevronRight 
} from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Table, TableHeader, TableBody, TableHead, TableRow, TableCell } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { toast } from 'vue-sonner'

const router = useRouter()

const appointments = ref([])
const isLoading = ref(false)
const pagination = ref({ current_page: 1, last_page: 1 })

const filterStatus = ref('all')
const filterDate = ref('')
const searchQuery = ref('')

const fetchAppointments = async (page = 1) => {
  isLoading.value = true
  try {
    const params = { page }
    if (filterStatus.value && filterStatus.value !== 'all') params.status = filterStatus.value
    if (filterDate.value) params.date = filterDate.value
    if (searchQuery.value) params.search = searchQuery.value

    const res = await client.get('/admin/appointments', { params })
    appointments.value = res.data.data.map(app => ({ ...app, isUpdating: false }))
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page
    }
  } catch (err) {
    toast.error('Gagal memuat data janji temu')
  } finally {
    isLoading.value = false
  }
}

const resetFilters = () => {
  filterStatus.value = 'all'
  filterDate.value = ''
  searchQuery.value = ''
  fetchAppointments(1)
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchAppointments(page)
  }
}

const updateStatus = async (id, newStatus, branchId) => {
  const app = appointments.value.find(a => a.id === id)
  if (!app) return
  
  app.isUpdating = true
  try {
    await client.put(`/admin/appointments/${id}/status`, { status: newStatus })
    app.status = newStatus
    toast.success(`Status diperbarui menjadi ${newStatus}`)

    // When marking as completed → redirect straight to POS
    if (newStatus === 'completed') {
      router.push({ 
        name: 'admin-pos', 
        query: { appointment_id: id, branch_id: branchId || app.branch_id } 
      })
    }
  } catch (err) {
    toast.error(err?.message || 'Gagal memperbarui status')
    fetchAppointments(pagination.value.current_page) // revert
  } finally {
    app.isUpdating = false
  }
}

const goToPOS = (app) => {
  router.push({ 
    name: 'admin-pos', 
    query: { appointment_id: app.id, branch_id: app.branch_id } 
  })
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
  })
}

const formatTime = (dateStr) => {
  return new Date(dateStr).toLocaleTimeString('id-ID', {
    hour: '2-digit', minute: '2-digit'
  })
}

const statusBadgeClass = (status) => {
  switch (status) {
    case 'pending':   return 'bg-amber-500 text-white shadow-amber-500/20'
    case 'confirmed': return 'bg-blue-500 text-white shadow-blue-500/20'
    case 'completed': return 'bg-emerald-500 text-white shadow-emerald-500/20'
    case 'cancelled': return 'bg-rose-500 text-white shadow-rose-500/20'
    case 'no_show':   return 'bg-slate-400 text-white shadow-slate-400/20'
    default:          return 'bg-slate-200 text-slate-500 shadow-none'
  }
}

onMounted(() => {
  fetchAppointments()
})
</script>
