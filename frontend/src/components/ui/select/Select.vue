<script setup>
import { SelectRoot } from 'reka-ui'

const props = defineProps({
  modelValue: { type: [String, Number, null], required: false },
  defaultValue: { type: [String, Number, null], required: false },
  open: { type: Boolean, required: false },
  defaultOpen: { type: Boolean, required: false },
  dir: { type: String, required: false },
  name: { type: String, required: false },
  autocomplete: { type: String, required: false },
  disabled: { type: Boolean, required: false },
  required: { type: Boolean, required: false },
})

const emits = defineEmits(['update:modelValue', 'update:open'])
</script>

<template>
  <SelectRoot v-bind="props" @update:model-value="emits('update:modelValue', $event)" @update:open="emits('update:open', $event)">
    <slot />
  </SelectRoot>
</template>
