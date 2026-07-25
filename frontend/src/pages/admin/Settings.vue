<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="space-y-1">
      <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
        <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
          <Settings2 class="w-6 h-6" />
        </div>
        Pengaturan Sistem
      </h1>
      <p class="text-slate-500 text-sm font-medium">Konfigurasi profil klinik, jam operasional, dan keamanan akun.</p>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-6xl">
      <!-- Sidebar Navigation -->
      <div class="lg:col-span-3">
        <nav class="flex flex-row lg:flex-col gap-2 p-1 bg-slate-100/50 rounded-2xl border border-slate-100 overflow-x-auto no-scrollbar">
          <button 
            v-for="tab in tabs" :key="tab.id"
            @click="activeTab = tab.id"
            class="flex-1 lg:flex-none px-4 py-3.5 text-xs font-black uppercase tracking-widest rounded-xl transition-all flex items-center justify-center lg:justify-start gap-3 whitespace-nowrap"
            :class="activeTab === tab.id ? 'bg-white text-gold-600 shadow-sm' : 'text-slate-400 hover:text-slate-600 hover:bg-white/50'"
          >
            <component :is="tab.icon" class="w-4 h-4 shrink-0" />
            {{ tab.name }}
          </button>
        </nav>
      </div>

      <!-- Tab Panels -->
      <div class="lg:col-span-9">
        <Card class="border-none shadow-sm bg-white/50 backdrop-blur-sm overflow-hidden min-h-[500px]">
          
          <!-- General Profile -->
          <div v-show="activeTab === 'general'" class="p-8 space-y-8 animate-in slide-in-from-right-4 duration-500">
            <div class="space-y-1">
              <h2 class="text-xl font-black text-slate-900 tracking-tight">Profil Klinik</h2>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi dasar & Kontak Bisnis</p>
            </div>

            <form @submit.prevent="saveSettings" class="space-y-6">
              <div class="space-y-2">
                <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Nama Klinik</Label>
                <Input v-model="settings.clinicName" placeholder="Masukkan nama klinik..." class="h-12 rounded-2xl bg-white border-slate-100 shadow-sm focus:ring-gold-500" />
              </div>

              <div class="space-y-2">
                <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">WhatsApp Business</Label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 p-1 rounded-lg bg-emerald-50 text-emerald-600">
                    <Phone class="w-4 h-4" />
                  </div>
                  <Input v-model="settings.whatsappNumber" placeholder="Contoh: 6281234567890" class="h-12 pl-12 rounded-2xl bg-white border-slate-100 shadow-sm focus:ring-gold-500" />
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase ml-1">Digunakan untuk notifikasi otomatis pesanan.</p>
              </div>

              <div class="space-y-2">
                <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Alamat Lengkap</Label>
                <Textarea v-model="settings.address" class="rounded-2xl bg-white border-slate-100 shadow-sm h-32 resize-none leading-relaxed focus:ring-gold-500" placeholder="Masukkan alamat fisik klinik..." />
              </div>

              <div class="pt-4">
                <Button type="submit" class="h-12 px-8 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-sm shadow-xl shadow-gold-500/20" :disabled="isSaving">
                  <Loader2 v-if="isSaving" class="mr-2 w-4 h-4 animate-spin" />
                  {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </Button>
              </div>
            </form>
          </div>

          <!-- Operational Hours -->
          <div v-show="activeTab === 'hours'" class="p-8 space-y-8 animate-in slide-in-from-right-4 duration-500">
            <div class="space-y-1">
              <h2 class="text-xl font-black text-slate-900 tracking-tight">Jam Operasional</h2>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Atur waktu buka & tutup klinik</p>
            </div>

              <form @submit.prevent="saveSettings" class="space-y-4">
              <div v-for="(hour, i) in operationalHours" :key="hour.day" class="group flex items-center gap-6 p-4 rounded-2xl bg-slate-50/50 border border-slate-100 hover:bg-white hover:shadow-sm transition-all">
                <span class="w-32 text-xs font-black text-slate-900 uppercase tracking-widest">{{ hour.day }}</span>
                <div class="flex items-center gap-3 flex-1">
                  <Input type="time" v-model="operationalHours[i].open" class="h-10 rounded-xl bg-white border-slate-100 text-center font-bold" />
                  <span class="text-slate-300 font-bold">-</span>
                  <Input type="time" v-model="operationalHours[i].close" class="h-10 rounded-xl bg-white border-slate-100 text-center font-bold" />
                </div>
                <Switch v-model:checked="operationalHours[i].isOpen" />
              </div>

              <div class="pt-6">
                <Button type="submit" class="h-12 px-8 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-sm shadow-xl shadow-gold-500/20" :disabled="isSaving">
                  <Loader2 v-if="isSaving" class="mr-2 w-4 h-4 animate-spin" />
                  {{ isSaving ? 'Menyimpan...' : 'Simpan Jadwal' }}
                </Button>
              </div>
            </form>
          </div>

          <!-- Security & 2FA -->
          <div v-show="activeTab === 'security'" class="p-8 space-y-8 animate-in slide-in-from-right-4 duration-500">
            <div class="space-y-1">
              <h2 class="text-xl font-black text-slate-900 tracking-tight">Keamanan Akun</h2>
              <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Proteksi & Verifikasi Tambahan</p>
            </div>
            
            <!-- 2FA Section -->
            <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-2xl shadow-slate-900/10">
              <div class="flex items-start justify-between mb-6">
                <div class="space-y-2">
                  <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-500/20 text-gold-500 text-[10px] font-black uppercase tracking-widest border border-gold-500/30">
                    Highly Recommended
                  </div>
                  <h3 class="text-lg font-black tracking-tight">Two-Factor Authentication (2FA)</h3>
                  <p class="text-sm text-slate-400 leading-relaxed font-medium">Lindungi akses admin dengan lapisan keamanan tambahan melalui aplikasi autentikator.</p>
                </div>
                <div 
                  class="w-14 h-7 rounded-full p-1 cursor-pointer transition-all border border-white/10"
                  :class="auth.user?.two_fa_enabled ? 'bg-gold-500' : 'bg-white/10'"
                  @click="open2FAModal"
                >
                  <div 
                    class="bg-white w-5 h-5 rounded-full shadow-lg transition-transform duration-300"
                    :class="{ 'translate-x-7': auth.user?.two_fa_enabled }"
                  ></div>
                </div>
              </div>

              <div class="flex items-center gap-4 pt-4 border-t border-white/5">
                <div class="flex items-center gap-2 text-xs font-bold" :class="auth.user?.two_fa_enabled ? 'text-emerald-400' : 'text-slate-500'">
                  <div class="w-2 h-2 rounded-full" :class="auth.user?.two_fa_enabled ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'"></div>
                  Status: {{ auth.user?.two_fa_enabled ? 'Proteksi Aktif' : 'Tidak Terproteksi' }}
                </div>
                <Button v-if="!auth.user?.two_fa_enabled" variant="ghost" size="sm" @click="open2FAModal" class="text-gold-500 hover:text-gold-400 hover:bg-gold-500/10 font-bold p-0 h-auto">
                  Setup Sekarang <ArrowRight class="ml-1 w-3 h-3" />
                </Button>
              </div>
            </div>

            <!-- Backup Card -->
            <div class="p-6 rounded-3xl border border-blue-100 bg-blue-50/50 group hover:bg-white hover:shadow-xl hover:shadow-blue-500/5 transition-all">
              <div class="flex items-center gap-4 mb-4">
                <div class="p-3 rounded-2xl bg-blue-500 text-white shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                  <Database class="w-5 h-5" />
                </div>
                <div class="space-y-0.5">
                  <h4 class="font-black text-slate-900 tracking-tight">Automated Cloud Backup</h4>
                  <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Backup Otomatis Aktif</p>
                </div>
              </div>
              <p class="text-sm text-slate-600 leading-relaxed font-medium mb-6">Database klinik dicadangkan setiap 24 jam ke penyimpanan cloud yang terenkripsi untuk mencegah kehilangan data.</p>
              <Button variant="outline" class="w-full h-12 rounded-2xl bg-white border-blue-200 text-blue-700 font-bold hover:bg-blue-50 gap-2">
                <RefreshCw class="w-4 h-4" /> Force Manual Backup
              </Button>
            </div>
          </div>
        </Card>
      </div>
    </div>

    <!-- 2FA Setup Dialog -->
    <Dialog v-model:open="show2FAModal">
      <DialogContent class="sm:max-w-md p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <div class="p-8 border-b bg-slate-50/50 flex items-center justify-between">
          <div class="space-y-1">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
              {{ auth.user?.two_fa_enabled ? 'Matikan 2FA' : 'Setup Autentikasi 2FA' }}
            </h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Scan QR Code dengan Google/Authy</p>
          </div>
          <div class="p-3 rounded-2xl bg-white shadow-sm text-gold-500">
            <ShieldCheck class="w-6 h-6" />
          </div>
        </div>

        <div class="p-8 space-y-8">
          <!-- Setup 2FA -->
          <div v-if="!auth.user?.two_fa_enabled" class="space-y-8">
            <div class="flex flex-col items-center justify-center p-8 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200 group transition-all">
              <div v-if="twoFactorData.qr_url" class="p-4 bg-white rounded-2xl shadow-xl shadow-slate-200/50 mb-6 group-hover:scale-105 transition-transform duration-500">
                <qrcode-vue :value="twoFactorData.qr_url" :size="200" level="H" class="rounded-lg" />
              </div>
              <div v-else class="h-44 flex items-center justify-center">
                <Loader2 class="w-10 h-10 animate-spin text-gold-500" />
              </div>
              
              <div class="space-y-1 text-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Backup Secret Key</p>
                <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border shadow-sm font-mono text-sm text-slate-600 font-bold tracking-widest">
                  {{ twoFactorData.secret }}
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <div class="space-y-2">
                <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1 text-center block">Masukkan 6 Digit Kode</Label>
                <div class="relative">
                  <Input 
                    v-model="verificationCode" 
                    type="text" 
                    maxlength="6"
                    placeholder="000 000" 
                    class="h-16 rounded-2xl bg-slate-50 border-slate-100 text-center text-3xl font-black tracking-[0.4em] focus:ring-gold-500 shadow-inner" 
                  />
                  <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300">
                    <KeyRound class="w-6 h-6" />
                  </div>
                </div>
              </div>
              
              <p v-if="twoFactorError" class="text-rose-500 text-[11px] font-bold uppercase tracking-wider text-center animate-bounce">
                {{ twoFactorError }}
              </p>

              <Button 
                @click="toggle2FA(true)" 
                class="w-full h-16 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-black text-lg shadow-xl shadow-slate-900/10"
                :disabled="isLoading2FA || verificationCode.length < 6"
              >
                <Loader2 v-if="isLoading2FA" class="mr-2 w-5 h-5 animate-spin" />
                Aktifkan Proteksi
              </Button>
            </div>
          </div>

          <!-- Disable 2FA -->
          <div v-else class="space-y-8">
            <div class="flex flex-col items-center gap-4 text-center">
              <div class="w-20 h-20 rounded-3xl bg-rose-50 text-rose-500 flex items-center justify-center shadow-lg shadow-rose-100">
                <AlertTriangle class="w-10 h-10" />
              </div>
              <div class="space-y-2">
                <h4 class="text-lg font-black text-slate-900 tracking-tight">Konfirmasi Nonaktifkan 2FA</h4>
                <p class="text-sm text-slate-500 font-medium">Masukkan kode verifikasi dari aplikasi Anda untuk melanjutkan.</p>
              </div>
            </div>

            <div class="space-y-6">
              <Input 
                v-model="verificationCode" 
                type="text" 
                maxlength="6"
                placeholder="000 000" 
                class="h-16 rounded-2xl bg-slate-50 border-slate-100 text-center text-3xl font-black tracking-[0.4em] focus:ring-gold-500 shadow-inner" 
              />
              
              <p v-if="twoFactorError" class="text-rose-500 text-[11px] font-bold uppercase tracking-wider text-center">
                {{ twoFactorError }}
              </p>

              <div class="flex gap-4">
                <Button variant="ghost" class="flex-1 h-14 rounded-2xl font-bold text-slate-500" @click="show2FAModal = false">Batal</Button>
                <Button 
                  @click="toggle2FA(false)" 
                  class="flex-1 h-14 rounded-2xl bg-rose-500 hover:bg-rose-600 text-white font-black shadow-xl shadow-rose-500/20"
                  :disabled="isLoading2FA || verificationCode.length < 6"
                >
                  <Loader2 v-if="isLoading2FA" class="mr-2 w-4 h-4 animate-spin" />
                  Matikan 2FA
                </Button>
              </div>
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { 
  Settings2, Store, Clock, ShieldCheck, X, 
  Phone, Mail, Lock, Database, ArrowRight, 
  RefreshCw, Loader2, KeyRound, AlertTriangle
} from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Switch } from '@/components/ui/switch'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import QrcodeVue from 'qrcode.vue'
import client from '@/api/client'
import { toast } from 'vue-sonner'

const auth = useAuthStore()
const router = useRouter()

const isSaving = ref(false)
const activeTab = ref('general')

// 2FA States
const show2FAModal = ref(false)
const isLoading2FA = ref(false)
const twoFactorError = ref('')
const verificationCode = ref('')
const twoFactorData = ref({
  secret: '',
  qr_url: ''
})

const tabs = [
  { id: 'general', name: 'Profil Klinik', icon: Store },
  { id: 'hours', name: 'Jam Operasional', icon: Clock },
  { id: 'security', name: 'Keamanan & 2FA', icon: ShieldCheck },
]

const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']

const settings = ref({
  clinicName: 'Aura Beauty Clinic',
  whatsappNumber: '6281234567890',
  address: 'Jl. Kecantikan Raya No 1, Jakarta Selatan',
})

const operationalHours = ref(
  days.map(d => ({ day: d, open: '09:00', close: '18:00', isOpen: true }))
)

const open2FAModal = async () => {
  twoFactorError.value = ''
  verificationCode.value = ''
  
  if (!auth.user?.two_fa_enabled) {
    try {
      const res = await client.get('/admin/2fa/setup')
      twoFactorData.value = res.data
    } catch (err) {
      toast.error('Gagal menyiapkan setup 2FA')
    }
  }
  
  show2FAModal.value = true
}

const toggle2FA = async (enable) => {
  isLoading2FA.value = true
  twoFactorError.value = ''
  try {
    await client.post('/admin/2fa/toggle', {
      code: verificationCode.value,
      enable: enable
    })
    
    // Update local user state
    auth.user.two_fa_enabled = enable
    localStorage.setItem('bc_user', JSON.stringify(auth.user))
    
    show2FAModal.value = false
    toast.success(`Fitur 2FA berhasil ${enable ? 'diaktifkan' : 'dimatikan'}`)
  } catch (err) {
    twoFactorError.value = err?.response?.data?.message || 'Verifikasi gagal. Silakan coba lagi.'
  } finally {
    isLoading2FA.value = false
  }
}

const saveSettings = async () => {
  isSaving.value = true
  try {
    await client.put('/admin/settings', {
      ...settings.value,
      operational_hours: operationalHours.value,
    })
    toast.success('Pengaturan berhasil disimpan')
  } catch (err) {
    toast.error(err?.response?.data?.message || 'Gagal menyimpan pengaturan')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  if (!auth.isOwner) {
    router.push('/admin/dashboard')
    return
  }
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
