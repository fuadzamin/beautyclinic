import axios from 'axios'
import { useAuthStore } from '@/stores/authStore'

const client = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept':       'application/json',
  },
  timeout: 10000,
})

// Request interceptor: attach Bearer token
client.interceptors.request.use((config) => {
  const auth = useAuthStore()
  if (auth.token) {
    config.headers.Authorization = `Bearer ${auth.token}`
  }
  return config
}, (error) => Promise.reject(error))

// Response interceptor: handle 401 globally
client.interceptors.response.use(
  (response) => response.data,
  (error) => {
    if (error.response?.status === 401) {
      const auth = useAuthStore()
      auth.logout()
      window.location.href = '/login'
    }
    return Promise.reject(error.response?.data || error.message)
  }
)

export default client
