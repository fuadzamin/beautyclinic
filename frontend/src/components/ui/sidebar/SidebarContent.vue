<script setup>
import { cn } from "@/lib/utils"

const props = defineProps({
  class: { type: String, default: "" }
})
</script>

<template>
  <div :class="cn('flex min-h-0 flex-1 flex-col gap-2 overflow-auto group-data-[collapsible=icon]/sidebar-wrapper:overflow-hidden', props.class)">
    <slot />
  </div>
</template>
