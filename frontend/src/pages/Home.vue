<template>
  <div class="bg-white">
    <!-- Hero Section -->
    <section class="relative min-h-[85vh] flex items-center overflow-hidden">
      <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=2000&q=80" alt="Clinic Hero" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-black/60" />
      </div>
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 text-sm mb-6">
            <Sparkles class="w-4 h-4 text-gold-400" />
            Premium Beauty & Wellness
          </div>
          <h1 class="text-5xl md:text-7xl font-extrabold text-white tracking-tight mb-6 leading-tight">
            Reveal Your
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-gold-300 to-gold-400">Natural Glow</span>
          </h1>
          <p class="text-lg md:text-xl text-white/70 mb-10 leading-relaxed max-w-xl">
            Experience premium aesthetic treatments and clinically-proven skincare designed exclusively to bring out the best version of you.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <Button as-child variant="premium" size="lg">
              <RouterLink to="/booking">
                Book Appointment
                <ArrowRight class="ml-2 w-5 h-5" />
              </RouterLink>
            </Button>
            <Button as-child variant="outline" size="lg" class="border-white/30 text-white bg-white/10 hover:bg-white/20 hover:border-white/50 hover:text-gold-400">
              <RouterLink to="/products">
                Shop Skincare
              </RouterLink>
            </Button>
          </div>
        </div>
      </div>
      <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-white to-transparent" />
    </section>

    <!-- Signature Treatments -->
    <section class="py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
          <div>
            <p class="text-gold-600 font-semibold text-sm tracking-widest uppercase mb-2">Our Services</p>
            <h2 class="text-4xl font-bold text-slate-900">Signature Treatments</h2>
            <p class="text-slate-500 mt-2">Tailored solutions for every skin concern.</p>
          </div>
          <Button as-child variant="ghost" class="hidden sm:flex">
            <RouterLink to="/treatments">
              View All <ChevronRight class="w-4 h-4 ml-1" />
            </RouterLink>
          </Button>
        </div>

        <div v-if="treatmentsStore.isLoading" class="flex justify-center py-12">
          <Loader2 class="w-8 h-8 text-gold-500 animate-spin" />
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="treatment in treatmentsStore.treatments.slice(0, 3)"
            :key="treatment.id"
            class="group relative rounded-2xl overflow-hidden bg-slate-50"
          >
            <div class="aspect-[4/5] overflow-hidden">
              <img
                :src="treatment.image_url"
                :alt="treatment.name"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
              />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-60 group-hover:opacity-90 transition-opacity duration-500" />
            <div class="absolute inset-0 flex flex-col justify-end p-6">
              <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                <h3 class="text-xl font-bold text-white mb-1">{{ treatment.name }}</h3>
                <p class="text-white/60 text-sm mb-3 line-clamp-2">{{ treatment.description }}</p>
                <div class="flex items-center justify-between mb-4">
                  <span class="text-gold-400 font-bold text-lg">Rp {{ formatPrice(treatment.price) }}</span>
                  <span class="text-white/50 text-sm flex items-center"><Clock class="w-4 h-4 mr-1" /> {{ treatment.duration_minutes }} min</span>
                </div>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                  <Button as-child variant="premium" size="sm" class="w-full">
                    <RouterLink :to="{ name: 'booking', query: { treatment: treatment.id } }">
                      Book This Treatment
                    </RouterLink>
                  </Button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-8 sm:hidden">
          <Button as-child variant="ghost">
            <RouterLink to="/treatments">
              View All Treatments <ChevronRight class="w-4 h-4 ml-1" />
            </RouterLink>
          </Button>
        </div>
      </div>
    </section>

    <!-- Why Choose Us -->
    <section class="relative bg-slate-900 py-24 overflow-hidden">
      <div class="absolute inset-0 opacity-5">
        <div class="absolute top-10 left-10 w-72 h-72 rounded-full bg-gold-500 blur-3xl" />
        <div class="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-gold-500 blur-3xl" />
      </div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-gold-400 font-semibold text-sm tracking-widest uppercase mb-3">Why Choose Us</p>
        <h2 class="text-4xl font-bold text-white mb-16">The Aura Standard</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
          <div class="group flex flex-col items-center">
            <div class="w-20 h-20 rounded-2xl bg-gold-500/10 border border-gold-500/20 flex items-center justify-center text-gold-400 mb-6 group-hover:bg-gold-500/20 group-hover:scale-110 transition-all duration-500">
              <Star class="w-9 h-9" />
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">Expert Dermatologists</h3>
            <p class="text-slate-400 text-sm leading-relaxed max-w-xs">Our certified professionals bring years of experience to ensure safe and effective results.</p>
          </div>
          <div class="group flex flex-col items-center">
            <div class="w-20 h-20 rounded-2xl bg-gold-500/10 border border-gold-500/20 flex items-center justify-center text-gold-400 mb-6 group-hover:bg-gold-500/20 group-hover:scale-110 transition-all duration-500">
              <ShieldCheck class="w-9 h-9" />
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">Premium Products</h3>
            <p class="text-slate-400 text-sm leading-relaxed max-w-xs">We only use top-tier, clinically tested skincare lines during all our procedures.</p>
          </div>
          <div class="group flex flex-col items-center">
            <div class="w-20 h-20 rounded-2xl bg-gold-500/10 border border-gold-500/20 flex items-center justify-center text-gold-400 mb-6 group-hover:bg-gold-500/20 group-hover:scale-110 transition-all duration-500">
              <Heart class="w-9 h-9" />
            </div>
            <h3 class="text-xl font-semibold text-white mb-3">Personalized Care</h3>
            <p class="text-slate-400 text-sm leading-relaxed max-w-xs">Every skin is unique. We tailor each treatment specifically to your goals and needs.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-24 bg-gradient-to-b from-white to-slate-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
          <div>
            <p class="text-gold-600 font-semibold text-sm tracking-widest uppercase mb-2">Shop</p>
            <h2 class="text-4xl font-bold text-slate-900">Curated Skincare</h2>
            <p class="text-slate-500 mt-2">Bring the clinic experience home.</p>
          </div>
          <Button as-child variant="ghost" class="hidden sm:flex">
            <RouterLink to="/products">
              Shop All <ChevronRight class="w-4 h-4 ml-1" />
            </RouterLink>
          </Button>
        </div>

        <div v-if="productStore.isLoading" class="flex justify-center py-12">
          <Loader2 class="w-8 h-8 text-gold-500 animate-spin" />
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="product in productStore.products.slice(0, 4)"
            :key="product.id"
            class="group rounded-2xl border border-slate-100 bg-white p-5 shadow-sm hover:shadow-lg hover:border-gold-200 transition-all duration-500 flex flex-col"
          >
            <RouterLink :to="{ name: 'product-detail', params: { id: product.id } }" class="flex-grow">
              <div class="aspect-square bg-slate-50 rounded-xl mb-4 overflow-hidden">
                <img
                  :src="product.image_url"
                  :alt="product.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                />
              </div>
              <h3 class="font-semibold text-slate-800 mb-1 group-hover:text-gold-600 transition-colors line-clamp-1">{{ product.name }}</h3>
              <p class="text-slate-400 text-xs uppercase tracking-wider font-medium mb-3">{{ product.category.replace('_', ' ') }}</p>
            </RouterLink>
            <div class="mt-auto">
              <p class="text-gold-600 font-bold mb-4 text-lg">Rp {{ formatPrice(product.price) }}</p>
              <Button variant="outline" size="sm" class="w-full" @click="addToCart(product)">
                <ShoppingBag class="w-4 h-4" />
                Add to Cart
              </Button>
            </div>
          </div>
        </div>

        <div class="text-center mt-8 sm:hidden">
          <Button as-child variant="ghost">
            <RouterLink to="/products">
              Shop All Products <ChevronRight class="w-4 h-4 ml-1" />
            </RouterLink>
          </Button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { ArrowRight, ChevronRight, Clock, Star, ShieldCheck, Heart, Loader2, ShoppingBag, Sparkles } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { toast } from 'vue-sonner'
import { useAppointmentStore } from '@/stores/appointmentStore'
import { useProductStore } from '@/stores/productStore'
import { useCartStore } from '@/stores/cartStore'

const treatmentsStore = useAppointmentStore()
const productStore = useProductStore()
const cartStore = useCartStore()

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n)

const addToCart = (product) => {
  cartStore.addItem(product)
  toast.success(`${product.name} added to cart!`, {
    description: 'Continue shopping or view your cart.',
    action: {
      label: 'View Cart',
      onClick: () => window.location.href = '/cart',
    },
  })
}

onMounted(() => {
  treatmentsStore.fetchTreatments()
  productStore.fetchProducts()
})
</script>