<script setup>
import { AvatarFallback } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: null, required: false },
  delayMs: { type: Number, required: false },
})
</script>

<template>
  <AvatarFallback
    v-bind="props"
    :class="cn('flex h-full w-full items-center justify-center rounded-full bg-muted font-bold text-slate-500', props.class)"
  >
    <slot />
  </AvatarFallback>
</template>
