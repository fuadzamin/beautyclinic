<template>
  <div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-slate-800 mb-2">Shop Products</h1>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3 mb-8">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="activeCategory = cat === activeCategory ? '' : cat"
          :class="['badge text-xs px-3 py-1.5 cursor-pointer capitalize transition-colors',
                   activeCategory === cat ? 'bg-gold-500 text-white' : 'bg-white text-slate-600 border border-slate-200']"
        >{{ cat.replace('_', ' ') }}</button>
      </div>

      <!-- Products grid -->
      <div v-if="store.isLoading" class="text-center py-20 text-slate-400">Loading...</div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="product in store.products"
          :key="product.id"
          class="card hover:shadow-md transition-shadow group"
        >
          <RouterLink :to="{ name: 'product-detail', params: { id: product.id } }">
            <img
              :src="product.image_url || '/placeholder-product.jpg'"
              :alt="product.name"
              class="w-full h-48 object-cover rounded-xl mb-4 group-hover:opacity-90 transition-opacity"
            />
          </RouterLink>
          <h2 class="font-semibold text-slate-800 mb-1 truncate">{{ product.name }}</h2>
          <p class="text-gold-600 font-bold mb-3">Rp {{ formatPrice(product.price) }}</p>
          <button @click="addToCart(product)" class="btn-primary w-full text-sm">
            Add to Cart
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useProductStore } from '@/stores/productStore'
import { useCartStore } from '@/stores/cartStore'
import { toast } from 'vue-sonner'

const store    = useProductStore()
const cart     = useCartStore()
const activeCategory = ref('')

const categories = ['serum', 'sunscreen', 'moisturizer', 'cleanser', 'acne_treatment', 'mask', 'body_care', 'soap']
const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n)

const addToCart = (product) => {
  cart.addItem(product)
  toast.success(`${product.name} added to cart!`)
}

watch(activeCategory, (val) => store.fetchProducts({ category: val || undefined }))
onMounted(() => store.fetchProducts())
</script>
