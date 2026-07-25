<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-slate-800 mb-2">Our Treatments</h1>
      <p class="text-slate-500 mb-8">Choose from our range of premium beauty treatments</p>
      
      <div v-if="isLoading" class="text-center py-20 text-slate-400">Loading...</div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="treatment in treatments"
          :key="treatment.id"
          class="card group flex flex-col hover:border-gold-200 p-0 overflow-hidden"
        >
          <!-- Image Section -->
          <div class="relative overflow-hidden aspect-[4/3]">
            <img 
              :src="treatment.image_url || '/placeholder-product.jpg'" 
              :alt="treatment.name" 
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
            />
          </div>
          
          <!-- Content Section -->
          <div class="p-6 flex flex-col flex-grow">
            <h2 class="font-semibold text-xl text-slate-800 mb-2 group-hover:text-gold-600 transition-colors">{{ treatment.name }}</h2>
            <p class="text-slate-500 text-sm mb-6 flex-grow">{{ treatment.description }}</p>
            
            <div class="flex items-center justify-between mb-6">
              <span class="text-gold-600 font-bold text-lg">Rp {{ formatPrice(treatment.price) }}</span>
              <span class="text-slate-400 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ treatment.duration_minutes }} min
              </span>
            </div>
            
            <RouterLink :to="{ name: 'booking', query: { treatment: treatment.id } }" class="btn-primary w-full text-center">
              Book Now
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAppointmentStore } from '@/stores/appointmentStore'

const store      = useAppointmentStore()
const treatments = ref([])
const isLoading  = ref(false)

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n)

onMounted(async () => {
  isLoading.value = true
  treatments.value = await store.fetchTreatments()
  isLoading.value = false
})
</script>
