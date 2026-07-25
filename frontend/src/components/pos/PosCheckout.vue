<template>
  <Card class="border-none shadow-sm flex flex-col h-full overflow-hidden bg-white/50 backdrop-blur-sm">
    <CardHeader class="px-6 py-5 border-b border-slate-100 flex flex-row items-center justify-between shrink-0 bg-white/50">
      <div class="flex flex-col gap-0.5">
        <CardTitle class="text-base font-bold flex items-center gap-2 text-slate-900">
          <ShoppingBag class="w-4 h-4 text-gold-500" />
          Checkout
        </CardTitle>
        <CardDescription class="text-[11px]">Rincian pembayaran & produk</CardDescription>
      </div>
      <Button 
        v-if="checkout.appointment_id || cartItems.length" 
        variant="ghost" 
        size="sm" 
        class="text-xs text-muted-foreground hover:text-rose-500 h-8 px-3 rounded-lg"
        @click="$emit('reset')"
      >
        Reset
      </Button>
    </CardHeader>

    <CardContent class="p-6 flex-1 space-y-6 overflow-y-auto bg-white/30">
      <!-- Customer Info -->
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-1.5">
          <Label class="text-xs font-bold text-slate-700 ml-1">Nama Pelanggan</Label>
          <Input v-model="checkout.customer_name" placeholder="Walk-in customer" class="h-10 rounded-xl bg-white/80 border-slate-200" />
        </div>
        <div class="space-y-1.5">
          <Label class="text-xs font-bold text-slate-700 ml-1">No. WhatsApp</Label>
          <Input v-model="checkout.customer_phone" placeholder="08..." class="h-10 rounded-xl bg-white/80 border-slate-200" />
        </div>
      </div>

      <!-- Treatment line -->
      <div v-if="selectedAppt" class="p-4 rounded-2xl bg-gold-50/50 border border-gold-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
          <Sparkles class="w-12 h-12" />
        </div>
        <div class="flex items-center justify-between">
          <div class="space-y-0.5">
            <p class="text-[10px] text-gold-600 font-bold uppercase tracking-wider">Layanan Utama</p>
            <p class="font-bold text-slate-900">{{ selectedAppt.treatment?.name }}</p>
          </div>
          <p class="font-black text-gold-600 text-lg">Rp {{ formatPrice(selectedAppt.treatment?.price) }}</p>
        </div>
      </div>

      <!-- Loyalty Points -->
      <div v-if="selectedAppt?.user" class="p-4 rounded-2xl bg-slate-900 text-white relative overflow-hidden group shadow-lg">
        <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
          <Trophy class="w-16 h-16" />
        </div>
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-gold-500 flex items-center justify-center">
              <Star class="w-4 h-4 text-white fill-white" />
            </div>
            <span class="text-xs font-bold uppercase tracking-wide">Loyalty Points</span>
          </div>
          <Badge variant="secondary" class="bg-white/10 text-white border-none hover:bg-white/20 font-bold px-2 py-0.5">
            {{ selectedAppt.user.loyalty_points }} pts
          </Badge>
        </div>
        
        <div class="flex items-center gap-2 relative z-10">
          <div class="relative flex-1">
            <Input 
              v-model.number="checkout.points_redeemed" 
              type="number" 
              class="h-9 bg-white/10 border-white/20 text-white placeholder:text-white/40 rounded-lg text-sm focus-visible:ring-gold-500/50" 
              placeholder="Gunakan..." 
              :max="selectedAppt.user.loyalty_points"
            />
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-white/40 font-bold uppercase">pts</span>
          </div>
          <div class="text-right shrink-0">
            <p class="text-xs font-bold text-gold-400">- Rp {{ formatPrice(checkout.points_redeemed * 1000) }}</p>
          </div>
        </div>
        <p class="text-[9px] text-white/40 mt-3 flex items-center gap-1 italic font-medium">
          <Info class="w-2.5 h-2.5" /> 1 point = Rp 1.000
        </p>
      </div>

      <!-- Extra Products -->
      <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
          <Label class="text-xs font-bold text-slate-700 flex items-center gap-2">
            <Package class="w-3.5 h-3.5 text-slate-400" />
            Produk Tambahan
            <Badge v-if="cartItems.length" variant="secondary" class="h-4 px-1.5 text-[9px] bg-gold-50 text-gold-600 border-gold-100">{{ cartItems.length }}</Badge>
          </Label>
          <Button variant="ghost" size="sm" class="h-7 text-[11px] font-bold text-gold-600 hover:text-gold-700 hover:bg-gold-50/50 px-2 rounded-lg gap-1" @click="$emit('open-product-modal')">
            <Plus class="w-3 h-3" /> Tambah
          </Button>
        </div>

        <!-- Cart items list -->
        <div v-if="cartItems.length" class="space-y-2">
          <div v-for="(item, i) in cartItems" :key="item.product_id"
            class="flex items-center gap-3 p-3 rounded-2xl bg-white border border-slate-100 shadow-sm group hover:border-gold-200 transition-colors">
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-slate-900 truncate">{{ item.item_name }}</p>
              <p class="text-[10px] font-medium text-slate-400">Rp {{ formatPrice(item.unit_price) }} / item</p>
            </div>
            <div class="flex items-center gap-2 shrink-0 bg-slate-50 p-1 rounded-xl">
              <Button 
                variant="ghost" 
                size="icon" 
                class="h-6 w-6 rounded-lg hover:bg-rose-50 hover:text-rose-500"
                @click="$emit('decrease-qty', i)"
              >
                <Minus class="w-3 h-3" />
              </Button>
              <span class="w-6 text-center font-bold text-xs text-slate-900">{{ item.quantity }}</span>
              <Button 
                variant="ghost" 
                size="icon" 
                class="h-6 w-6 rounded-lg hover:bg-emerald-50 hover:text-emerald-500"
                @click="$emit('increase-qty', i)"
              >
                <Plus class="w-3 h-3" />
              </Button>
            </div>
            <div class="text-right shrink-0 w-24">
              <p class="text-sm font-black text-slate-900">Rp {{ formatPrice(item.unit_price * item.quantity) }}</p>
            </div>
          </div>
        </div>
        <div v-else class="flex flex-col items-center justify-center py-6 px-4 rounded-2xl border-2 border-dashed border-slate-100 text-slate-400 gap-2">
          <Package class="w-8 h-8 opacity-20" />
          <p class="text-[11px] font-medium italic">Belum ada produk tambahan.</p>
        </div>
      </div>

      <Separator class="bg-slate-100" />

      <!-- Totals -->
      <div class="space-y-2.5 px-1">
        <div class="flex justify-between items-center text-xs font-medium text-slate-500">
          <span>Subtotal Layanan</span>
          <span class="font-bold text-slate-900">Rp {{ formatPrice(subtotal) }}</span>
        </div>
        <div class="flex justify-between items-center text-xs font-medium text-slate-500">
          <span>Subtotal Produk</span>
          <span class="font-bold text-slate-900">Rp {{ formatPrice(productsTotal) }}</span>
        </div>
        <div class="flex justify-between items-center">
          <Label class="text-xs font-medium text-slate-500">Diskon Manual</Label>
          <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold text-slate-400">Rp</span>
            <Input v-model.number="checkout.discount" type="number" min="0" class="h-8 w-32 text-right text-xs rounded-lg bg-white" />
          </div>
        </div>
        <div v-if="checkout.points_redeemed > 0" class="flex justify-between items-center text-xs font-bold text-gold-600">
          <span class="flex items-center gap-1.5"><Star class="w-3 h-3 fill-gold-600" /> Penukaran Poin</span>
          <span>- Rp {{ formatPrice(checkout.points_redeemed * 1000) }}</span>
        </div>
        <div class="pt-4 mt-2 border-t-2 border-dashed border-slate-100 flex justify-between items-end">
          <div class="space-y-0.5">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Akhir</p>
            <p class="text-3xl font-black text-gold-600">Rp {{ formatPrice(grandTotal) }}</p>
          </div>
        </div>
      </div>

      <!-- Payment Method -->
      <div class="space-y-3">
        <Label class="text-xs font-bold text-slate-700 ml-1">Metode Pembayaran</Label>
        <div class="grid grid-cols-4 gap-2">
          <button 
            v-for="m in paymentMethods" :key="m.value"
            type="button"
            @click="checkout.payment_method = m.value"
            :class="[
              'p-3 rounded-2xl border-2 flex flex-col items-center gap-1.5 transition-all duration-300 relative overflow-hidden group',
              checkout.payment_method === m.value 
                ? 'border-gold-500 bg-gold-50 shadow-md shadow-gold-500/10' 
                : 'border-slate-100 bg-white hover:border-slate-200'
            ]"
          >
            <div v-if="checkout.payment_method === m.value" class="absolute top-0 right-0 p-1">
              <CheckCircle class="w-3 h-3 text-gold-500" />
            </div>
            <component 
              :is="m.icon" 
              :class="[
                'w-5 h-5 transition-colors',
                checkout.payment_method === m.value ? 'text-gold-600' : 'text-slate-400 group-hover:text-slate-600'
              ]" 
            />
            <span :class="[
              'text-[10px] font-bold uppercase tracking-tight',
              checkout.payment_method === m.value ? 'text-gold-700' : 'text-slate-500'
            ]">{{ m.label }}</span>
          </button>
        </div>
      </div>

      <!-- Amount Paid + Change (for cash) -->
      <div v-if="checkout.payment_method === 'cash'" class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-900 text-white shadow-xl">
        <div class="space-y-1.5">
          <Label class="text-[10px] font-bold text-white/50 uppercase tracking-widest ml-1">Jumlah Bayar</Label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-white/30">Rp</span>
            <Input v-model.number="checkout.amount_paid" type="number" min="0" :placeholder="'Rp ' + formatPrice(grandTotal)" class="h-10 pl-9 bg-white/10 border-white/20 text-white font-black text-lg focus-visible:ring-gold-500/50 rounded-xl" />
          </div>
        </div>
        <div class="space-y-1.5">
          <Label class="text-[10px] font-bold text-white/50 uppercase tracking-widest ml-1">Kembalian</Label>
          <div class="h-10 px-4 flex items-center bg-emerald-500/20 rounded-xl border border-emerald-500/30 text-emerald-400 font-black text-lg">
            Rp {{ formatPrice(Math.max(0, checkout.amount_paid - grandTotal)) }}
          </div>
        </div>
      </div>

      <!-- Notes -->
      <div class="space-y-1.5">
        <Label class="text-xs font-bold text-slate-700 ml-1">Catatan Transaksi</Label>
        <textarea v-model="checkout.notes" class="w-full h-20 rounded-2xl bg-white border border-slate-200 p-3 text-sm focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 outline-none resize-none transition-all placeholder:text-slate-300" placeholder="Tambahkan catatan jika ada..." />
      </div>
    </CardContent>

    <!-- Process Button -->
    <CardFooter class="p-6 border-t bg-white shrink-0 flex flex-col gap-4">
      <div v-if="blockReason" class="w-full flex items-center gap-3 p-3 rounded-xl bg-amber-50 border border-amber-100 animate-in slide-in-from-bottom duration-300">
        <AlertCircle class="w-4 h-4 text-amber-600 shrink-0" />
        <p class="text-[11px] font-bold text-amber-700">{{ blockReason }}</p>
      </div>
      
      <Button 
        class="w-full h-14 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-lg shadow-xl shadow-gold-500/20 transition-all active:scale-[0.98] disabled:opacity-50 disabled:grayscale"
        :disabled="!canCheckout || isProcessing || !!blockReason"
        @click="$emit('checkout')"
      >
        <Loader2 v-if="isProcessing" class="mr-2 h-5 w-5 animate-spin" />
        <CreditCard v-else class="mr-2 h-5 w-5" />
        PROSES PEMBAYARAN
      </Button>
    </CardFooter>
  </Card>
</template>

<script setup>
import { 
  ShoppingBag, Plus, Minus, CreditCard, Loader2, Banknote, 
  Smartphone, Building2, CreditCard as CardIcon, Star, Info,
  Package, Sparkles, Trophy, CheckCircle, AlertCircle
} from 'lucide-vue-next'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'

const props = defineProps({
  checkout: Object,
  cartItems: Array,
  selectedAppt: Object,
  subtotal: Number,
  productsTotal: Number,
  grandTotal: Number,
  isProcessing: Boolean,
  canCheckout: Boolean,
  blockReason: String
})

defineEmits(['reset', 'open-product-modal', 'increase-qty', 'decrease-qty', 'checkout'])

const paymentMethods = [
  { value: 'cash',     label: 'Cash',     icon: Banknote },
  { value: 'transfer', label: 'Transfer', icon: Building2 },
  { value: 'qris',     label: 'QRIS',     icon: Smartphone },
  { value: 'card',     label: 'Card',     icon: CardIcon },
]

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)
</script>
