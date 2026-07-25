<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Controls bar -->
    <Card class="border-none shadow-sm bg-white/50 backdrop-blur-sm">
      <CardContent class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-xl bg-gold-50 text-gold-600">
            <History class="w-5 h-5" />
          </div>
          <div class="flex flex-col">
            <CardTitle class="text-lg">Riwayat Transaksi</CardTitle>
            <CardDescription v-if="!loading" class="text-xs">
              Menampilkan {{ meta.total }} transaksi hari ini
            </CardDescription>
          </div>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
          <Label class="text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:block">Filter Tanggal</Label>
          <Input 
            type="date" 
            :value="date" 
            @input="$emit('update:date', $event.target.value)" 
            class="h-10 w-full sm:w-48 rounded-xl bg-white border-slate-200 focus:ring-gold-500/10" 
          />
        </div>
      </CardContent>
    </Card>

    <!-- Summary stats -->
    <div v-if="!loading" class="grid grid-cols-2 xl:grid-cols-4 gap-4">
      <Card v-for="stat in summaryCards" :key="stat.label" class="border-none shadow-sm hover:shadow-md transition-all duration-300 group">
        <CardContent class="p-4 flex items-center gap-4">
          <div :class="['p-3 rounded-2xl transition-colors duration-300', stat.colorClass]">
            <component :is="stat.icon" class="w-5 h-5" />
          </div>
          <div class="flex flex-col">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ stat.label }}</p>
            <p class="text-lg font-black text-slate-900">{{ stat.value }}</p>
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Table -->
    <Card class="border-none shadow-sm overflow-hidden">
      <div class="overflow-x-auto w-full">
        <Table>
          <TableHeader class="bg-slate-50/50">
            <TableRow>
              <TableHead class="font-bold text-slate-900 pl-6">No. Transaksi</TableHead>
            <TableHead class="font-bold text-slate-900">Pelanggan</TableHead>
            <TableHead class="font-bold text-slate-900">Layanan/Produk</TableHead>
            <TableHead class="font-bold text-slate-900 text-right">Total Bayar</TableHead>
            <TableHead class="font-bold text-slate-900 text-center">Metode</TableHead>
            <TableHead class="font-bold text-slate-900">Waktu</TableHead>
            <TableHead class="font-bold text-slate-900 text-right">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="loading">
            <TableCell colspan="7" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
                <p class="text-sm font-medium text-slate-500">Memuat data riwayat...</p>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-else-if="!data.length">
            <TableCell colspan="7" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-4 opacity-40">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                  <History class="w-10 h-10" />
                </div>
                <div class="space-y-1">
                  <p class="font-bold text-slate-900">Tidak ada transaksi</p>
                  <p class="text-xs">pada {{ new Date(date).toLocaleDateString('id-ID', {dateStyle: 'long'}) }}</p>
                </div>
              </div>
            </TableCell>
          </TableRow>

          <TableRow v-else v-for="trx in data" :key="trx.id" class="group hover:bg-slate-50/50 transition-colors">
            <TableCell class="pl-6">
              <p class="font-mono text-xs font-bold text-gold-600">{{ trx.transaction_number || `TRX-${trx.id.toString().padStart(6, '0')}` }}</p>
              <p class="text-[10px] text-slate-400 mt-0.5">ID: #{{ trx.id }}</p>
            </TableCell>
            <TableCell>
              <div class="flex flex-col">
                <p class="font-bold text-slate-900 text-sm">{{ trx.customer_name }}</p>
                <p class="text-[11px] text-slate-500">{{ trx.customer_phone || '—' }}</p>
              </div>
            </TableCell>
            <TableCell class="max-w-[180px]">
              <p v-if="trx.appointment?.treatment" class="text-sm font-medium text-slate-700 truncate">
                {{ trx.appointment.treatment.name }}
              </p>
              <p v-if="trx.items?.length" class="text-[10px] font-bold text-emerald-600 mt-0.5 flex items-center gap-1">
                <Package class="w-3 h-3" /> + {{ trx.items.length }} Produk
              </p>
              <p v-if="!trx.appointment?.treatment && !trx.items?.length" class="text-xs text-slate-400 italic">Walk-in</p>
            </TableCell>
            <TableCell class="text-right">
              <p class="font-black text-slate-900">Rp {{ formatPrice(trx.grand_total) }}</p>
              <p v-if="trx.discount" class="text-[10px] font-bold text-rose-500">- Rp {{ formatPrice(trx.discount) }}</p>
            </TableCell>
            <TableCell class="text-center">
              <Badge :class="['rounded-lg px-2 py-0.5 text-[10px] font-black uppercase border-none', getMethodClass(trx.payment_method)]">
                {{ trx.payment_method }}
              </Badge>
            </TableCell>
            <TableCell>
              <p class="text-xs font-medium text-slate-600">{{ formatDateTime(trx.created_at) }}</p>
            </TableCell>
            <TableCell class="text-right">
              <Button variant="ghost" size="icon" @click="$emit('print', trx)" class="h-9 w-9 rounded-xl hover:bg-gold-50 hover:text-gold-600 text-slate-400 transition-all">
                <Printer class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <CardFooter v-if="meta.last_page > 1" class="px-6 py-4 border-t bg-slate-50/30 flex items-center justify-between">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
          Halaman {{ meta.current_page }} dari {{ meta.last_page }}
        </p>
        <div class="flex gap-2">
          <Button
            variant="outline"
            size="sm"
            class="rounded-xl h-9 px-4 border-slate-200"
            :disabled="meta.current_page <= 1"
            @click="$emit('page-change', meta.current_page - 1)"
          >
            <ChevronLeft class="w-4 h-4 mr-1" /> Prev
          </Button>
          <Button
            variant="outline"
            size="sm"
            class="rounded-xl h-9 px-4 border-slate-200"
            :disabled="meta.current_page >= meta.last_page"
            @click="$emit('page-change', meta.current_page + 1)"
          >
            Next <ChevronRight class="w-4 h-4 ml-1" />
          </Button>
        </div>
      </CardFooter>
    </Card>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
  Loader2, History, Printer, Package, ChevronLeft, 
  ChevronRight, CreditCard, Banknote, TrendingUp, DollarSign
} from 'lucide-vue-next'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Table, TableHeader, TableBody, TableHead, TableRow, TableCell } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const props = defineProps({
  loading: Boolean,
  data: { type: Array, default: () => [] },
  meta: { type: Object, default: () => ({ total: 0, current_page: 1, last_page: 1 }) },
  summary: { type: Object, default: () => ({ total_transactions: 0, total_revenue: 0, average_value: 0, top_payment_method: '—' }) },
  date: String
})

defineEmits(['update:date', 'page-change', 'print'])

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)
const formatDateTime = (d) => d ? new Date(d).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : ''

const summaryCards = computed(() => [
  { 
    label: 'Total Transaksi', 
    value: props.summary.total_transactions, 
    icon: History,
    colorClass: 'bg-gold-50 text-gold-600 group-hover:bg-gold-500 group-hover:text-white',
  },
  { 
    label: 'Total Pendapatan', 
    value: `Rp ${formatPrice(props.summary.total_revenue)}`, 
    icon: DollarSign,
    colorClass: 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white',
  },
  { 
    label: 'Rata-rata', 
    value: `Rp ${formatPrice(props.summary.average_value)}`, 
    icon: TrendingUp,
    colorClass: 'bg-blue-50 text-blue-600 group-hover:bg-blue-500 group-hover:text-white',
  },
  { 
    label: 'Metode Utama', 
    value: props.summary.top_payment_method, 
    icon: CreditCard,
    colorClass: 'bg-slate-100 text-slate-600 group-hover:bg-slate-900 group-hover:text-white',
  }
])

const getMethodClass = (method) => {
  const m = method?.toLowerCase()
  if (m === 'cash') return 'bg-emerald-100 text-emerald-700'
  if (m === 'transfer') return 'bg-blue-100 text-blue-700'
  if (m === 'qris') return 'bg-purple-100 text-purple-700'
  return 'bg-slate-100 text-slate-700'
}
</script>
