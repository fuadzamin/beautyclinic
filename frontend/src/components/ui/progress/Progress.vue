<script setup>
import { ProgressIndicator, ProgressRoot } from 'reka-ui'
import { computed } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: null, required: false },
  modelValue: { type: [Number, null], default: 0 },
  max: { type: Number, default: 100 },
})

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})
</script>

<template>
  <ProgressRoot
    v-bind="delegatedProps"
    :class="cn(
      'relative h-4 w-full overflow-hidden rounded-full bg-slate-100',
      props.class,
    )"
  >
    <ProgressIndicator
      :class="cn('h-full w-full flex-1 bg-gold-500 transition-all')"
      :style="`transform: translateX(-${100 - (props.modelValue || 0)}%);`"
    />
  </ProgressRoot>
</template>
