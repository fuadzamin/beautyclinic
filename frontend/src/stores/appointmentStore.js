import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '@/api/client'

export const useAppointmentStore = defineStore('appointment', () => {
  const appointments      = ref([])
  const selectedAppointment = ref(null)
  const availableSlots    = ref([])
  const treatments        = ref([])
  const branches          = ref([])
  const isLoading         = ref(false)

  async function fetchTreatments(branchId = null) {
    const res = await client.get('/treatments', { params: { branch_id: branchId } })
    treatments.value = res.data
    return res.data
  }

  async function fetchBranches() {
    const res = await client.get('/branches')
    branches.value = res.data || []
    return res.data
  }

  async function fetchSlots(branchId, treatmentId, date) {
    isLoading.value = true
    try {
      const res = await client.get('/appointments/available-slots', {
        params: { branch_id: branchId, treatment_id: treatmentId, date },
      })
      availableSlots.value = res.data
      return res.data
    } finally {
      isLoading.value = false
    }
  }

  async function bookAppointment(payload) {
    isLoading.value = true
    try {
      const res = await client.post('/appointments', payload)
      return res.data
    } finally {
      isLoading.value = false
    }
  }

  async function fetchMyAppointments() {
    const res = await client.get('/user/appointments')
    appointments.value = res.data
    return res.data
  }

  return {
    appointments,
    selectedAppointment,
    availableSlots,
    treatments,
    branches,
    isLoading,
    fetchTreatments,
    fetchBranches,
    fetchSlots,
    bookAppointment,
    fetchMyAppointments,
  }
})
