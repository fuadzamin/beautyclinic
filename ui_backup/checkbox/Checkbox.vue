<script setup>
import { CheckboxIndicator, CheckboxRoot } from 'reka-ui'
import { computed } from 'vue'
import { Check } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

const props = defineProps({
  class: { type: null, required: false },
  checked: { type: [Boolean, String], required: false },
  disabled: { type: Boolean, required: false },
})

const emits = defineEmits(['update:checked'])

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})
</script>

<template>
  <CheckboxRoot
    v-bind="delegatedProps"
    :class="cn(
      'peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground',
      props.class,
    )"
    @update:checked="emits('update:checked', $event)"
  >
    <CheckboxIndicator class="flex h-full w-full items-center justify-center text-current">
      <slot>
        <Check class="h-4 w-4" />
      </slot>
    </CheckboxIndicator>
  </CheckboxRoot>
</template>
