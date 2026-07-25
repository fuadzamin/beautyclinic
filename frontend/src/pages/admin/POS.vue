<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Point of Sale</h1>
        <p class="text-muted-foreground text-sm">Proses pembayaran untuk layanan dan produk klinik.</p>
      </div>
      
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <!-- Branch selector (Custom Styled) -->
        <div class="relative w-full sm:w-56 group">
          <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-gold-500 transition-colors" />
          <select 
            v-model="selectedBranch" 
            @change="loadAppointments" 
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all appearance-none cursor-pointer"
          >
            <option value="" disabled>Pilih Cabang...</option>
            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
          </select>
          <ChevronRight class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 rotate-90 pointer-events-none" />
        </div>
      </div>
    </div>

    <!-- Main View -->
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 mt-2">
      <!-- Left: Appointments Queue -->
      <div class="xl:col-span-2">
        <PosQueue 
          v-model:search="queueSearch"
          :appointments="appointments"
          :filtered-appointments="filteredAppointments"
          :selected-branch="selectedBranch"
          :selected-id="checkout.appointment_id"
          :loading="apptLoading"
          @select="loadCheckout"
        />
      </div>

      <!-- Right: Checkout Panel -->
      <div class="xl:col-span-3">
        <PosCheckout 
          :checkout="checkout"
          :cart-items="cartItems"
          :selected-appt="selectedAppt"
          :subtotal="subtotal"
          :products-total="productsTotal"
          :grand-total="grandTotal"
          :is-processing="isProcessing"
          :can-checkout="canCheckout"
          :block-reason="checkoutBlockReason"
          @reset="resetCheckout"
          @open-product-modal="openProductModal"
          @increase-qty="idx => cartItems[idx].quantity++"
          @decrease-qty="decreaseQty"
          @checkout="showConfirmModal = true"
        />
      </div>
    </div>

    <!-- ─── MODALS ─── -->
    
    <!-- PRODUCT SEARCH DIALOG -->
    <Dialog :open="showProductModal" @update:open="val => !val && closeProductModal()">
      <DialogContent class="sm:max-w-[500px] p-0 overflow-hidden border-none shadow-2xl">
        <DialogHeader class="px-6 pt-6 pb-4">
          <DialogTitle class="text-xl">Tambah Produk</DialogTitle>
          <DialogDescription>Cari produk untuk ditambahkan ke transaksi</DialogDescription>
        </DialogHeader>

        <div class="px-6 py-2">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <Input
              ref="productSearchRef"
              v-model="productQuery"
              @input="debouncedSearch"
              placeholder="Ketik nama produk..."
              class="pl-9 pr-9 h-11 rounded-xl"
            />
          </div>
        </div>

        <!-- Category scroll -->
        <div class="px-6 py-2 flex gap-2 overflow-x-auto no-scrollbar border-b">
          <Button 
            v-for="cat in productCategories" :key="cat.value"
            variant="ghost"
            size="sm"
            class="rounded-full h-8 text-xs font-semibold whitespace-nowrap px-4"
            :class="selectedCategory === cat.value ? 'bg-gold-500 text-white hover:bg-gold-600' : 'bg-slate-100 hover:bg-slate-200'"
            @click="selectedCategory = cat.value; loadCategoryProducts()"
          >
            {{ cat.label }}
          </Button>
        </div>

        <!-- Results -->
        <div class="h-[400px] overflow-y-auto">
          <div v-if="productLoading" class="flex flex-col items-center justify-center h-full text-muted-foreground gap-2">
            <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
            <p class="text-sm">Mencari produk...</p>
          </div>
          
          <div v-else-if="productResults.length === 0" class="flex flex-col items-center justify-center h-full text-muted-foreground gap-2 p-8 text-center">
            <ShoppingBag class="w-12 h-12 opacity-20" />
            <p class="text-sm font-medium">Tidak ada produk ditemukan</p>
            <p class="text-xs">Coba gunakan kata kunci lain atau kategori yang berbeda</p>
          </div>

          <div v-else class="divide-y divide-slate-100">
            <button
              v-for="p in productResults" :key="p.id"
              @click="addToCart(p)"
              class="w-full text-left px-6 py-4 hover:bg-gold-50/50 flex items-center gap-4 transition-colors group"
            >
              <div v-if="p.image_url" class="w-12 h-12 rounded-xl overflow-hidden border bg-white shrink-0">
                <img :src="p.image_url" class="w-full h-full object-cover" @error="$event.target.style.display='none'" />
              </div>
              <div v-else class="w-12 h-12 rounded-xl bg-slate-100 shrink-0 flex items-center justify-center text-slate-400">
                <ShoppingBag class="w-5 h-5" />
              </div>
              
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-900 text-sm truncate">{{ p.name }}</p>
                <div class="flex items-center gap-2 mt-0.5">
                  <Badge variant="outline" class="text-[10px] h-4 px-1.5 border-slate-200 capitalize">{{ p.category }}</Badge>
                  <p class="text-[11px] text-emerald-600 font-medium" v-if="getBranchStock(p) !== null">
                    Stok: {{ getBranchStock(p) }}
                  </p>
                </div>
              </div>
              
              <div class="text-right shrink-0">
                <p class="font-bold text-slate-900">Rp {{ formatPrice(p.price) }}</p>
                <Badge v-if="isInCart(p.id)" class="bg-gold-500 hover:bg-gold-500 text-white h-5 px-1.5 mt-1">✓ Sip</Badge>
              </div>
            </button>
          </div>
        </div>

        <DialogFooter class="p-6 border-t bg-slate-50/50">
          <div class="flex items-center justify-between w-full">
            <p class="text-xs font-medium text-muted-foreground">{{ cartItems.length }} item dalam keranjang</p>
            <Button @click="closeProductModal" class="bg-slate-900 text-white hover:bg-slate-800 rounded-xl px-8">
              Selesai
            </Button>
          </div>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- PAYMENT CONFIRMATION DIALOG -->
    <Dialog :open="showConfirmModal" @update:open="val => !val && (showConfirmModal = false)">
      <DialogContent class="sm:max-w-[400px] p-0 overflow-hidden border-none shadow-2xl rounded-2xl">
        <div class="h-1 bg-gold-500 w-full"></div>
        <DialogHeader class="px-6 pt-6 text-center">
          <div class="mx-auto w-12 h-12 rounded-full bg-gold-50 flex items-center justify-center mb-4">
            <CreditCard class="w-6 h-6 text-gold-600" />
          </div>
          <DialogTitle class="text-xl font-bold">Konfirmasi Bayar</DialogTitle>
          <DialogDescription>Pastikan data transaksi sudah sesuai sebelum diproses.</DialogDescription>
        </DialogHeader>

        <div class="p-6 space-y-4">
          <div class="bg-slate-50 rounded-2xl p-4 space-y-3">
            <div class="flex justify-between items-center text-sm">
              <span class="text-slate-500">Pelanggan</span>
              <span class="font-bold text-slate-900">{{ checkout.customer_name }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
              <span class="text-slate-500">Metode</span>
              <span class="font-bold text-slate-900 uppercase">{{ checkout.payment_method }}</span>
            </div>
            <div class="pt-3 border-t border-slate-200 flex justify-between items-center">
              <span class="text-sm font-bold text-slate-700">TOTAL</span>
              <span class="text-2xl font-black text-gold-600">Rp {{ formatPrice(grandTotal) }}</span>
            </div>
          </div>
        </div>

        <DialogFooter class="p-6 gap-3 sm:gap-0 sm:flex-row flex-col">
          <Button variant="ghost" @click="showConfirmModal = false" class="flex-1 rounded-xl h-11">Batal</Button>
          <Button 
            @click="processCheckout" 
            :disabled="isProcessing" 
            class="flex-1 bg-gold-500 hover:bg-gold-600 text-white font-bold h-11 rounded-xl shadow-md shadow-gold-500/20"
          >
            <Loader2 v-if="isProcessing" class="mr-2 h-4 w-4 animate-spin" />
            <Check v-else class="mr-2 h-4 w-4" />
            Proses Sekarang
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- RECEIPT MODAL -->
    <PosReceipt 
      v-if="receipt"
      :receipt="receipt"
      :settings="receiptSettings"
      @close="receipt = null; resetCheckout(); loadAppointments()"
      @print="printPage"
    />

  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted, watch, markRaw } from 'vue'
import { useRoute } from 'vue-router'
import client from '@/api/client'
import { 
  History, ShoppingBag, Search, X, Loader2, CreditCard, 
  Receipt, MapPin, ChevronRight, Check, Zap, Sparkles
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

// Shadcn Components
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import { 
  Dialog, DialogContent, DialogDescription, DialogFooter, 
  DialogHeader, DialogTitle 
} from '@/components/ui/dialog'

// Legacy/Modular Components
import PosQueue from '@/components/pos/PosQueue.vue'
import PosCheckout from '@/components/pos/PosCheckout.vue'
import PosReceipt from '@/components/pos/PosReceipt.vue'

// ─── State ──────────────────────────────────────────────────────────────
const branches      = ref([])
const selectedBranch = ref('')
const appointments  = ref([])
const apptLoading   = ref(false)
const isProcessing  = ref(false)
const receipt       = ref(null)
const queueSearch   = ref('')
const showConfirmModal = ref(false)

// Receipt settings
const receiptSettings = ref({
  clinic_name: 'AURA Beauty Clinic', tagline: '', address: '', phone: '', logo_url: '',
  show_treatment: true, show_products: true, show_discount: true, show_payment_method: true,
  show_appointment_date: true, footer_message: 'Terima kasih 💖',
  social_instagram: '', social_whatsapp: '', website: '', auto_print: false
})

// Product search
const showProductModal  = ref(false)
const productQuery      = ref('')
const productResults    = ref([])
const productSearchRef  = ref(null)
const productLoading    = ref(false)
const selectedCategory  = ref('')
const cartItems         = ref([])

const productCategories = [
  { value: '', label: 'Semua' },
  { value: 'serum', label: 'Serum' },
  { value: 'sunscreen', label: 'Sunscreen' },
  { value: 'moisturizer', label: 'Moisturizer' }
]

const selectedAppt = ref(null)
const checkout = ref({
  appointment_id: null, customer_name: '', customer_phone: '',
  payment_method: 'cash', amount_paid: 0, discount: 0, 
  points_redeemed: 0, points_value: 0, notes: ''
})

// ─── Computed ────────────────────────────────────────────────────────────
const subtotal = computed(() => selectedAppt.value?.treatment?.price || 0)
const productsTotal = computed(() => cartItems.value.reduce((sum, i) => sum + i.unit_price * i.quantity, 0))
const pointsValue = computed(() => (checkout.value.points_redeemed || 0) * 1000)
const grandTotal = computed(() => {
  const total = subtotal.value + productsTotal.value - (checkout.value.discount || 0) - pointsValue.value
  return Math.max(0, total)
})

const filteredAppointments = computed(() => {
  if (!queueSearch.value.trim()) return appointments.value
  const q = queueSearch.value.toLowerCase()
  return appointments.value.filter(a => 
    (a.customer_name || a.user?.name || '').toLowerCase().includes(q) ||
    (a.treatment?.name || '').toLowerCase().includes(q)
  )
})

const canCheckout = computed(() => !!selectedBranch.value && !!checkout.value.customer_name?.trim())

const checkoutBlockReason = computed(() => {
  if (!selectedBranch.value) return 'Pilih cabang terlebih dahulu'
  if (!checkout.value.customer_name?.trim()) return 'Nama pelanggan wajib diisi'
  if (!selectedAppt.value && cartItems.value.length === 0) return 'Pilih reservasi atau produk'
  if (checkout.value.payment_method === 'cash' && checkout.value.amount_paid < grandTotal.value)
    return `Jumlah kurang (butuh Rp ${formatPrice(grandTotal.value)})`
  return null
})

// ─── Methods ─────────────────────────────────────────────────────────────
const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)
const printPage = () => window.print()

const fetchBranches = async () => {
  try {
    const res = await client.get('/admin/branches')
    branches.value = res?.data || []
    if (branches.value.length === 1) {
      selectedBranch.value = branches.value[0].id
      loadAppointments()
    }
  } catch (err) { branches.value = [] }
}

const loadAppointments = async () => {
  if (!selectedBranch.value) return
  apptLoading.value = true
  try {
    const res = await client.get('/admin/pos/appointments', { params: { branch_id: selectedBranch.value } })
    appointments.value = res?.data || []
  } catch (err) { appointments.value = [] }
  finally { apptLoading.value = false }
}



const loadCheckout = (appt) => {
  selectedAppt.value = appt
  checkout.value.appointment_id = appt.id
  checkout.value.customer_name = appt.customer_name || appt.user?.name || ''
  checkout.value.customer_phone = appt.customer_phone || appt.user?.phone || ''
  checkout.value.discount = 0
  checkout.value.points_redeemed = 0
  checkout.value.amount_paid = appt.treatment?.price || 0
  cartItems.value = []
}

const resetCheckout = () => {
  selectedAppt.value = null
  cartItems.value = []
  checkout.value = { 
    appointment_id: null, customer_name: '', customer_phone: '', 
    payment_method: 'cash', amount_paid: 0, discount: 0, 
    points_redeemed: 0, points_value: 0, notes: '' 
  }
}

const openProductModal = () => {
  showProductModal.value = true
  loadCategoryProducts()
  // Use a small delay to ensure the Dialog animation is complete before focusing
  setTimeout(() => {
    const el = productSearchRef.value
    if (el) {
      // Handle both native input and shadcn Input component
      const native = el.inputRef ?? el.$el ?? el
      native?.focus?.()
    }
  }, 150)
}

const closeProductModal = () => showProductModal.value = false

const loadCategoryProducts = async () => {
  productLoading.value = true
  try {
    const params = { branch_id: selectedBranch.value || undefined, category: selectedCategory.value || undefined, search: productQuery.value || undefined }
    const res = await client.get('/products', { params })
    productResults.value = res?.data?.data || []
  } catch (err) { productResults.value = [] }
  finally { productLoading.value = false }
}

let searchTimer = null
const debouncedSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(loadCategoryProducts, 300)
}

const addToCart = (product) => {
  const existing = cartItems.value.find(i => i.product_id === product.id)
  if (existing) existing.quantity++
  else cartItems.value.push({ product_id: product.id, item_name: product.name, quantity: 1, unit_price: product.price })
}

const decreaseQty = (idx) => {
  if (cartItems.value[idx].quantity > 1) cartItems.value[idx].quantity--
  else cartItems.value.splice(idx, 1)
}

const getBranchStock = (p) => {
  const b = p.branches?.find(br => br.id === selectedBranch.value)
  return b ? b.pivot.stock_quantity : null
}

const isInCart = (id) => cartItems.value.some(i => i.product_id === id)

// Helper: safely parse integer, fallback to 0 for NaN/undefined/null
const safeInt = (v) => { const n = parseInt(v); return isNaN(n) ? 0 : n }

const processCheckout = async () => {
  showConfirmModal.value = false
  isProcessing.value = true
  try {
    const amountPaid = checkout.value.payment_method === 'cash'
      ? safeInt(checkout.value.amount_paid)
      : safeInt(grandTotal.value)

    const payload = {
      branch_id:        safeInt(selectedBranch.value),
      appointment_id:   checkout.value.appointment_id || undefined,
      customer_name:    checkout.value.customer_name?.trim() || '',
      customer_phone:   checkout.value.customer_phone?.trim() || undefined,
      payment_method:   checkout.value.payment_method,
      amount_paid:      amountPaid,
      discount:         safeInt(checkout.value.discount),
      points_redeemed:  safeInt(checkout.value.points_redeemed),
      notes:            checkout.value.notes || undefined,
      items:            cartItems.value.map(i => ({
        product_id: i.product_id,
        item_name:  i.item_name,
        quantity:   safeInt(i.quantity),
        unit_price: safeInt(i.unit_price),
      }))
    }

    console.log('[POS] Sending payload:', JSON.stringify(payload, null, 2))

    const res = await client.post('/admin/pos/checkout', payload)
    receipt.value = res?.data || res
    appointments.value = appointments.value.filter(a => a.id !== checkout.value.appointment_id)
    toast.success('Transaksi berhasil diproses!')
  } catch (err) {
    console.error('Checkout error:', err?.response?.data || err)
    if (err?.response?.data?.errors) {
      const errMsgs = Object.values(err.response.data.errors).flat().join(', ')
      toast.error(`Checkout gagal: ${errMsgs}`)
    } else {
      toast.error(err?.response?.data?.message || 'Checkout gagal.')
    }
  } finally { isProcessing.value = false }
}



const loadReceiptSettings = async () => {
  try {
    const res = await client.get('/admin/receipt-settings/view', { params: { branch_id: selectedBranch.value || undefined } })
    if (res?.data) Object.assign(receiptSettings.value, res.data)
  } catch (_) {}
}

let refreshTimer = null
onMounted(async () => {
  await fetchBranches()
  await loadReceiptSettings()
  refreshTimer = setInterval(() => { if (selectedBranch.value) loadAppointments() }, 30000)
})

onUnmounted(() => clearInterval(refreshTimer))

watch(grandTotal, (val) => { if (checkout.value.payment_method === 'cash' && checkout.value.amount_paid < val) checkout.value.amount_paid = val })
watch(() => checkout.value.payment_method, (method) => { if (method === 'cash' && checkout.value.amount_paid < grandTotal.value) checkout.value.amount_paid = grandTotal.value })
</script>

<style>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>



