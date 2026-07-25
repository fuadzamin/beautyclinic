<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <MapPin class="w-6 h-6" />
          </div>
          Cabang Klinik
        </h1>
        <p class="text-slate-500 text-sm font-medium">Kelola lokasi fisik klinik dan informasi kontak.</p>
      </div>
      
      <Button @click="openModal()" class="h-11 px-6 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-xl shadow-slate-900/10 gap-2 w-full sm:w-auto">
        <Plus class="w-4 h-4" /> Tambah Cabang
      </Button>
    </div>

    <!-- Main Content -->
    <Card class="border-none shadow-sm overflow-hidden bg-white/50 backdrop-blur-sm">
      <div class="overflow-x-auto w-full">
        <Table>
          <TableHeader class="bg-slate-50/50">
            <TableRow>
              <TableHead class="font-bold text-slate-900 py-5 pl-6">Nama Cabang</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Alamat & Telepon</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-center">Status</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-right pr-6">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="isLoading">
            <TableCell colspan="4" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
                <p class="text-sm font-medium text-slate-500">Memuat data cabang...</p>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-else-if="branches.length === 0">
            <TableCell colspan="4" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-4 opacity-40">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                  <MapPin class="w-10 h-10" />
                </div>
                <p class="font-bold text-slate-900">Belum ada data cabang.</p>
              </div>
            </TableCell>
          </TableRow>

          <TableRow v-else v-for="branch in branches" :key="branch.id" class="group hover:bg-slate-50/50 transition-colors">
            <TableCell class="py-4 pl-6">
              <p class="font-black text-slate-900 group-hover:text-gold-600 transition-colors">{{ branch.name }}</p>
            </TableCell>
            <TableCell>
              <div class="space-y-1 max-w-md">
                <p class="text-sm font-medium text-slate-800 line-clamp-2 leading-relaxed">{{ branch.address }}</p>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                  <Phone class="w-3 h-3" />
                  {{ branch.phone }}
                </div>
              </div>
            </TableCell>
            <TableCell class="text-center">
              <Badge 
                class="rounded-full px-3 py-1 text-[10px] font-black uppercase shadow-sm"
                :class="branch.is_active ? 'bg-emerald-500 text-white shadow-emerald-500/20' : 'bg-slate-200 text-slate-500'"
              >
                {{ branch.is_active ? 'Buka' : 'Tutup' }}
              </Badge>
            </TableCell>
            <TableCell class="text-right pr-6">
              <Button variant="ghost" size="icon" @click="openModal(branch)" class="h-9 w-9 rounded-xl hover:bg-blue-50 hover:text-blue-600 text-slate-400 transition-all" title="Edit Cabang">
                <Edit class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
        </Table>
      </div>
    </Card>

    <!-- Branch Form Dialog -->
    <Dialog v-model:open="isModalOpen">
      <DialogContent class="sm:max-w-lg p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <div class="p-8 border-b bg-slate-50/50 flex items-center justify-between">
          <div class="space-y-1">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
              {{ form.id ? 'Edit Cabang' : 'Tambah Cabang Baru' }}
            </h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Lokasi & Kontak</p>
          </div>
          <div class="p-3 rounded-2xl bg-white shadow-sm text-gold-500">
            <MapPin class="w-6 h-6" />
          </div>
        </div>
        
        <form @submit.prevent="saveBranch" class="p-8 space-y-6">
          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Nama Cabang</Label>
            <Input v-model="form.name" placeholder="Contoh: Aura Beauty - Jakarta Selatan" class="h-12 rounded-2xl bg-slate-50 border-slate-100" required />
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Nomor Telepon</Label>
            <div class="relative">
              <Phone class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
              <Input v-model="form.phone" placeholder="Contoh: 021-1234567" class="h-12 pl-11 rounded-2xl bg-slate-50 border-slate-100" required />
            </div>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Alamat Lengkap</Label>
            <Textarea v-model="form.address" class="rounded-2xl bg-slate-50 border-slate-100 h-32 resize-none leading-relaxed" placeholder="Masukkan alamat lengkap cabang..." required />
          </div>

          <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
            <div class="space-y-0.5">
              <Label for="isActive" class="text-sm font-bold text-slate-700">Status Operasional</Label>
              <p class="text-[10px] font-bold text-slate-400 uppercase">Aktifkan untuk menerima reservasi</p>
            </div>
            <Switch id="isActive" :checked="form.is_active" @update:checked="form.is_active = $event" />
          </div>

          <div class="flex gap-4 pt-4">
            <Button type="button" variant="ghost" class="flex-1 h-14 rounded-2xl font-bold text-slate-500" @click="closeModal">Batal</Button>
            <Button type="submit" class="flex-1 h-14 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-lg shadow-xl shadow-gold-500/20" :disabled="isSaving">
              <Loader2 v-if="isSaving" class="mr-2 w-5 h-5 animate-spin" />
              {{ isSaving ? 'Menyimpan...' : 'Simpan Cabang' }}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import client from '@/api/client'
import { Plus, Edit3 as Edit, X, MapPin, Phone, Loader2 } from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Table, TableHeader, TableBody, TableHead, TableRow, TableCell } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Textarea } from '@/components/ui/textarea'
import { Switch } from '@/components/ui/switch'
import { toast } from 'vue-sonner'

const branches = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const isModalOpen = ref(false)

const form = ref({
  id: null,
  name: '',
  phone: '',
  address: '',
  is_active: true
})

const fetchBranches = async () => {
  isLoading.value = true
  try {
    const res = await client.get('/admin/branches')
    branches.value = res.data || []
  } catch (err) {
    toast.error('Gagal memuat data cabang')
    branches.value = []
  } finally {
    isLoading.value = false
  }
}

const openModal = (branch = null) => {
  if (branch && branch.id) {
    form.value = { 
      ...branch, 
      is_active: branch.is_active === 1 || branch.is_active === true
    }
  } else {
    form.value = {
      id: null,
      name: '',
      phone: '',
      address: '',
      is_active: true
    }
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const saveBranch = async () => {
  isSaving.value = true
  try {
    if (form.value.id) {
      await client.put(`/admin/branches/${form.value.id}`, form.value)
      toast.success('Cabang berhasil diperbarui')
    } else {
      await client.post('/admin/branches', form.value)
      toast.success('Cabang baru berhasil ditambahkan')
    }
    closeModal()
    fetchBranches()
  } catch (err) {
    toast.error(err?.message || 'Gagal menyimpan cabang')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchBranches()
})
</script>
