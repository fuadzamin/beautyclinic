import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import client from '@/api/client'

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(JSON.parse(localStorage.getItem('bc_user')  || 'null'))
  const token = ref(localStorage.getItem('bc_token') || null)
  const role  = ref(localStorage.getItem('bc_role')  || null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin         = computed(() => !!role.value)
  const isOwner         = computed(() => role.value === 'owner')
  const isManagerOrOwner= computed(() => role.value === 'owner' || role.value === 'branch_manager')

  async function login(email, password) {
    const res = await client.post('/auth/login', { email, password })
    
    if (res.requires_2fa) {
      return { requires_2fa: true, staff_id: res.staff_id }
    }

    const isStaff = res.data.user.role !== 'customer'
    _setAuth(res.data, isStaff)
    return res
  }

  async function verify2fa(staff_id, code) {
    const res = await client.post('/auth/verify-2fa', { staff_id, code })
    _setAuth(res.data, true)
    return res
  }

  async function register(payload) {
    const res = await client.post('/auth/register', payload)
    _setAuth(res.data)
    return res
  }

  async function logout() {
    try {
      await client.post('/auth/logout')
    } catch (_) { /* ignore errors on logout */ }
    _clearAuth()
  }

  function _setAuth(data, isStaff = false) {
    user.value  = data.user
    token.value = data.access_token
    role.value  = isStaff ? data.user.role : null

    localStorage.setItem('bc_user',  JSON.stringify(data.user))
    localStorage.setItem('bc_token', data.access_token)
    if (isStaff) {
      localStorage.setItem('bc_role', data.user.role)
    }
  }

  function _clearAuth() {
    user.value  = null
    token.value = null
    role.value  = null
    localStorage.removeItem('bc_user')
    localStorage.removeItem('bc_token')
    localStorage.removeItem('bc_role')
  }

  function hasRole(roles) {
    if (!role.value) return false
    return roles.includes(role.value)
  }

  return {
    user,
    token,
    role,
    isAuthenticated,
    isAdmin,
    isOwner,
    isManagerOrOwner,
    hasRole,
    login,
    verify2fa,
    register,
    logout,
  }
}
, { persist: false })
