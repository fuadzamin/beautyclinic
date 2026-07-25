<template>
  <div v-if="receipt" class="fixed inset-0 bg-slate-900/60 z-[70] flex items-center justify-center p-4" id="receipt-modal">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm max-h-[90vh] overflow-y-auto print:shadow-none print:rounded-none print:max-h-none">
      <!-- Receipt header -->
      <div class="p-6 text-center border-b border-dashed border-slate-300">
        <img v-if="settings.logo_url" :src="settings.logo_url" class="h-12 mx-auto mb-2 object-contain" />
        <Sparkles v-else class="w-8 h-8 text-gold-500 mx-auto mb-2" />
        <p class="font-bold text-lg text-slate-800">{{ settings.clinic_name }}</p>
        <p v-if="settings.tagline" class="text-xs text-slate-500">{{ settings.tagline }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ receipt.branch?.name }}</p>
        <p v-if="settings.address" class="text-xs text-slate-500">{{ settings.address }}</p>
        <p v-if="settings.phone" class="text-xs text-slate-500">{{ settings.phone }}</p>
        <p class="font-mono text-sm font-bold text-gold-700 mt-2">{{ receipt.transaction_number }}</p>
        <p class="text-xs text-slate-400">{{ formatDateTime(receipt.created_at) }}</p>
        <p v-if="settings.show_appointment_date && receipt.appointment?.appointment_date" class="text-xs text-slate-400">
          Jadwal: {{ new Date(receipt.appointment.appointment_date).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}) }}
        </p>
      </div>
      <!-- Items -->
      <div class="p-4 space-y-2 border-b border-dashed border-slate-300">
        <div v-if="settings.show_treatment && receipt.appointment?.treatment" class="flex justify-between text-sm">
          <span class="text-slate-700">{{ receipt.appointment.treatment.name }}</span>
          <span class="font-medium">Rp {{ formatPrice(receipt.subtotal) }}</span>
        </div>
        <template v-if="settings.show_products">
          <div v-for="item in receipt.items" :key="item.id" class="flex justify-between text-sm">
            <span class="text-slate-700">{{ item.item_name }} x{{ item.quantity }}</span>
            <span class="font-medium">Rp {{ formatPrice(item.total_price) }}</span>
          </div>
        </template>
        <div v-if="settings.show_discount && receipt.discount > 0" class="flex justify-between text-sm text-rose-600">
          <span>Discount</span>
          <span>- Rp {{ formatPrice(receipt.discount) }}</span>
        </div>
      </div>
      <!-- Total & payment -->
      <div class="p-4 space-y-1">
        <div class="flex justify-between font-bold text-base text-slate-800">
          <span>TOTAL</span>
          <span>Rp {{ formatPrice(receipt.grand_total) }}</span>
        </div>
        <template v-if="settings.show_payment_method">
          <div class="flex justify-between text-sm text-slate-500">
            <span>{{ receipt.payment_method?.toUpperCase() }}</span>
            <span>Rp {{ formatPrice(receipt.amount_paid) }}</span>
          </div>
          <div v-if="receipt.change_amount > 0" class="flex justify-between text-sm text-green-600 font-medium">
            <span>Kembali</span>
            <span>Rp {{ formatPrice(receipt.change_amount) }}</span>
          </div>
        </template>
      </div>
      <!-- Footer -->
      <div class="px-6 pb-6 pt-2 border-t border-dashed border-slate-300 text-center">
        <p class="text-xs text-slate-400 mb-3 leading-relaxed">{{ settings.footer_message }}</p>
        <div v-if="settings.social_instagram || settings.social_whatsapp || settings.website" 
          class="text-xs text-slate-400 mb-4 space-y-0.5">
          <p v-if="settings.social_instagram">📸 {{ settings.social_instagram }}</p>
          <p v-if="settings.social_whatsapp">💬 wa.me/{{ settings.social_whatsapp }}</p>
          <p v-if="settings.website">🌐 {{ settings.website }}</p>
        </div>
        <div class="flex gap-3 print:hidden">
          <button @click="$emit('close')" class="btn-secondary flex-1 text-sm">Tutup</button>
          <button @click="$emit('print')" class="btn-primary flex-1 text-sm flex items-center justify-center gap-1">
            <Printer class="w-4 h-4" /> Print
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Sparkles, Printer } from 'lucide-vue-next'

defineProps({
  receipt: Object,
  settings: Object
})

defineEmits(['close', 'print'])

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)
const formatDateTime = (d) => d ? new Date(d).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : ''
</script>
