<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <History class="w-6 h-6" />
          </div>
          Riwayat Transaksi
        </h1>
        <p class="text-muted-foreground text-sm font-medium">Lihat dan kelola riwayat transaksi pada klinik Anda.</p>
      </div>
      
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <!-- Branch selector -->
        <div class="relative w-full sm:w-56 group">
          <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-gold-500 transition-colors" />
          <select 
            v-model="selectedBranch" 
            @change="loadHistory" 
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all appearance-none cursor-pointer"
          >
            <option value="" disabled>Pilih Cabang...</option>
            <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
          </select>
          <ChevronRight class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 rotate-90 pointer-events-none" />
        </div>
      </div>
    </div>

    <!-- HISTORY VIEW -->
    <PosHistory 
      v-model:date="historyDate"
      :loading="historyLoading"
      :data="historyData"
      :meta="historyMeta"
      :summary="historySummary"
      @page-change="hPage => { historyPage = hPage; loadHistory() }"
      @print="printReceipt"
    />

    <!-- RECEIPT MODAL -->
    <PosReceipt 
      v-if="receipt"
      :receipt="receipt"
      :settings="receiptSettings"
      @close="receipt = null"
      @print="printPage"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import client from '@/api/client'
import { History, MapPin, ChevronRight } from 'lucide-vue-next'

import PosHistory from '@/components/pos/PosHistory.vue'
import PosReceipt from '@/components/pos/PosReceipt.vue'

const branches      = ref([])
const selectedBranch = ref('')
const receipt       = ref(null)

const historyDate   = ref(new Date().toISOString().split('T')[0])
const historyData   = ref([])
const historyLoading = ref(false)
const historyPage   = ref(1)
const historyMeta   = ref({ total: 0, per_page: 20, current_page: 1, last_page: 1 })
const historySummary = ref({ total_transactions: 0, total_revenue: 0, average_value: 0, top_payment_method: '—' })

const receiptSettings = ref({
  clinic_name: 'AURA Beauty Clinic', tagline: '', address: '', phone: '', logo_url: '',
  show_treatment: true, show_products: true, show_discount: true, show_payment_method: true,
  show_appointment_date: true, footer_message: 'Terima kasih 💖',
  social_instagram: '', social_whatsapp: '', website: '', auto_print: false
})

const printPage = () => window.print()
const printReceipt = (trx) => receipt.value = trx

const fetchBranches = async () => {
  try {
    const res = await client.get('/admin/branches')
    branches.value = res?.data || []
    if (branches.value.length > 0) {
      selectedBranch.value = branches.value[0].id
    }
  } catch (err) { branches.value = [] }
}

const loadHistory = async () => {
  historyLoading.value = true
  try {
    const params = { date: historyDate.value, page: historyPage.value, branch_id: selectedBranch.value || undefined }
    const [summaryRes, listRes] = await Promise.all([
      client.get('/admin/pos/transactions/summary', { params: { date: params.date, branch_id: params.branch_id } }),
      client.get('/admin/pos/transactions', { params })
    ])
    if (summaryRes?.data) historySummary.value = summaryRes.data
    const paginated = listRes?.data ?? listRes
    historyData.value = paginated?.data || []
    historyMeta.value = { 
      total: paginated?.total || 0, 
      current_page: paginated?.current_page || 1, 
      last_page: paginated?.last_page || 1 
    }
  } catch (err) { historyData.value = [] }
  finally { historyLoading.value = false }
}

const loadReceiptSettings = async () => {
  try {
    const res = await client.get('/admin/receipt-settings/view', { params: { branch_id: selectedBranch.value || undefined } })
    if (res?.data) Object.assign(receiptSettings.value, res.data)
  } catch (_) {}
}

onMounted(async () => {
  await fetchBranches()
  await loadReceiptSettings()
  await loadHistory()
})
</script>
