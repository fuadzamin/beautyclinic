<script setup>
import { cn } from "@/lib/utils";

const props = defineProps({
  class: {
    type: [Boolean, null, String, Object, Array],
    required: false,
    skipCheck: true,
  },
});
</script>

<template>
  <div
    :class="
      cn(
        'rounded-2xl border border-slate-100 bg-card text-card-foreground shadow-sm hover:shadow-md transition-all duration-300',
        props.class,
      )
    "
  >
    <slot />
  </div>
</template>
