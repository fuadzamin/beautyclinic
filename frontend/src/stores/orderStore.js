import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '@/api/client'

export const useOrderStore = defineStore('order', () => {
  const orders      = ref([])
  const selectedOrder = ref(null)
  const isLoading   = ref(false)

  async function placeOrder(payload) {
    isLoading.value = true
    try {
      const res = await client.post('/orders', payload)
      return res.data
    } finally {
      isLoading.value = false
    }
  }

  async function fetchMyOrders() {
    const res = await client.get('/user/orders')
    orders.value = res.data.data
    return res.data
  }

  async function fetchOrder(id) {
    const res = await client.get(`/orders/${id}`)
    selectedOrder.value = res.data
    return res.data
  }

  return { orders, selectedOrder, isLoading, placeOrder, fetchMyOrders, fetchOrder }
})
