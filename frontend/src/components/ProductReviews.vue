<template>
  <div class="mt-12 bg-white rounded-2xl border border-slate-100 p-6 sm:p-8 shadow-sm">
    <h2 class="text-xl font-bold text-slate-800 mb-6">Product Reviews</h2>

    <!-- Summary of Ratings -->
    <div v-if="reviews.length > 0" class="flex items-center gap-6 mb-8 pb-6 border-b border-slate-100">
      <div class="text-center">
        <p class="text-5xl font-black text-slate-800">{{ averageRating.toFixed(1) }}</p>
        <div class="flex items-center justify-center gap-1 my-2 text-amber-500">
          <Star v-for="i in 5" :key="i" class="w-5 h-5" :class="i <= Math.round(averageRating) ? 'fill-amber-500' : 'text-slate-200'" />
        </div>
        <p class="text-xs text-slate-400 font-medium">{{ reviews.length }} reviews</p>
      </div>
    </div>

    <!-- Add Review Form (only for logged-in users) -->
    <div v-if="auth.isAuthenticated" class="mb-8 p-4 bg-slate-50 rounded-xl border border-slate-100">
      <h3 class="text-sm font-bold text-slate-800 mb-4">Write a Review</h3>
      <form @submit.prevent="submitReview" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Rating</label>
          <div class="flex items-center gap-1.5">
            <button v-for="i in 5" :key="i" type="button" @click="form.rating = i" class="text-amber-500 transition-transform active:scale-95">
              <Star class="w-6 h-6" :class="i <= form.rating ? 'fill-amber-500' : 'text-slate-200'" />
            </button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Comment</label>
          <textarea v-model="form.comment" rows="3" class="w-full text-sm bg-white rounded-xl border border-slate-200 p-3 outline-none focus:border-gold-500 transition-colors" placeholder="Tell us what you think..."></textarea>
        </div>
        <button type="submit" :disabled="submitting" class="btn-primary py-2 px-5 text-sm h-10 w-fit">
          {{ submitting ? 'Submitting...' : 'Submit Review' }}
        </button>
      </form>
    </div>
    <div v-else class="mb-8 p-4 bg-slate-50 rounded-xl text-center text-xs text-slate-500">
      Please <RouterLink to="/login" class="text-gold-600 font-bold underline">Sign In</RouterLink> to write a review.
    </div>

    <!-- Reviews List -->
    <div v-if="loading" class="text-center py-6 text-slate-400 text-sm">Loading reviews...</div>
    <div v-else-if="reviews.length === 0" class="text-center py-8 text-slate-400 text-sm">No reviews yet for this product.</div>
    <div v-else class="space-y-6">
      <div v-for="r in reviews" :key="r.id" class="pb-6 border-b border-slate-100 last:border-b-0 last:pb-0">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm font-bold text-slate-800">{{ r.user?.name || 'Anonymous' }}</p>
            <div class="flex items-center gap-1 mt-1 text-amber-500">
              <Star v-for="i in 5" :key="i" class="w-3.5 h-3.5" :class="i <= r.rating ? 'fill-amber-500' : 'text-slate-200'" />
            </div>
          </div>
          <span class="text-[10px] text-slate-400 font-medium">{{ formatDate(r.created_at) }}</span>
        </div>
        <p class="text-sm text-slate-600 mt-2.5 leading-relaxed">{{ r.comment }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Star } from 'lucide-vue-next'
import { useAuthStore } from '@/stores/authStore'
import client from '@/api/client'

const props = defineProps({
  productId: { type: [String, Number], required: true }
})

const auth = useAuthStore()
const reviews = ref([])
const loading = ref(false)
const submitting = ref(false)
const form = ref({ rating: 5, comment: '' })

const averageRating = computed(() => {
  if (reviews.value.length === 0) return 0
  const sum = reviews.value.reduce((acc, r) => acc + r.rating, 0)
  return sum / reviews.value.length
})

const fetchReviews = async () => {
  loading.value = true
  try {
    const res = await client.get(`/products/${props.productId}/reviews`)
    reviews.value = res.data?.data?.data || []
  } catch (_) {}
  finally { loading.value = false }
}

const submitReview = async () => {
  submitting.value = true
  try {
    await client.post('/reviews', {
      product_id: props.productId,
      rating: form.value.rating,
      comment: form.value.comment
    })
    form.value.comment = ''
    form.value.rating = 5
    await fetchReviews()
  } catch (_) {}
  finally { submitting.value = false }
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

onMounted(fetchReviews)
</script>
