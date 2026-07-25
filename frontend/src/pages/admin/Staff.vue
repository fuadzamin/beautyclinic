<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <Users2 class="w-6 h-6" />
          </div>
          Manajemen Staff
        </h1>
        <p class="text-slate-500 text-sm font-medium">Tambah atau kelola akun administrator dan hak akses role.</p>
      </div>
      
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
          <Search class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
          <Input 
            v-model="searchQuery" 
            placeholder="Cari nama staff..." 
            class="h-11 pl-11 rounded-2xl bg-white border-slate-100 shadow-sm focus:ring-gold-500" 
          />
        </div>
        <Button @click="openModal()" class="h-11 px-6 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-xl shadow-slate-900/10 gap-2 whitespace-nowrap">
          <Plus class="w-4 h-4" /> Tambah Staff
        </Button>
      </div>
    </div>

    <!-- Table -->
    <Card class="border-none shadow-sm overflow-hidden bg-white/50 backdrop-blur-sm">
      <div class="overflow-x-auto w-full">
        <Table>
          <TableHeader class="bg-slate-50/50">
            <TableRow>
              <TableHead class="font-bold text-slate-900 py-5 pl-6">Staff Member</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Info Kontak</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Role & Cabang</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-center">Status</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-right pr-6">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="isLoading">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
                <p class="text-sm font-medium text-slate-500">Memuat data staff...</p>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-else-if="filteredStaff.length === 0">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-4 opacity-40">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                  <Users2 class="w-10 h-10" />
                </div>
                <p class="font-bold text-slate-900">Belum ada data staff.</p>
              </div>
            </TableCell>
          </TableRow>

          <TableRow v-else v-for="member in filteredStaff" :key="member.id" class="group hover:bg-slate-50/50 transition-colors">
            <TableCell class="py-4 pl-6">
              <div class="flex items-center gap-4">
                <div class="relative">
                  <Avatar class="w-11 h-11 border-2 border-white shadow-sm ring-2 ring-slate-100 group-hover:ring-gold-200 transition-all">
                    <AvatarFallback class="bg-gold-100 text-gold-600 font-black text-lg">
                      {{ member.name.charAt(0).toUpperCase() }}
                    </AvatarFallback>
                  </Avatar>
                  <div v-if="member.is_active" class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                </div>
                <div class="space-y-0.5">
                  <p class="font-black text-slate-900 group-hover:text-gold-600 transition-colors">{{ member.name }}</p>
                  <p class="text-[10px] font-bold text-slate-400 uppercase">Login: {{ member.last_login ? formatDate(member.last_login) : 'Belum Pernah' }}</p>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <div class="space-y-1">
                <p class="text-sm font-bold text-slate-800">{{ member.email }}</p>
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                  <Phone class="w-3 h-3" />
                  {{ member.phone || '-' }}
                </div>
              </div>
            </TableCell>
            <TableCell>
              <div class="space-y-1.5">
                <Badge variant="secondary" class="rounded-xl px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border-none">
                  {{ member.role.replace('_', ' ') }}
                </Badge>
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                  <Building2 class="w-3 h-3 opacity-50" />
                  {{ member.branch ? member.branch.name : 'Akses Global' }}
                </div>
              </div>
            </TableCell>
            <TableCell class="text-center">
              <Badge 
                class="rounded-full px-3 py-1 text-[10px] font-black uppercase shadow-sm"
                :class="member.is_active && !member.deleted_at ? 'bg-emerald-500 text-white shadow-emerald-500/20' : 'bg-rose-500 text-white shadow-rose-500/20'"
              >
                {{ member.is_active && !member.deleted_at ? 'Aktif' : 'Non-aktif' }}
              </Badge>
            </TableCell>
            <TableCell class="text-right pr-6">
              <div class="flex items-center justify-end gap-1">
                <Button 
                  v-if="!member.deleted_at"
                  variant="ghost" 
                  size="icon"
                  @click="openModal(member)" 
                  class="h-9 w-9 rounded-xl hover:bg-blue-50 hover:text-blue-600 text-slate-400 transition-all"
                  title="Edit Staff"
                >
                  <Edit class="w-4 h-4" />
                </Button>
                <Button 
                  v-if="!member.deleted_at && member.id !== auth.user?.id"
                  variant="ghost" 
                  size="icon"
                  @click="deactivateStaff(member.id)" 
                  class="h-9 w-9 rounded-xl hover:bg-rose-50 hover:text-rose-600 text-slate-400 transition-all"
                  title="Nonaktifkan Akun"
                >
                  <Trash2 class="w-4 h-4" />
                </Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
        </Table>
      </div>
    </Card>

    <!-- Staff Form Dialog -->
    <Dialog v-model:open="isModalOpen">
      <DialogContent class="sm:max-w-lg p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <div class="p-8 border-b bg-slate-50/50 flex items-center justify-between">
          <div class="space-y-1">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
              {{ form.id ? 'Edit Akun Staff' : 'Buat Akun Staff Baru' }}
            </h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Identitas & Role</p>
          </div>
          <div class="p-3 rounded-2xl bg-white shadow-sm text-gold-500">
            <Users2 class="w-6 h-6" />
          </div>
        </div>
        
        <form @submit.prevent="saveStaff" class="p-8 space-y-6 max-h-[70vh] overflow-y-auto">
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Nama Lengkap</Label>
              <Input v-model="form.name" placeholder="Contoh: Siti Aminah" class="h-12 rounded-2xl bg-slate-50 border-slate-100" required />
            </div>
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Nomor Telepon</Label>
              <Input v-model="form.phone" placeholder="Contoh: 0812..." class="h-12 rounded-2xl bg-slate-50 border-slate-100" />
            </div>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Email Address</Label>
            <div class="relative">
              <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
              <Input v-model="form.email" type="email" placeholder="email@contoh.com" class="h-12 pl-11 rounded-2xl bg-slate-50 border-slate-100" required />
            </div>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Password</Label>
            <div class="relative">
              <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
              <Input 
                v-model="form.password" 
                type="password" 
                class="h-12 pl-11 rounded-2xl bg-slate-50 border-slate-100" 
                :required="!form.id" 
                placeholder="Kosongkan jika tidak diubah" 
                minlength="8"
              />
            </div>
            <p class="text-[10px] font-bold text-slate-400 uppercase ml-1">Minimal 8 karakter dengan kombinasi angka.</p>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Akses Role</Label>
            <Select v-model="form.role">
              <SelectTrigger class="h-12 rounded-2xl bg-slate-50 border-slate-100">
                <SelectValue placeholder="Pilih Role" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="admin_klinik">Admin Klinik (Janji Temu)</SelectItem>
                <SelectItem value="admin_produk">Admin Produk (Stok & Toko)</SelectItem>
                <SelectItem value="branch_manager">Branch Manager (Kepala Cabang)</SelectItem>
                <SelectItem value="owner">Owner (Akses Penuh)</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <!-- Branch Selection -->
          <div v-if="form.role !== 'owner'" class="space-y-2 animate-in slide-in-from-top-2 duration-300">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Cabang Penempatan</Label>
            <Select v-model="form.branch_id">
              <SelectTrigger class="h-12 rounded-2xl bg-slate-50 border-slate-100">
                <SelectValue placeholder="Pilih Cabang" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="branch in branches" :key="branch.id" :value="branch.id">
                  {{ branch.name }}
                </SelectItem>
              </SelectContent>
            </Select>
            <p class="text-[10px] font-bold text-slate-400 uppercase ml-1">Staff hanya akan mengelola data pada cabang terpilih.</p>
          </div>

          <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
            <Label for="isActive" class="text-sm font-bold text-slate-700">Akun Aktif</Label>
            <Switch id="isActive" :checked="form.is_active" @update:checked="form.is_active = $event" />
          </div>

          <!-- Validation Errors -->
          <div v-if="validationErrors" class="p-5 bg-rose-50 rounded-2xl border border-rose-100 space-y-2">
            <div class="flex items-center gap-2 text-rose-600 font-black text-xs uppercase tracking-wider">
              <AlertCircle class="w-4 h-4" /> Ada Kesalahan Input
            </div>
            <ul class="space-y-1 pl-6">
              <li v-for="(errors, field) in validationErrors" :key="field" class="text-[11px] font-bold text-rose-500 leading-relaxed">
                {{ errors[0] }}
              </li>
            </ul>
          </div>

          <div class="flex gap-4 pt-4 sticky bottom-0 bg-white pt-6">
            <Button type="button" variant="ghost" class="flex-1 h-14 rounded-2xl font-bold text-slate-500" @click="closeModal">Batal</Button>
            <Button type="submit" class="flex-1 h-14 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-lg shadow-xl shadow-gold-500/20" :disabled="isSaving">
              <Loader2 v-if="isSaving" class="mr-2 w-5 h-5 animate-spin" />
              {{ isSaving ? 'Menyimpan...' : 'Simpan Akun' }}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import client from '@/api/client'
import { useAuthStore } from '@/stores/authStore'
import { 
  Plus, Edit3 as Edit, Trash2, X, Search, Users2, Mail, 
  Lock, Phone, Building2, Loader2, AlertCircle
} from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Table, TableHeader, TableBody, TableHead, TableRow, TableCell } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Switch } from '@/components/ui/switch'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()
const staffList = ref([])
const branches = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const searchQuery = ref('')
const validationErrors = ref(null)

const isModalOpen = ref(false)
const form = ref({
  id: null,
  name: '',
  email: '',
  phone: '',
  password: '',
  role: 'admin_klinik',
  branch_id: null,
  is_active: true
})

const filteredStaff = computed(() => {
  if (!searchQuery.value) return staffList.value
  const q = searchQuery.value.toLowerCase()
  return staffList.value.filter(s => 
    s.name.toLowerCase().includes(q) || 
    s.email.toLowerCase().includes(q)
  )
})

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

const fetchStaff = async () => {
  isLoading.value = true
  try {
    const res = await client.get('/admin/staff')
    staffList.value = res.data || []
  } catch (err) {
    if (err?.response?.status === 403) {
      router.push('/admin/dashboard')
    } else {
      toast.error('Gagal memuat data staff')
    }
  } finally {
    isLoading.value = false
  }
}

const fetchBranches = async () => {
  try {
    const res = await client.get('/branches')
    branches.value = res.data || []
  } catch (err) {
    console.error('Failed to load branches', err)
  }
}

const openModal = (member = null) => {
  validationErrors.value = null
  if (member) {
    form.value = { 
      ...member, 
      password: '',
      is_active: member.is_active === 1 || member.is_active === true
    }
  } else {
    form.value = {
      id: null,
      name: '',
      email: '',
      phone: '',
      password: '',
      role: 'admin_klinik',
      branch_id: null,
      is_active: true
    }
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  validationErrors.value = null
}

const saveStaff = async () => {
  isSaving.value = true
  validationErrors.value = null
  try {
    const payload = { ...form.value }
    if (payload.id && !payload.password) {
      delete payload.password
    }

    if (payload.id) {
      await client.put(`/admin/staff/${payload.id}`, payload)
      toast.success('Akun staff berhasil diperbarui')
    } else {
      await client.post('/admin/staff', payload)
      toast.success('Akun staff baru berhasil dibuat')
    }
    
    closeModal()
    fetchStaff()
  } catch (err) {
    if (err.response?.data?.errors) {
      validationErrors.value = err.response.data.errors
      toast.error('Gagal menyimpan: Periksa input Anda')
    } else {
      toast.error(err.message || 'Gagal menyimpan akun staff')
    }
  } finally {
    isSaving.value = false
  }
}

const deactivateStaff = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menonaktifkan akun ini? Akses akan dicabut segera.')) return
  try {
    await client.delete(`/admin/staff/${id}`)
    toast.success('Akun berhasil dinonaktifkan')
    fetchStaff()
  } catch (err) {
    toast.error('Gagal menonaktifkan staff')
  }
}

onMounted(() => {
  if (!auth.isOwner) {
    router.push('/admin/dashboard')
    return
  }
  fetchStaff()
  fetchBranches()
})
</script>
