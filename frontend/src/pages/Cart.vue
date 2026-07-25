<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-4xl mx-auto flex flex-col lg:flex-row gap-8">
      <!-- Cart Items -->
      <div class="flex-1">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Shopping Cart</h1>
        <div v-if="cart.items.length === 0" class="card text-center py-16">
          <p class="text-slate-400 text-lg">Your cart is empty</p>
          <RouterLink to="/products" class="btn-primary mt-4 inline-block">Shop Now</RouterLink>
        </div>
        <div v-else>
          <div v-for="item in cart.items" :key="item.product_id" class="card mb-4 flex items-center gap-4">
            <img :src="item.image_url || '/placeholder-product.jpg'" class="w-16 h-16 object-cover rounded-xl" />
            <div class="flex-1">
              <p class="font-semibold text-slate-800">{{ item.name }}</p>
              <p class="text-gold-600">Rp {{ formatPrice(item.price) }}</p>
            </div>
            <div class="flex items-center gap-2">
              <button @click="cart.updateQuantity(item.product_id, item.quantity - 1)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold">-</button>
              <span class="w-8 text-center font-medium">{{ item.quantity }}</span>
              <button @click="cart.updateQuantity(item.product_id, item.quantity + 1)" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold">+</button>
            </div>
            <p class="font-semibold w-24 text-right">Rp {{ formatPrice(item.price * item.quantity) }}</p>
            <button @click="cart.removeItem(item.product_id)" class="text-slate-400 hover:text-red-500 ml-2">✕</button>
          </div>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="w-full lg:w-96" v-if="cart.items.length > 0">
        <div class="card sticky top-24">
          <h2 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Checkout Details</h2>
          
          <form @submit.prevent="submitOrder" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Your Name</label>
              <input v-model="form.customer_name" type="text" class="input py-2" required placeholder="John Doe" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">WhatsApp Number</label>
              <input v-model="form.customer_phone" type="text" class="input py-2" required placeholder="62812345678" />
              <p class="text-xs text-slate-400 mt-1">Start with 62</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Delivery Method</label>
              <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="form.delivery_method = 'pickup'" :class="form.delivery_method === 'pickup' ? 'bg-gold-50 border-gold-200 text-gold-700' : 'bg-slate-50 border-slate-200 text-slate-600'" class="border rounded-xl py-2 text-sm font-medium transition-colors">
                  Pickup (Ambil Sendiri)
                </button>
                <button type="button" @click="form.delivery_method = 'shipping'" :class="form.delivery_method === 'shipping' ? 'bg-gold-50 border-gold-200 text-gold-700' : 'bg-slate-50 border-slate-200 text-slate-600'" class="border rounded-xl py-2 text-sm font-medium transition-colors">
                  Shipping (Kirim)
                </button>
              </div>
            </div>

            <!-- Branch Selection for Pickup -->
            <div v-if="form.delivery_method === 'pickup'">
              <label class="block text-sm font-medium text-slate-700 mb-1">Choose Clinic Branch</label>
              <select v-model="form.branch_id" class="input py-2" required>
                <option :value="null" disabled>Select Branch...</option>
                <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
              </select>
            </div>

            <!-- Address for Shipping -->
            <div v-if="form.delivery_method === 'shipping'">
              <label class="block text-sm font-medium text-slate-700 mb-1">Shipping Address</label>
              <textarea v-model="form.shipping_address" class="input py-2 h-20 resize-none" required placeholder="Jalan Mawar No. 12, RT/RW..."></textarea>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-4">
              <div class="flex justify-between text-lg font-bold mb-4">
                <span>Total</span>
                <span class="text-gold-600">Rp {{ formatPrice(cart.totalPrice) }}</span>
              </div>
              <button type="submit" class="btn-primary w-full text-center block" :disabled="isSubmitting">
                {{ isSubmitting ? 'Processing...' : 'Place Order via WhatsApp' }}
              </button>
              <p v-if="errorMsg" class="text-rose-500 text-sm mt-2 font-medium">{{ errorMsg }}</p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import { useAuthStore } from '@/stores/authStore'
import client from '@/api/client'

const cart = useCartStore()
const auth = useAuthStore()
const router = useRouter()
const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n)

const branches = ref([])
const isSubmitting = ref(false)
const errorMsg = ref('')

const form = ref({
  customer_name: auth.user?.name || '',
  customer_phone: auth.user?.phone || '',
  delivery_method: 'pickup',
  branch_id: null,
  shipping_address: '',
  notes: ''
})

onMounted(async () => {
  try {
    const res = await client.get('/branches')
    branches.value = res.data.data || res.data || []
  } catch (err) {
    console.error(err)
  }
})

const submitOrder = async () => {
  errorMsg.value = ''
  isSubmitting.value = true
  try {
    const payload = {
      ...form.value,
      items: cart.items.map(i => ({
        product_id: i.product_id,
        quantity: i.quantity
      }))
    }
    
    // Create order via API
    const res = await client.post('/orders', payload)
    
    // If successful, get WA link from API
    const waUrl = res.data?.whatsapp_url || res.data?.data?.whatsapp_url
    if (!waUrl) throw new Error('Failed to generate WhatsApp link.')

    // Clear cart and redirect
    cart.clearCart()
    window.open(waUrl, '_blank')
    router.push('/')
  } catch (err) {
    if (err.response?.data?.message) {
      errorMsg.value = err.response.data.message
    } else {
      errorMsg.value = 'Failed to process order. Please try again.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
