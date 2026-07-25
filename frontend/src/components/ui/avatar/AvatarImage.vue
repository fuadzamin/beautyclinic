<script setup>
import { AvatarImage } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps({
  src: { type: String, required: false },
  alt: { type: String, required: false },
  class: { type: null, required: false },
})
</script>

<template>
  <AvatarImage
    v-bind="props"
    :class="cn('aspect-square h-full w-full object-cover', props.class)"
  />
</template>
