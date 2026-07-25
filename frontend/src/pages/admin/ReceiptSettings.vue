<template>
  <div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <Printer class="w-6 h-6" />
          </div>
          Pengaturan Struk
        </h1>
        <p class="text-slate-500 text-sm font-medium">Kustomisasi tampilan struk fisik dan digital untuk pelanggan.</p>
      </div>
      
      <div v-if="branches.length > 1" class="flex items-center gap-3 bg-white p-2 pl-4 rounded-2xl shadow-sm border border-slate-100">
        <Label class="text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Cabang:</Label>
        <Select v-model="selectedBranch" @update:modelValue="loadSettings">
          <SelectTrigger class="w-48 h-10 border-none bg-slate-50 rounded-xl font-bold text-xs focus:ring-0">
            <SelectValue placeholder="Global" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Global (Semua)</SelectItem>
            <SelectItem v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <div v-if="isLoading" class="py-20 flex flex-col items-center gap-4">
      <Loader2 class="w-10 h-10 animate-spin text-gold-500" />
      <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Memuat Pengaturan...</p>
    </div>

    <form v-else @submit.prevent="saveSettings" class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-6xl">
      <!-- Main Settings -->
      <div class="lg:col-span-7 space-y-6">
        <!-- Header Section -->
        <Card class="border-none shadow-sm bg-white overflow-hidden rounded-3xl">
          <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
              <Store class="w-4 h-4 text-gold-500" /> Informasi Klinik
            </h2>
          </div>
          <div class="p-6 space-y-6">
            <div class="space-y-2">
              <Label class="text-[10px] font-black text-slate-700 uppercase tracking-widest ml-1">Nama Klinik</Label>
              <Input v-model="form.clinic_name" placeholder="AURA Beauty Clinic" class="h-11 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-slate-100 shadow-none font-bold" />
            </div>
            
            <div class="space-y-2">
              <Label class="text-[10px] font-black text-slate-700 uppercase tracking-widest ml-1">Tagline / Slogan</Label>
              <Input v-model="form.tagline" placeholder="Kecantikan adalah investasi terbaik" class="h-11 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-slate-100 shadow-none font-bold" />
            </div>

            <div class="space-y-2">
              <Label class="text-[10px] font-black text-slate-700 uppercase tracking-widest ml-1">Alamat Struk</Label>
              <Textarea v-model="form.address" placeholder="Jl. Kecantikan No. 1, Jakarta" class="rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-slate-100 shadow-none h-24 resize-none leading-relaxed font-medium" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label class="text-[10px] font-black text-slate-700 uppercase tracking-widest ml-1">Telepon</Label>
                <Input v-model="form.phone" placeholder="08123456789" class="h-11 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-slate-100 shadow-none font-bold" />
              </div>
              <div class="space-y-2">
                <Label class="text-[10px] font-black text-slate-700 uppercase tracking-widest ml-1">Email</Label>
                <Input v-model="form.email" type="email" placeholder="info@aura.com" class="h-11 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-slate-100 shadow-none font-bold" />
              </div>
            </div>
          </div>
        </Card>

        <!-- Display Toggles -->
        <Card class="border-none shadow-sm bg-white overflow-hidden rounded-3xl">
          <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
              <Settings2 class="w-4 h-4 text-gold-500" /> Konfigurasi Tampilan
            </h2>
          </div>
          <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="toggle in toggleOptions" :key="toggle.key" 
                 class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-transparent hover:border-slate-100 hover:bg-white transition-all group">
              <div class="space-y-0.5">
                <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">{{ toggle.label }}</p>
                <p class="text-[10px] text-slate-400 font-bold tracking-tight">{{ toggle.desc }}</p>
              </div>
              <Switch v-model:checked="form[toggle.key]" />
            </div>
          </div>
        </Card>

        <!-- Footer Message -->
        <Card class="border-none shadow-sm bg-white overflow-hidden rounded-3xl">
          <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
            <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
              <MessageSquare class="w-4 h-4 text-gold-500" /> Pesan Penutup
            </h2>
          </div>
          <div class="p-6 space-y-4">
            <div class="space-y-2">
              <Label class="text-[10px] font-black text-slate-700 uppercase tracking-widest ml-1 text-center">Pesan Terimakasih</Label>
              <Textarea v-model="form.footer_message" class="rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:border-slate-100 shadow-none h-24 resize-none leading-relaxed text-center font-bold" placeholder="Terima kasih telah mempercayakan kecantikan Anda kepada kami 💖" />
            </div>
            
            <div class="grid grid-cols-3 gap-4">
              <div class="space-y-2">
                <Label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Instagram</Label>
                <Input v-model="form.social_instagram" placeholder="@auraclinic" class="h-10 rounded-xl bg-slate-50 border-transparent text-xs font-bold" />
              </div>
              <div class="space-y-2">
                <Label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">WhatsApp</Label>
                <Input v-model="form.social_whatsapp" placeholder="6281234567890" class="h-10 rounded-xl bg-slate-50 border-transparent text-xs font-bold" />
              </div>
              <div class="space-y-2">
                <Label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Website</Label>
                <Input v-model="form.website" placeholder="auraclinic.com" class="h-10 rounded-xl bg-slate-50 border-transparent text-xs font-bold" />
              </div>
            </div>
          </div>
        </Card>

        <div class="flex items-center gap-4">
          <Button type="submit" class="flex-1 h-14 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-base shadow-xl shadow-gold-500/20" :disabled="isSaving">
            <Loader2 v-if="isSaving" class="mr-2 w-5 h-5 animate-spin" />
            <Save v-else class="mr-2 w-5 h-5" />
            Simpan Konfigurasi
          </Button>
          <Button type="button" variant="outline" @click="showPreview = true" class="h-14 px-8 rounded-2xl border-slate-200 text-slate-600 font-black flex lg:hidden">
            <Eye class="w-5 h-5" />
          </Button>
        </div>
      </div>

      <!-- Preview Column (Sticky) -->
      <div class="lg:col-span-5 hidden lg:block">
        <div class="sticky top-8 space-y-4">
          <div class="flex items-center justify-between px-2">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Real-time Preview</h3>
            <div class="flex items-center gap-2 text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full uppercase tracking-widest">
              <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div> Digital Thermal
            </div>
          </div>
          
          <!-- Thermal Paper Effect -->
          <div class="relative bg-white shadow-2xl rounded-sm p-8 min-h-[600px] receipt-paper overflow-hidden mx-auto max-w-[320px]">
            <!-- Jagged Top Edge -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-slate-200 receipt-edge-top"></div>
            
            <div class="receipt-content font-mono text-[10px] space-y-6 text-slate-900">
              <!-- Header -->
              <div class="text-center space-y-1">
                <img v-if="form.logo_url" :src="form.logo_url" class="h-12 mx-auto mb-3 object-contain opacity-80" />
                <h4 class="text-base font-black uppercase tracking-tighter">{{ form.clinic_name }}</h4>
                <p v-if="form.tagline" class="text-[9px] italic opacity-60">{{ form.tagline }}</p>
                <div class="pt-2 opacity-70 leading-tight">
                  <p v-if="form.address">{{ form.address }}</p>
                  <p v-if="form.phone">Telp: {{ form.phone }}</p>
                  <p v-if="form.email">{{ form.email }}</p>
                </div>
              </div>

              <div class="border-t border-dashed border-slate-300"></div>

              <!-- Transaction Meta -->
              <div class="space-y-0.5 text-center">
                <p class="font-black">TRX-20260514-0042</p>
                <p class="opacity-60">14 Mei 2026, 14:30 WIB</p>
                <p class="pt-1 font-bold">Kasir: {{ form.show_cashier_name ? 'Sarah Admin' : 'Staff Aura' }}</p>
                <p class="opacity-70">Pelanggan: Anita Wijaya</p>
              </div>

              <div class="border-t border-dashed border-slate-300"></div>

              <!-- Items -->
              <div class="space-y-3">
                <div v-if="form.show_treatment" class="space-y-1">
                  <div class="flex justify-between font-black">
                    <span>FACIAL GLOW PREMIUM</span>
                    <span>185,000</span>
                  </div>
                  <div v-if="form.show_appointment_date" class="text-[8px] opacity-60">Sched: 15/05/2026</div>
                </div>
                
                <div v-if="form.show_products" class="space-y-1">
                  <div class="flex justify-between font-black">
                    <span>SUNSCREEN GEL SPF 50</span>
                    <span>95,000</span>
                  </div>
                  <div class="text-[8px] opacity-60">Qty: 1 x 95,000</div>
                </div>

                <div v-if="form.show_discount" class="flex justify-between italic">
                  <span>Promo Member Silver (10%)</span>
                  <span>(28,000)</span>
                </div>
              </div>

              <div class="border-t-2 border-slate-900 border-double pt-3"></div>

              <!-- Totals -->
              <div class="space-y-1 text-xs font-black">
                <div class="flex justify-between text-[10px]">
                  <span>SUBTOTAL</span>
                  <span>280,000</span>
                </div>
                <div v-if="form.show_discount" class="flex justify-between text-[10px]">
                  <span>TOTAL DISKON</span>
                  <span>-28,000</span>
                </div>
                <div class="flex justify-between text-base pt-1 border-t border-slate-200 mt-2">
                  <span>TOTAL</span>
                  <span>252,000</span>
                </div>
              </div>

              <div class="pt-2 space-y-0.5 opacity-70 font-bold">
                <div class="flex justify-between">
                  <span>Metode:</span>
                  <span>{{ form.show_payment_method ? 'QRIS DANA' : 'TUNAI' }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Bayar:</span>
                  <span>252,000</span>
                </div>
                <div class="flex justify-between">
                  <span>Kembali:</span>
                  <span>0</span>
                </div>
              </div>

              <div class="border-t border-dashed border-slate-300"></div>

              <!-- Footer -->
              <div class="text-center space-y-4">
                <p class="font-bold leading-relaxed">{{ form.footer_message }}</p>
                
                <div class="space-y-0.5 opacity-60 text-[8px] font-black uppercase tracking-widest">
                  <p v-if="form.social_instagram">IG: {{ form.social_instagram }}</p>
                  <p v-if="form.social_whatsapp">WA: {{ form.social_whatsapp }}</p>
                  <p v-if="form.website">{{ form.website }}</p>
                </div>

                <div class="pt-2 flex flex-col items-center gap-1 opacity-20 grayscale">
                  <div class="h-6 w-32 bg-slate-900 rounded-sm"></div>
                  <p class="text-[7px]">AUR-POS-SYS-V2.0</p>
                </div>
              </div>
            </div>
            
            <!-- Jagged Bottom Edge -->
            <div class="absolute bottom-0 left-0 right-0 h-4 bg-slate-100 receipt-edge-bottom"></div>
          </div>
          
          <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-xl flex items-center justify-between">
            <div class="space-y-0.5">
              <p class="text-xs font-black tracking-tight">Auto-Print</p>
              <p class="text-[10px] text-slate-400 font-bold tracking-tight">Cetak struk otomatis setelah bayar</p>
            </div>
            <Switch v-model:checked="form.auto_print" />
          </div>
        </div>
      </div>
    </form>

    <!-- Preview Modal for Mobile -->
    <Dialog v-model:open="showPreview">
      <DialogContent class="sm:max-w-md p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white lg:hidden">
        <div class="p-4 border-b flex items-center justify-between bg-slate-50/50">
          <p class="text-xs font-black text-slate-900 uppercase tracking-widest">Preview Struk</p>
          <Button variant="ghost" size="icon" @click="showPreview = false" class="rounded-xl"><X class="w-4 h-4" /></Button>
        </div>
        <div class="p-8 bg-slate-100/50 flex justify-center overflow-y-auto max-h-[80vh] no-scrollbar">
          <!-- Thermal Paper Copy for Mobile -->
          <div class="bg-white shadow-xl rounded-sm p-8 w-full max-w-[300px] receipt-paper">
             <!-- Simplified version of the same receipt preview logic -->
             <div class="receipt-content font-mono text-[10px] space-y-6 text-slate-900 text-center">
                <h4 class="text-base font-black uppercase tracking-tighter">{{ form.clinic_name }}</h4>
                <div class="border-t border-dashed border-slate-300"></div>
                <div class="space-y-1">
                  <div class="flex justify-between font-black"><span>TOTAL</span><span>252,000</span></div>
                </div>
                <div class="border-t border-dashed border-slate-300"></div>
                <p class="font-bold">{{ form.footer_message }}</p>
             </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import client from '@/api/client'
import { 
  Save, Eye, X, Loader2, Printer, 
  Store, Settings2, MessageSquare, 
  RefreshCw, CheckCircle 
} from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Switch } from '@/components/ui/switch'
import { 
  Select, SelectContent, SelectItem, 
  SelectTrigger, SelectValue 
} from '@/components/ui/select'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { toast } from 'vue-sonner'

const branches      = ref([])
const selectedBranch = ref('all')
const isLoading     = ref(false)
const isSaving      = ref(false)
const showPreview   = ref(false)

const form = ref({
  clinic_name:           'AURA Beauty Clinic',
  tagline:               'Kecantikan adalah investasi terbaik',
  address:               'Jl. Kecantikan No. 1, Jakarta Selatan',
  phone:                 '081234567890',
  email:                 'info@auraclinic.com',
  logo_url:              '',
  show_treatment:        true,
  show_products:         true,
  show_discount:         true,
  show_payment_method:   true,
  show_cashier_name:     false,
  show_appointment_date: true,
  footer_message:        'Terima kasih telah mempercayakan kecantikan Anda kepada kami 💖',
  social_instagram:      '@auraclinic',
  social_whatsapp:       '6281234567890',
  website:               'auraclinic.com',
  auto_print:            false,
})

const toggleOptions = [
  { key: 'show_treatment',        label: 'Nama Treatment',    desc: 'Tampilkan layanan yang diambil' },
  { key: 'show_products',         label: 'Produk Tambahan',   desc: 'Tampilkan item produk yang dibeli' },
  { key: 'show_discount',         label: 'Diskon',            desc: 'Tampilkan baris diskon jika ada' },
  { key: 'show_payment_method',   label: 'Metode Pembayaran', desc: 'Cash / Transfer / QRIS / Card' },
  { key: 'show_cashier_name',     label: 'Nama Kasir',        desc: 'Nama staff yang memproses' },
  { key: 'show_appointment_date', label: 'Tanggal Janji',     desc: 'Tanggal appointment booking' },
]

const fetchBranches = async () => {
  try {
    const res = await client.get('/admin/branches')
    branches.value = res.data || []
  } catch (_) {}
}

const loadSettings = async () => {
  isLoading.value = true
  try {
    const params = selectedBranch.value !== 'all' ? { branch_id: selectedBranch.value } : {}
    const res = await client.get('/admin/receipt-settings', { params })
    const data = res.data
    if (data?.id) {
      Object.keys(form.value).forEach(k => {
        if (data[k] !== undefined && data[k] !== null) form.value[k] = data[k]
      })
    }
  } catch (_) {
    toast.error('Gagal memuat pengaturan struk')
  } finally {
    isLoading.value = false
  }
}

const saveSettings = async () => {
  isSaving.value = true
  try {
    await client.put('/admin/receipt-settings', {
      ...form.value,
      branch_id: selectedBranch.value === 'all' ? null : selectedBranch.value,
    })
    toast.success('Pengaturan struk berhasil disimpan')
  } catch (err) {
    toast.error(err?.response?.data?.message || 'Gagal menyimpan pengaturan')
  } finally {
    isSaving.value = false
  }
}

onMounted(async () => {
  await fetchBranches()
  await loadSettings()
})
</script>

<style scoped>
.receipt-paper {
  background: white;
  background-image: linear-gradient(rgba(0,0,0,.02) 1px, transparent 1px);
  background-size: 100% 2px;
  filter: drop-shadow(0 10px 20px rgba(0,0,0,0.05));
}

.receipt-edge-top {
  clip-path: polygon(0 0, 5% 100%, 10% 0, 15% 100%, 20% 0, 25% 100%, 30% 0, 35% 100%, 40% 0, 45% 100%, 50% 0, 55% 100%, 60% 0, 65% 100%, 70% 0, 75% 100%, 80% 0, 85% 100%, 90% 0, 95% 100%, 100% 0);
}

.receipt-edge-bottom {
  clip-path: polygon(0 100%, 0 0, 5% 100%, 10% 0, 15% 100%, 20% 0, 25% 100%, 30% 0, 35% 100%, 40% 0, 45% 100%, 50% 0, 55% 100%, 60% 0, 65% 100%, 70% 0, 75% 100%, 80% 0, 85% 100%, 90% 0, 95% 100%, 100% 0, 100% 100%);
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
