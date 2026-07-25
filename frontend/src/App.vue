<template>
  <div class="flex flex-col min-h-screen bg-white">
    <!-- Navbar -->
    <nav v-if="!isAdminRoute" class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gold-100 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <div class="flex items-center">
            <RouterLink to="/" class="flex items-center gap-2 group">
              <div class="w-10 h-10 rounded-full bg-gold-50 flex items-center justify-center text-gold-600 group-hover:bg-gold-100 transition-colors">
                <Sparkles class="w-6 h-6" />
              </div>
              <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gold-600 to-gold-400">
                AURA CLINIC
              </span>
            </RouterLink>
          </div>
          
          <div class="hidden md:flex space-x-8 items-center">
            <RouterLink to="/" class="text-slate-600 hover:text-gold-600 font-medium transition-colors">Home</RouterLink>
            <RouterLink to="/treatments" class="text-slate-600 hover:text-gold-600 font-medium transition-colors">Treatments</RouterLink>
            <RouterLink to="/products" class="text-slate-600 hover:text-gold-600 font-medium transition-colors">Shop</RouterLink>
            <RouterLink to="/about" class="text-slate-600 hover:text-gold-600 font-medium transition-colors">About</RouterLink>
          </div>

          <div class="flex items-center space-x-4">
            <RouterLink to="/cart" class="relative p-2 text-slate-600 hover:text-gold-600 transition-colors">
              <ShoppingBag class="w-6 h-6" />
              <span v-if="cart.totalItems > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-gold-500 rounded-full">
                {{ cart.totalItems }}
              </span>
            </RouterLink>

            <!-- Mobile menu toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-slate-600 hover:text-gold-600 transition-colors">
              <Menu v-if="!mobileMenuOpen" class="w-6 h-6" />
              <X v-else class="w-6 h-6" />
            </button>

            <template v-if="auth.isAuthenticated">
              <RouterLink :to="auth.isAdmin ? '/admin/dashboard' : '/dashboard'" class="btn-secondary hidden md:flex">
                <User class="w-4 h-4 mr-2" />
                Dashboard
              </RouterLink>
            </template>
            <template v-else>
              <RouterLink to="/login" class="btn-primary hidden md:flex">Sign In</RouterLink>
            </template>
          </div>
        </div>

        <!-- Mobile menu dropdown -->
        <div v-if="mobileMenuOpen" class="md:hidden border-t border-gold-100 py-4 space-y-1 animate-in slide-in-from-top-2 duration-200">
          <RouterLink to="/" class="block px-4 py-3 rounded-xl text-slate-600 hover:text-gold-600 hover:bg-gold-50 font-medium transition-colors" @click="mobileMenuOpen = false">Home</RouterLink>
          <RouterLink to="/treatments" class="block px-4 py-3 rounded-xl text-slate-600 hover:text-gold-600 hover:bg-gold-50 font-medium transition-colors" @click="mobileMenuOpen = false">Treatments</RouterLink>
          <RouterLink to="/products" class="block px-4 py-3 rounded-xl text-slate-600 hover:text-gold-600 hover:bg-gold-50 font-medium transition-colors" @click="mobileMenuOpen = false">Shop</RouterLink>
          <RouterLink to="/about" class="block px-4 py-3 rounded-xl text-slate-600 hover:text-gold-600 hover:bg-gold-50 font-medium transition-colors" @click="mobileMenuOpen = false">About</RouterLink>
          <RouterLink to="/contact" class="block px-4 py-3 rounded-xl text-slate-600 hover:text-gold-600 hover:bg-gold-50 font-medium transition-colors" @click="mobileMenuOpen = false">Contact</RouterLink>
          <div class="border-t border-gold-100 pt-3 mt-2">
            <template v-if="auth.isAuthenticated">
              <RouterLink :to="auth.isAdmin ? '/admin/dashboard' : '/dashboard'" class="flex items-center gap-2 px-4 py-3 rounded-xl text-gold-600 hover:bg-gold-50 font-bold transition-colors" @click="mobileMenuOpen = false">
                <User class="w-4 h-4" /> Dashboard
              </RouterLink>
            </template>
            <template v-else>
              <RouterLink to="/login" class="flex items-center gap-2 px-4 py-3 rounded-xl text-gold-600 hover:bg-gold-50 font-bold transition-colors" @click="mobileMenuOpen = false">
                Sign In
              </RouterLink>
              <RouterLink to="/register" class="flex items-center gap-2 px-4 py-3 rounded-xl text-slate-600 hover:bg-gold-50 font-medium transition-colors" @click="mobileMenuOpen = false">
                Register
              </RouterLink>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
      <RouterView />
    </main>

    <!-- Footer -->
    <footer v-if="!isAdminRoute" class="bg-slate-50 border-t border-gold-100 pt-16 pb-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
          <div class="md:col-span-1">
            <div class="flex items-center gap-2 mb-6">
              <div class="w-8 h-8 rounded-full bg-gold-50 flex items-center justify-center text-gold-600">
                <Sparkles class="w-5 h-5" />
              </div>
              <span class="text-lg font-bold text-gold-700">AURA CLINIC</span>
            </div>
            <p class="text-slate-500 text-sm leading-relaxed">
              Experience the pinnacle of beauty and wellness with our premium treatments and curated skincare products.
            </p>
          </div>
          <div>
            <h4 class="text-slate-800 font-semibold mb-6">Explore</h4>
            <ul class="space-y-4 text-sm text-slate-500">
              <li><RouterLink to="/treatments" class="hover:text-gold-600 transition-colors">Our Treatments</RouterLink></li>
              <li><RouterLink to="/products" class="hover:text-gold-600 transition-colors">Skincare Shop</RouterLink></li>
              <li><RouterLink to="/booking" class="hover:text-gold-600 transition-colors">Book Appointment</RouterLink></li>
            </ul>
          </div>
          <div>
            <h4 class="text-slate-800 font-semibold mb-6">Company</h4>
            <ul class="space-y-4 text-sm text-slate-500">
              <li><RouterLink to="/about" class="hover:text-gold-600 transition-colors">About Us</RouterLink></li>
              <li><RouterLink to="/contact" class="hover:text-gold-600 transition-colors">Contact</RouterLink></li>
              <li><a href="#" class="hover:text-gold-600 transition-colors">Privacy Policy</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-slate-800 font-semibold mb-6">Connect</h4>
            <ul class="space-y-4 text-sm text-slate-500">
              <li class="flex items-center gap-3"><MapPin class="w-4 h-4 text-gold-500"/> Jl. Kebumen Raya No. 1</li>
              <li class="flex items-center gap-3"><Phone class="w-4 h-4 text-gold-500"/> +62 812 3456 7890</li>
              <li class="flex items-center gap-3"><Mail class="w-4 h-4 text-gold-500"/> hello@auraclinic.com</li>
            </ul>
          </div>
        </div>
        <div class="border-t border-slate-200 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-slate-400 text-sm">© {{ new Date().getFullYear() }} Aura Beauty Clinic. All rights reserved.</p>
        </div>
      </div>
    </footer>
    <Toaster position="top-right" rich-colors />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { RouterView, RouterLink, useRoute } from 'vue-router'
import { Sparkles, ShoppingBag, User, MapPin, Phone, Mail, Menu, X } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/authStore'
import { useCartStore } from '@/stores/cartStore'
import { Toaster } from '@/components/ui/sonner'

const auth = useAuthStore()
const cart = useCartStore()
const route = useRoute()
const mobileMenuOpen = ref(false)

const isAdminRoute = computed(() => route.path.startsWith('/admin'))
</script>
