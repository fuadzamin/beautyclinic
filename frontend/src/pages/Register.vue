<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
      <h1 class="text-3xl font-black text-slate-900 text-center mb-2 tracking-tight">Create Account</h1>
      <p class="text-center text-slate-500 text-sm font-medium mb-8">Join Aura Clinic today</p>
      
      <form @submit.prevent="handleRegister" class="space-y-5">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide ml-1 mb-2">Full Name</label>
          <input v-model="form.name" placeholder="John Doe" class="w-full h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" required />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide ml-1 mb-2">Email Address</label>
          <input v-model="form.email" type="email" placeholder="john@example.com" class="w-full h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" required />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide ml-1 mb-2">Phone Number</label>
          <input v-model="form.phone" placeholder="08123456789" class="w-full h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide ml-1 mb-2">Password</label>
          <input v-model="form.password" type="password" placeholder="Min 8 chars, 1 letter, 1 number" class="w-full h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" required />
          <p class="text-[10px] text-slate-400 font-bold ml-1 mt-1">* Password must contain at least one letter and one number.</p>
        </div>
        
        <p v-if="error" class="text-rose-500 text-sm font-medium bg-rose-50 p-3 rounded-xl border border-rose-100">{{ error }}</p>
        
        <button type="submit" class="btn-premium w-full h-12 text-lg shadow-xl shadow-gold-500/20 mt-2" :disabled="isLoading">
          <span v-if="isLoading" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Registering...
          </span>
          <span v-else>Register</span>
        </button>
      </form>
      
      <p class="text-center mt-8 text-slate-500 text-sm font-medium">
        Already have an account? <RouterLink to="/login" class="font-bold text-gold-600 hover:text-gold-500 transition-colors">Login here</RouterLink>
      </p>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
const auth = useAuthStore()
const router = useRouter()
const form    = ref({ name: '', email: '', phone: '', password: '' })
const error   = ref('')
const isLoading = ref(false)
const handleRegister = async () => {
  isLoading.value = true
  error.value = ''
  try {
    await auth.register(form.value)
    router.push({ name: 'dashboard' })
  } catch (err) {
    error.value = err.message || 'Registration failed.'
  } finally {
    isLoading.value = false
  }
}
</script>
