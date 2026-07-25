<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100">
      <h1 class="text-3xl font-black text-slate-900 text-center mb-2 tracking-tight">{{ requires2fa ? 'Two-Factor Authentication' : 'Sign In' }}</h1>
      <p v-if="!requires2fa" class="text-center text-slate-500 text-sm font-medium mb-8">Welcome back to Aura Clinic</p>
      
      <!-- Standard Login Form -->
      <form v-if="!requires2fa" @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide ml-1 mb-2">Email</label>
          <input v-model="form.email" type="email" placeholder="admin@klinik.com" class="w-full h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" required />
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide ml-1 mb-2">Password</label>
          <input v-model="form.password" type="password" placeholder="••••••••" class="w-full h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" required />
        </div>
        <p v-if="error" class="text-rose-500 text-sm font-medium bg-rose-50 p-3 rounded-xl border border-rose-100">{{ error }}</p>
        <button type="submit" class="btn-premium w-full h-12 text-lg shadow-xl shadow-gold-500/20" :disabled="isLoading">
          <span v-if="isLoading" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Signing in...
          </span>
          <span v-else>Login</span>
        </button>
      </form>

      <!-- 2FA Verification Form -->
      <form v-else @submit.prevent="handle2fa" class="space-y-5 text-center mt-6">
        <p class="text-slate-500 font-medium text-sm mb-4">Please enter the 6-digit code from your authenticator app.</p>
        <input 
          v-model="twoFactorCode" 
          type="text" 
          placeholder="000000" 
          maxlength="6" 
          class="w-full h-16 rounded-2xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all text-center text-3xl tracking-[0.5em] font-black text-slate-900" 
          required 
          autoFocus
        />
        <p v-if="error" class="text-rose-500 text-sm font-medium bg-rose-50 p-3 rounded-xl border border-rose-100">{{ error }}</p>
        <button type="submit" class="btn-premium w-full h-12 text-lg shadow-xl shadow-gold-500/20" :disabled="isLoading">
          <span v-if="isLoading" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Verifying...
          </span>
          <span v-else>Verify & Login</span>
        </button>
        <button type="button" @click="requires2fa = false" class="text-sm font-bold text-slate-400 hover:text-rose-500 transition-colors mt-4 block w-full">
          &larr; Back to Login
        </button>
      </form>

      <p v-if="!requires2fa" class="text-center mt-8 text-slate-500 text-sm font-medium">
        Don't have an account? <RouterLink to="/register" class="font-bold text-gold-600 hover:text-gold-500 transition-colors">Register here</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const route  = useRoute()
const auth   = useAuthStore()

const form = ref({
  email:    '',
  password: '',
})

const isLoading = ref(false)
const error = ref('')
const requires2fa = ref(false)
const staffId = ref(null)
const twoFactorCode = ref('')

const submit = async () => {
  isLoading.value = true
  error.value = ''
  try {
    const res = await auth.login(form.value.email, form.value.password)
    
    if (res?.requires_2fa) {
      requires2fa.value = true
      staffId.value = res.staff_id
      return
    }

    _redirect()
  } catch (err) {
    error.value = err?.message || 'Login failed. Please check your credentials.'
  } finally {
    isLoading.value = false
  }
}

const handle2fa = async () => {
  if (twoFactorCode.value.length !== 6) {
    error.value = 'Kode 2FA harus 6 digit.'
    return
  }
  isLoading.value = true
  error.value = ''
  try {
    await auth.verify2fa(staffId.value, twoFactorCode.value)
    _redirect()
  } catch (err) {
    error.value = err?.message || 'Invalid 2FA code.'
  } finally {
    isLoading.value = false
  }
}

const _redirect = () => {
  if (auth.isAdmin) {
    router.push(route.query.redirect || '/admin/dashboard')
  } else {
    router.push(route.query.redirect || '/dashboard')
  }
}
</script>
