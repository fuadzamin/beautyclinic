<script setup>
import { AvatarRoot } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: null, required: false },
  shape: { type: String, default: 'circle' },
  size: { type: String, default: 'md' },
})

const sizeClasses = {
  sm: 'h-8 w-8 text-xs',
  md: 'h-10 w-10 text-sm',
  lg: 'h-12 w-12 text-base',
}
</script>

<template>
  <AvatarRoot
    :class="cn(
      'relative flex shrink-0 overflow-hidden bg-slate-100',
      props.shape === 'circle' ? 'rounded-full' : 'rounded-md',
      sizeClasses[props.size],
      props.class,
    )"
  >
    <slot />
  </AvatarRoot>
</template>
