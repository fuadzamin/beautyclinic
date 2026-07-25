import { defineStore } from 'pinia'
import { ref } from 'vue'
import client from '@/api/client'

export const useProductStore = defineStore('product', () => {
  const products         = ref([])
  const selectedProduct  = ref(null)
  const isLoading        = ref(false)
  const pagination       = ref(null)

  async function fetchProducts(filters = {}) {
    isLoading.value = true
    try {
      const res = await client.get('/products', { params: filters })
      products.value  = res.data.data
      pagination.value = res.data
    } finally {
      isLoading.value = false
    }
  }

  async function fetchProduct(id) {
    isLoading.value = true
    try {
      const res = await client.get(`/products/${id}`)
      selectedProduct.value = res.data
      return res.data
    } finally {
      isLoading.value = false
    }
  }

  return { products, selectedProduct, isLoading, pagination, fetchProducts, fetchProduct }
})
