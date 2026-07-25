<script setup>
import { SelectIcon, SelectTrigger } from 'reka-ui'
import { ChevronDown } from 'lucide-vue-next'
import { computed } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps({
  disabled: { type: Boolean, required: false },
  class: { type: null, required: false },
})

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})
</script>

<template>
  <SelectTrigger
    v-bind="delegatedProps"
    :class="cn(
      'flex h-11 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-gold-400/20 focus:border-gold-400 disabled:cursor-not-allowed disabled:opacity-50 transition-all shadow-sm',
      props.class,
    )"
  >
    <slot />
    <SelectIcon as-child>
      <ChevronDown class="h-4 w-4 opacity-50" />
    </SelectIcon>
  </SelectTrigger>
</template>
