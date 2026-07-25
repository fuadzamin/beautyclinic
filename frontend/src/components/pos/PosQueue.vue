<template>
  <Card class="border-none shadow-sm h-[700px] flex flex-col overflow-hidden bg-white/50 backdrop-blur-sm">
    <!-- Header -->
    <CardHeader class="px-5 py-4 border-b border-slate-100/80 bg-white/50 shrink-0">
      <div class="flex items-center justify-between mb-4">
        <div class="flex flex-col gap-0.5">
          <CardTitle class="text-base font-bold flex items-center gap-2 text-slate-900">
            <Users class="w-4 h-4 text-gold-500" />
            Antrian Reservasi
          </CardTitle>
          <CardDescription class="text-[11px]">Siap untuk pembayaran hari ini</CardDescription>
        </div>
        <Badge v-if="appointments.length" variant="secondary" class="bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-lg border-none">
          {{ filteredAppointments.length }}/{{ appointments.length }}
        </Badge>
      </div>

      <!-- Search bar -->
      <div class="relative">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-muted-foreground" />
        <Input
          :value="search"
          @input="$emit('update:search', $event.target.value)"
          placeholder="Cari nama atau layanan..."
          class="pl-9 pr-8 h-10 rounded-xl bg-white/80 border-slate-200 focus:ring-gold-500/10 focus:border-gold-500 transition-all text-sm"
        />
        <Button 
          v-if="search" 
          variant="ghost" 
          size="icon" 
          class="absolute right-1 top-1/2 -translate-y-1/2 h-8 w-8 text-slate-400 hover:text-slate-600 rounded-lg"
          @click="$emit('update:search', '')"
        >
          <X class="w-3.5 h-3.5" />
        </Button>
      </div>
    </CardHeader>

    <CardContent class="p-0 flex-1 overflow-hidden flex flex-col bg-white/30">
      <div v-if="!selectedBranch" class="flex flex-col items-center justify-center h-full p-8 text-center gap-3">
        <div class="w-16 h-16 rounded-3xl bg-slate-50 flex items-center justify-center">
          <MapPin class="w-8 h-8 text-slate-300" />
        </div>
        <div class="space-y-1">
          <p class="text-sm font-bold text-slate-700">Pilih Cabang</p>
          <p class="text-xs text-muted-foreground">Silakan pilih cabang terlebih dahulu untuk melihat antrian.</p>
        </div>
      </div>

      <div v-else-if="loading" class="flex flex-col items-center justify-center h-full p-8 gap-3">
        <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
        <p class="text-xs font-medium text-slate-500">Memuat antrian...</p>
      </div>

      <div v-else-if="!appointments.length" class="flex flex-col items-center justify-center h-full p-8 text-center gap-4">
        <div class="w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center">
          <Sparkles class="w-10 h-10 text-emerald-500" />
        </div>
        <div class="space-y-1">
          <p class="text-sm font-bold text-slate-800">Antrian Kosong!</p>
          <p class="text-xs text-muted-foreground">Semua reservasi telah diproses atau belum ada reservasi hari ini.</p>
        </div>
      </div>

      <div v-else-if="search && !filteredAppointments.length" class="flex flex-col items-center justify-center h-full p-8 text-center gap-3">
        <Search class="w-12 h-12 text-slate-200" />
        <div class="space-y-1">
          <p class="text-sm font-semibold text-slate-700">Hasil tidak ditemukan</p>
          <p class="text-xs text-muted-foreground">Pencarian "{{ search }}" tidak cocok dengan data apapun.</p>
        </div>
        <Button variant="link" size="sm" class="text-gold-600 font-bold" @click="$emit('update:search', '')">
          Hapus Pencarian
        </Button>
      </div>

      <div v-else class="divide-y divide-slate-100/50 overflow-y-auto h-full scrollbar-thin scrollbar-thumb-slate-200">
        <button 
          v-for="appt in filteredAppointments" 
          :key="appt.id"
          @click="$emit('select', appt)"
          :class="[
            'w-full text-left px-5 py-4 hover:bg-gold-50/40 transition-all duration-300 group relative',
            selectedId === appt.id ? 'bg-gold-50/80' : ''
          ]"
        >
          <div v-if="selectedId === appt.id" class="absolute left-0 top-0 bottom-0 w-1.5 bg-gold-500 rounded-r-full animate-in slide-in-from-left duration-300"></div>
          
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0 space-y-1">
              <p class="font-bold text-slate-900 group-hover:text-gold-700 truncate transition-colors">
                {{ appt.customer_name || appt.user?.name }}
              </p>
              <div class="flex items-center gap-2">
                <p class="text-xs font-medium text-slate-600 truncate">{{ appt.treatment?.name }}</p>
                <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                <p class="text-[10px] font-bold text-gold-600 uppercase tracking-tight">Rp {{ formatPrice(appt.treatment?.price) }}</p>
              </div>
              <div class="flex items-center gap-3 pt-1 text-[10px] text-muted-foreground font-medium">
                <span class="flex items-center gap-1"><Clock class="w-3 h-3" /> {{ formatTime(appt.appointment_date) }}</span>
                <span class="flex items-center gap-1"><User class="w-3 h-3" /> {{ appt.staff?.name || 'No staff' }}</span>
              </div>
            </div>
            
            <div class="flex flex-col items-end gap-2">
              <div class="p-2 rounded-xl bg-white border border-slate-100 text-slate-400 group-hover:bg-gold-500 group-hover:text-white transition-all duration-300">
                <ChevronRight class="w-4 h-4" />
              </div>
            </div>
          </div>
        </button>
      </div>
    </CardContent>
  </Card>
</template>

<script setup>
import { 
  Users, Search, X, MapPin, Loader2, Clock, 
  User, ChevronRight, Sparkles 
} from 'lucide-vue-next'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

defineProps({
  appointments: { type: Array, default: () => [] },
  filteredAppointments: { type: Array, default: () => [] },
  search: { type: String, default: '' },
  selectedId: Number,
  selectedBranch: [String, Number],
  loading: Boolean
})

defineEmits(['update:search', 'select'])

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)
const formatTime  = (d) => d ? new Date(d).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : ''
</script>
