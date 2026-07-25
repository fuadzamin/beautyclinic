<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-3xl mx-auto">
      <h1 class="text-3xl font-bold text-slate-800 mb-8">Book an Appointment</h1>

      <!-- Booking Form -->
      <form v-if="!isSubmitted" @submit.prevent="submit" class="space-y-6">

        <!-- Section 1: Branch Location -->
        <div class="card">
          <h2 class="font-semibold text-lg mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center text-sm">1</span>
            Select Branch
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label v-for="branch in store.branches" :key="branch.id"
              :class="['p-4 rounded-xl border-2 cursor-pointer transition-colors',
                       form.branch_id === branch.id ? 'border-gold-500 bg-gold-50' : 'border-slate-100 hover:border-slate-300']"
            >
              <input type="radio" :value="branch.id" v-model="form.branch_id" @change="onBranchSelected" class="hidden" />
              <div class="flex items-center justify-between mb-1">
                <p class="font-bold text-slate-800">{{ branch.name }}</p>
                <div v-if="form.branch_id === branch.id" class="w-4 h-4 rounded-full bg-gold-500 border-4 border-gold-200"></div>
              </div>
              <p class="text-sm text-slate-500 line-clamp-2">{{ branch.address }}</p>
            </label>
          </div>
        </div>
        
        <!-- Section 2: Treatment -->
        <div class="card" :class="{'opacity-50 pointer-events-none': !form.branch_id}">
          <h2 class="font-semibold text-lg mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center text-sm">2</span>
            Select Treatment
          </h2>
          <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
            <label v-for="t in store.treatments" :key="t.id"
              :class="['flex items-center gap-4 p-4 rounded-xl border-2 cursor-pointer transition-colors',
                       form.treatment_id === t.id ? 'border-gold-500 bg-gold-50' : 'border-slate-100 hover:border-slate-300']"
            >
              <input type="radio" :value="t.id" v-model="form.treatment_id" @change="onTreatmentSelected" class="hidden" />
              <div class="flex-1">
                <p class="font-medium text-slate-800">{{ t.name }}</p>
                <p class="text-sm text-slate-500">{{ t.duration_minutes }} min · Rp {{ formatPrice(t.price) }}</p>
              </div>
            </label>
          </div>
        </div>

        <!-- Section 3: Date & Time -->
        <div class="card" :class="{'opacity-50 pointer-events-none': !form.treatment_id}">
          <h2 class="font-semibold text-lg mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center text-sm">3</span>
            Schedule
          </h2>
          <input type="date" v-model="form.date" :min="today" :max="maxDate" class="input mb-4" @change="loadSlots" />
          
          <div v-if="slots.length" class="grid grid-cols-3 sm:grid-cols-4 gap-2">
            <button type="button" v-for="slot in slots" :key="slot.time"
              @click="form.appointment_date = slot.datetime"
              :class="['p-2 rounded-lg text-sm font-medium border-2 transition-colors',
                       form.appointment_date === slot.datetime ? 'border-gold-500 bg-gold-50 text-gold-700' : 'border-slate-200 hover:border-gold-300']"
            >{{ slot.time }}</button>
          </div>
          <p v-else-if="form.date" class="text-slate-400 text-sm">No available slots for this date.</p>
          <p v-else class="text-slate-400 text-sm">Please select a date to view available slots.</p>
        </div>

        <!-- Section 4: Identity -->
        <div class="card" :class="{'opacity-50 pointer-events-none': !form.appointment_date}">
          <h2 class="font-semibold text-lg mb-4 flex items-center gap-2">
            <span class="w-6 h-6 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center text-sm">4</span>
            Your Details
          </h2>
          <div class="space-y-4">
            <div>
              <input v-model="form.customer_name" placeholder="Full Name *" class="input" :class="{'border-red-500': validationErrors?.customer_name}" required />
              <p v-if="validationErrors?.customer_name" class="text-red-500 text-xs mt-1">{{ validationErrors.customer_name[0] }}</p>
            </div>
            <div>
              <input v-model="form.customer_phone" placeholder="Phone (e.g., 08123456789) *" class="input" :class="{'border-red-500 focus:border-red-500 focus:ring-red-200': validationErrors?.customer_phone}" required />
              <p v-if="validationErrors?.customer_phone" class="text-red-500 text-xs mt-1 font-medium">{{ validationErrors.customer_phone[0] }}</p>
            </div>
            <textarea v-model="form.customer_concern" placeholder="Skin concern / notes (optional)" class="input h-24 resize-none" />
          </div>
        </div>

        <!-- Validation Errors -->
        <div v-if="validationErrors" class="p-4 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100 shadow-sm">
          <ul class="list-disc list-inside space-y-1">
            <li v-for="(errors, field) in validationErrors" :key="field">{{ errors[0] }}</li>
          </ul>
        </div>
        <p v-else-if="errorMessage" class="text-red-600 text-sm text-center font-medium">{{ errorMessage }}</p>

        <!-- Submit Button -->
        <button type="submit" :disabled="!isFormValid || store.isLoading" class="btn-primary w-full py-4 text-lg shadow-gold-500/30">
          {{ store.isLoading ? 'Processing...' : 'Confirm Booking' }}
        </button>
      </form>

      <!-- Success State -->
      <div v-else class="card text-center py-16">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 text-green-500">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h2 class="text-3xl font-bold text-slate-800 mb-3">Booking Confirmed!</h2>
        <p class="text-slate-500 mb-8 text-lg">Thank you, {{ form.customer_name }}. Our team will contact you via WhatsApp shortly to confirm your appointment.</p>
        <RouterLink to="/dashboard" class="btn-primary px-8">Go to My Dashboard</RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useAppointmentStore } from '@/stores/appointmentStore'

const store = useAppointmentStore()
const route = useRoute()

const isSubmitted = ref(false)
const slots = ref([])

const today   = new Date().toISOString().split('T')[0]
const maxDate = new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0]

const form = ref({
  branch_id:        null,
  treatment_id:     route.query.treatment ? Number(route.query.treatment) : null,
  date:             '',
  appointment_date: '',
  customer_name:    '',
  customer_phone:   '',
  customer_concern: '',
})

const validationErrors = ref(null)
const errorMessage = ref('')

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n)

// Fetch slots when a date is selected
const loadSlots = async () => {
  form.value.appointment_date = '' // reset slot
  if (!form.value.branch_id || !form.value.treatment_id || !form.value.date) return
  slots.value = await store.fetchSlots(form.value.branch_id, form.value.treatment_id, form.value.date)
}

const onBranchSelected = async () => {
  form.value.appointment_date = ''
  
  if (form.value.branch_id) {
    const available = await store.fetchTreatments(form.value.branch_id)
    if (!available.some(t => t.id === form.value.treatment_id)) {
      form.value.treatment_id = null
    }
  } else {
    form.value.treatment_id = null
  }

  if (form.value.date && form.value.treatment_id) {
    loadSlots()
  }
}

// Reset slots if treatment is changed
const onTreatmentSelected = () => {
  form.value.appointment_date = ''
  if (form.value.date && form.value.branch_id) {
    loadSlots()
  }
}

const isFormValid = computed(() => {
  return form.value.branch_id &&
         form.value.treatment_id && 
         form.value.appointment_date && 
         form.value.customer_name && 
         form.value.customer_phone;
})

const submit = async () => {
  validationErrors.value = null
  errorMessage.value = ''
  
  try {
    await store.bookAppointment({
      branch_id:        form.value.branch_id,
      treatment_id:     form.value.treatment_id,
      appointment_date: form.value.appointment_date,
      customer_name:    form.value.customer_name,
      customer_phone:   form.value.customer_phone,
      customer_concern: form.value.customer_concern,
    })
    isSubmitted.value = true
  } catch (err) {
    if (err.errors) {
      validationErrors.value = err.errors
    } else {
      errorMessage.value = err.message || 'Booking failed. Please try again.'
    }
  }
}

onMounted(async () => {
  await store.fetchBranches()
  // We don't fetch all treatments initially, 
  // only when a branch is selected, or if branch_id is pre-filled.
  if (form.value.branch_id) {
    await store.fetchTreatments(form.value.branch_id)
  }
})
</script>
