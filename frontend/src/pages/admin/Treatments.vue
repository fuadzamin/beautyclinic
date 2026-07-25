<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <Sparkles class="w-6 h-6" />
          </div>
          Layanan Treatment
        </h1>
        <p class="text-slate-500 text-sm font-medium">Kelola daftar layanan treatment dan ketersediaan di setiap cabang.</p>
      </div>
      
      <Button @click="openModal()" class="h-11 px-6 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-xl shadow-slate-900/10 gap-2 w-full sm:w-auto">
        <Plus class="w-4 h-4" /> Tambah Treatment
      </Button>
    </div>

    <!-- Main Content -->
    <Card class="border-none shadow-sm overflow-hidden bg-white/50 backdrop-blur-sm">
      <div class="overflow-x-auto w-full">
        <Table>
          <TableHeader class="bg-slate-50/50">
            <TableRow>
              <TableHead class="font-bold text-slate-900 py-5 pl-6">Treatment</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Harga & Durasi</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Tersedia Di</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-center">Status</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-right pr-6">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="isLoading">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
                <p class="text-sm font-medium text-slate-500">Memuat data treatment...</p>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-else-if="treatments.length === 0">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-4 opacity-40">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                  <Sparkles class="w-10 h-10" />
                </div>
                <p class="font-bold text-slate-900">Belum ada data treatment.</p>
              </div>
            </TableCell>
          </TableRow>

          <TableRow v-else v-for="treatment in treatments" :key="treatment.id" class="group hover:bg-slate-50/50 transition-colors">
            <TableCell class="py-4 pl-6">
              <div class="space-y-0.5">
                <p class="font-black text-slate-900 group-hover:text-gold-600 transition-colors">{{ treatment.name }}</p>
                <Badge variant="outline" class="text-[10px] uppercase font-bold text-slate-400 border-slate-200">
                  {{ treatment.category }}
                </Badge>
              </div>
            </TableCell>
            <TableCell>
              <div class="space-y-0.5">
                <p class="text-lg font-black text-slate-900">Rp {{ formatPrice(treatment.price) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                  <Clock class="w-3 h-3" />
                  {{ treatment.duration_minutes }} Menit
                </div>
              </div>
            </TableCell>
            <TableCell>
              <div class="flex flex-wrap gap-1.5 max-w-xs">
                <Badge 
                  v-for="b in treatment.branches" :key="b.id"
                  variant="secondary"
                  class="rounded-xl px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-600 border-none"
                >
                  <Building2 class="w-3 h-3 mr-1 opacity-50" />
                  {{ b.name }}
                </Badge>
                <p v-if="!treatment.branches?.length" class="text-xs text-slate-400 italic">Belum ditentukan</p>
              </div>
            </TableCell>
            <TableCell class="text-center">
              <Badge 
                class="rounded-full px-3 py-1 text-[10px] font-black uppercase"
                :class="treatment.is_active ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-slate-200 text-slate-500'"
              >
                {{ treatment.is_active ? 'Aktif' : 'Non-aktif' }}
              </Badge>
            </TableCell>
            <TableCell class="text-right pr-6">
              <Button variant="ghost" size="icon" @click="openModal(treatment)" class="h-9 w-9 rounded-xl hover:bg-blue-50 hover:text-blue-600 text-slate-400 transition-all" title="Edit Treatment">
                <Edit class="w-4 h-4" />
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
        </Table>
      </div>
    </Card>

    <!-- Treatment Form Dialog -->
    <Dialog v-model:open="isModalOpen">
      <DialogContent class="sm:max-w-2xl p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <div class="p-8 border-b bg-slate-50/50 flex items-center justify-between">
          <div class="space-y-1">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
              {{ form.id ? 'Edit Treatment' : 'Tambah Treatment Baru' }}
            </h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Layanan & Cabang</p>
          </div>
          <div class="p-3 rounded-2xl bg-white shadow-sm text-gold-500">
            <Sparkles class="w-6 h-6" />
          </div>
        </div>
        
        <form @submit.prevent="saveTreatment" class="p-8 space-y-6 max-h-[70vh] overflow-y-auto">
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Nama Treatment</Label>
              <Input v-model="form.name" placeholder="Contoh: Glowing Facial" class="h-12 rounded-2xl bg-slate-50 border-slate-100" required />
            </div>
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Kategori</Label>
              <Select v-model="form.category">
                <SelectTrigger class="h-12 rounded-2xl bg-slate-50 border-slate-100">
                  <SelectValue placeholder="Pilih Kategori" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="cat in categories" :key="cat.value" :value="cat.value">{{ cat.label }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Harga (Rp)</Label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">Rp</span>
                <Input v-model.number="form.price" type="number" min="0" class="h-12 pl-12 rounded-2xl bg-slate-50 border-slate-100 font-black text-lg" required />
              </div>
            </div>
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Durasi (Menit)</Label>
              <div class="relative">
                <Input v-model.number="form.duration_minutes" type="number" min="15" step="15" class="h-12 rounded-2xl bg-slate-50 border-slate-100 font-black text-lg" required />
                <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs">Menit</span>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Deskripsi</Label>
            <Textarea v-model="form.description" class="rounded-2xl bg-slate-50 border-slate-100 h-24 resize-none" placeholder="Jelaskan detail treatment, manfaat, dan tahapan proses..." />
          </div>

          <!-- Branch Selection -->
          <div class="space-y-3">
            <div class="flex items-center justify-between ml-1">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide">Ketersediaan Cabang</Label>
              <Badge variant="outline" class="text-[10px] font-bold border-slate-200">
                {{ form.branch_ids.length }} Cabang Terpilih
              </Badge>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
              <div 
                v-for="b in allBranches" :key="b.id" 
                class="flex items-center space-x-3 p-2 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-gold-500 transition-colors"
              >
                <Checkbox 
                  :id="'branch-' + b.id" 
                  :checked="form.branch_ids.includes(b.id)"
                  @update:checked="(checked) => toggleBranch(b.id, checked)"
                  class="rounded-md border-slate-300 text-gold-500 focus:ring-gold-500"
                />
                <Label :for="'branch-' + b.id" class="text-xs font-bold text-slate-700 cursor-pointer line-clamp-1">
                  {{ b.name }}
                </Label>
              </div>
            </div>
            <p v-if="!form.branch_ids.length" class="text-[10px] font-bold text-rose-500 ml-1 flex items-center gap-1">
              <AlertCircle class="w-3 h-3" /> Wajib memilih minimal satu cabang.
            </p>
          </div>

          <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
            <Label for="isActive" class="text-sm font-bold text-slate-700">Treatment Aktif</Label>
            <Switch id="isActive" :checked="form.is_active" @update:checked="form.is_active = $event" />
          </div>

          <div class="flex gap-4 pt-4 sticky bottom-0 bg-white">
            <Button type="button" variant="ghost" class="flex-1 h-14 rounded-2xl font-bold text-slate-500" @click="closeModal">Batal</Button>
            <Button type="submit" class="flex-1 h-14 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-lg shadow-xl shadow-gold-500/20" :disabled="isSaving || !form.branch_ids.length">
              <Loader2 v-if="isSaving" class="mr-2 w-5 h-5 animate-spin" />
              {{ isSaving ? 'Menyimpan...' : 'Simpan Layanan' }}
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
import { 
  Plus, Edit3 as Edit, X, Sparkles, Clock, Building2, 
  Loader2, AlertCircle
} from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Table, TableHeader, TableBody, TableHead, TableRow, TableCell } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Textarea } from '@/components/ui/textarea'
import { Switch } from '@/components/ui/switch'
import { Checkbox } from '@/components/ui/checkbox'
import { toast } from 'vue-sonner'

const treatments = ref([])
const allBranches = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const isModalOpen = ref(false)

const categories = [
  { value: 'facial', label: 'Facial' },
  { value: 'laser', label: 'Laser' },
  { value: 'peeling', label: 'Peeling' },
  { value: 'injection', label: 'Injection' },
  { value: 'hair_removal', label: 'Hair Removal' },
  { value: 'other', label: 'Other' }
]

const form = ref({
  id: null,
  name: '',
  category: 'facial',
  price: 0,
  duration_minutes: 30,
  description: '',
  branch_ids: [],
  is_active: true
})

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)

const fetchTreatments = async () => {
  isLoading.value = true
  try {
    const res = await client.get('/treatments', { params: { show_all: 1 } })
    treatments.value = res.data || []
  } catch (err) {
    toast.error('Gagal memuat data treatment')
    treatments.value = []
  } finally {
    isLoading.value = false
  }
}

const fetchBranches = async () => {
  try {
    const res = await client.get('/admin/branches')
    allBranches.value = res.data || []
  } catch (err) {
    console.error('Failed to load branches', err)
  }
}

const toggleBranch = (branchId, checked) => {
  if (checked) {
    if (!form.value.branch_ids.includes(branchId)) {
      form.value.branch_ids.push(branchId)
    }
  } else {
    form.value.branch_ids = form.value.branch_ids.filter(id => id !== branchId)
  }
}

const openModal = (treatment = null) => {
  if (treatment && treatment.id) {
    form.value = { 
      ...treatment, 
      branch_ids: treatment.branches?.map(b => b.id) || [],
      is_active: treatment.is_active === 1 || treatment.is_active === true
    }
  } else {
    form.value = {
      id: null,
      name: '',
      category: 'facial',
      price: 0,
      duration_minutes: 30,
      description: '',
      branch_ids: [],
      is_active: true
    }
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const saveTreatment = async () => {
  isSaving.value = true
  try {
    if (form.value.id) {
      await client.put(`/admin/treatments/${form.value.id}`, form.value)
      toast.success('Treatment berhasil diperbarui')
    } else {
      await client.post('/admin/treatments', form.value)
      toast.success('Treatment baru berhasil ditambahkan')
    }
    closeModal()
    fetchTreatments()
  } catch (err) {
    toast.error(err?.message || 'Gagal menyimpan treatment')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchTreatments()
  fetchBranches()
})
</script>
