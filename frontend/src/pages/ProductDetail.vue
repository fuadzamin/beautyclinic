<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
      <div v-if="product">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <img :src="product.image_url || '/placeholder-product.jpg'" :alt="product.name" class="w-full rounded-2xl object-cover" />
          <div>
            <h1 class="text-3xl font-bold text-slate-800 mb-2">{{ product.name }}</h1>
            <p class="text-2xl text-gold-600 font-bold mb-4">Rp {{ formatPrice(product.price) }}</p>
            <p class="text-slate-600 mb-6">{{ product.description }}</p>
            <div class="flex items-center gap-4 mb-6">
              <button @click="qty > 1 ? qty-- : null" class="w-10 h-10 rounded-full bg-slate-100 font-bold text-lg">-</button>
              <span class="w-8 text-center font-medium">{{ qty }}</span>
              <button @click="qty++" class="w-10 h-10 rounded-full bg-slate-100 font-bold text-lg">+</button>
            </div>
            <button @click="addToCart" class="btn-primary w-full">Add to Cart</button>
          </div>
        </div>
        <!-- Product Reviews Component -->
        <ProductReviews :product-id="product.id" />
      </div>
      <div v-else class="text-center py-20 text-slate-400">Loading...</div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '@/stores/productStore'
import { useCartStore } from '@/stores/cartStore'
import ProductReviews from '@/components/ProductReviews.vue'

const route = useRoute()
const store = useProductStore()
const cart  = useCartStore()
const product = ref(null)
const qty     = ref(1)
const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n)
const addToCart = () => { cart.addItem(product.value, qty.value); alert('Added to cart!') }
onMounted(async () => { product.value = await store.fetchProduct(route.params.id) })
</script>
