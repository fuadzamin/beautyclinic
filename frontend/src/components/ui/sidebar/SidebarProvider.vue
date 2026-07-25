<script setup>
import { cn } from '@/lib/utils'
import { useEventListener, useVModel, useMediaQuery } from '@vueuse/core'
import { computed, ref } from 'vue'
import { provideSidebar, SIDEBAR_WIDTH_ICON, SIDEBAR_KEYBOARD_SHORTCUT } from './utils'

const props = defineProps({
  defaultOpen: { type: Boolean, default: true },
  open: { type: Boolean, default: undefined },
  class: { type: String, default: '' }
})

const emits = defineEmits(['update:open'])

const isMobile = useMediaQuery('(max-width: 1023px)')
const openMobile = ref(false)

const _open = useVModel(props, 'open', emits, {
  defaultValue: props.defaultOpen,
  passive: (props.open === undefined),
})

const state = computed(() => _open.value ? 'expanded' : 'collapsed')

function toggleSidebar() {
  return isMobile.value ? openMobile.value = !openMobile.value : _open.value = !_open.value
}

useEventListener('keydown', (event) => {
  if (event.key === SIDEBAR_KEYBOARD_SHORTCUT && (event.metaKey || event.ctrlKey)) {
    event.preventDefault()
    toggleSidebar()
  }
})

provideSidebar({
  state,
  open: _open,
  setOpen: (value) => _open.value = value,
  isMobile,
  openMobile,
  setOpenMobile: (value) => openMobile.value = value,
  toggleSidebar,
})
</script>

<template>
  <div
    :style="{
      '--sidebar-width': '18rem',
      '--sidebar-width-icon': SIDEBAR_WIDTH_ICON,
    }"
    :class="cn('group/sidebar-wrapper flex min-h-svh w-full has-[[data-variant=inset]]:bg-sidebar', props.class)"
  >
    <slot />
  </div>
</template>
