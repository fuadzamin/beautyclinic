import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const CART_KEY = 'bc_cart'

export const useCartStore = defineStore('cart', () => {
  const items = ref(JSON.parse(localStorage.getItem(CART_KEY) || '[]'))

  const totalItems  = computed(() => items.value.reduce((sum, i) => sum + i.quantity, 0))
  const totalPrice  = computed(() => items.value.reduce((sum, i) => sum + (i.price * i.quantity), 0))

  function addItem(product, quantity = 1) {
    const existing = items.value.find(i => i.product_id === product.id)
    if (existing) {
      existing.quantity += quantity
    } else {
      items.value.push({
        product_id: product.id,
        name:       product.name,
        price:      product.price,
        image_url:  product.image_url,
        quantity,
      })
    }
    _persist()
  }

  function updateQuantity(productId, quantity) {
    const item = items.value.find(i => i.product_id === productId)
    if (item) {
      if (quantity <= 0) {
        removeItem(productId)
      } else {
        item.quantity = quantity
        _persist()
      }
    }
  }

  function removeItem(productId) {
    items.value = items.value.filter(i => i.product_id !== productId)
    _persist()
  }

  function clearCart() {
    items.value = []
    _persist()
  }

  function _persist() {
    localStorage.setItem(CART_KEY, JSON.stringify(items.value))
  }

  return { items, totalItems, totalPrice, addItem, updateQuantity, removeItem, clearCart }
})
