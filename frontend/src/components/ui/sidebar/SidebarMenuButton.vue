<script setup>
import { cn } from "@/lib/utils"
import { computed } from "vue"

const props = defineProps({
  as: { type: [String, Object], default: "button" },
  asChild: { type: Boolean, default: false },
  variant: { type: String, default: "default" },
  size: { type: String, default: "default" },
  isActive: { type: Boolean, default: false },
  class: { type: String, default: "" },
  tooltip: { type: [String, Object], default: undefined }
})

const sidebarMenuButtonVariants = {
  default: "hover:bg-sidebar-accent hover:text-sidebar-accent-foreground",
  outline: "bg-background shadow-[0_0_0_1px_hsl(var(--sidebar-border))] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground",
}

const sidebarMenuButtonSizes = {
  default: "h-8 text-sm",
  sm: "h-7 text-xs",
  lg: "h-12 text-sm group-data-[collapsible=icon]/sidebar-wrapper:!p-0",
}

const baseClasses = "peer/menu-button flex w-full items-center gap-2 overflow-hidden rounded-md p-2 text-left outline-none ring-sidebar-ring transition-[width,height,padding] hover:bg-sidebar-accent hover:text-sidebar-accent-foreground focus-visible:ring-2 active:bg-sidebar-accent active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 group-has-[[data-sidebar=menu-action]]/sidebar-menu-item:pr-8 data-[active=true]:bg-sidebar-primary data-[active=true]:text-sidebar-primary-foreground data-[active=true]:font-medium data-[state=open]:hover:bg-sidebar-accent data-[state=open]:hover:text-sidebar-accent-foreground group-data-[collapsible=icon]/sidebar-wrapper:h-8 group-data-[collapsible=icon]/sidebar-wrapper:!p-2"

const classes = computed(() => cn(
  baseClasses,
  sidebarMenuButtonVariants[props.variant],
  sidebarMenuButtonSizes[props.size],
  props.class
))
</script>

<template>
  <component
    :is="as"
    :data-sidebar="'menu-button'"
    :data-size="size"
    :data-active="isActive"
    :class="classes"
  >
    <slot />
  </component>
</template>
