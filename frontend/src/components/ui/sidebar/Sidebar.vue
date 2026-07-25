<script setup>
import { cn } from "@/lib/utils"
import { useSidebar } from "./utils"
import { Sheet, SheetContent } from "@/components/ui/sheet"

const props = defineProps({
  side: { type: String, default: "left" },
  variant: { type: String, default: "sidebar" },
  collapsible: { type: String, default: "offcanvas" },
  class: { type: String, default: "" }
})

const { state, isMobile, openMobile } = useSidebar()
</script>

<template>
  <template v-if="isMobile">
    <Sheet v-model:open="openMobile" v-bind="$attrs">
      <SheetContent
        data-sidebar="sidebar"
        data-mobile="true"
        :side="side"
        class="w-[--sidebar-width] bg-slate-950 p-0 text-slate-400 [&>button]:text-white [&>button]:hover:text-slate-200 border-none"
        :style="{
          '--sidebar-width': '18rem',
        }"
      >
        <div class="flex h-full w-full flex-col">
          <slot />
        </div>
      </SheetContent>
    </Sheet>
  </template>

  <template v-else>
    <div
      v-if="collapsible !== 'none'"
      class="hidden lg:block h-full w-[--sidebar-width] bg-transparent transition-[width] duration-200 ease-linear"
      :class="state === 'collapsed' ? 'w-[--sidebar-width-icon]' : ''"
    />
    <div
      :class="cn(
        'duration-200 fixed inset-y-0 z-10 hidden w-[--sidebar-width] transition-[left,right,width] ease-linear lg:flex',
        side === 'left'
          ? 'left-0 group-data-[collapsible=offcanvas]/sidebar-wrapper:left-[calc(var(--sidebar-width)*-1)]'
          : 'right-0 group-data-[collapsible=offcanvas]/sidebar-wrapper:right-[calc(var(--sidebar-width)*-1)]',
        variant === 'floating' || variant === 'inset'
          ? 'p-2 group-data-[collapsible=icon]/sidebar-wrapper:w-[calc(var(--sidebar-width-icon)+calc(var(--spacing)*4))]'
          : 'group-data-[collapsible=icon]/sidebar-wrapper:w-[--sidebar-width-icon] border-r',
        props.class,
      )"
      :data-state="state"
      :data-collapsible="state === 'collapsed' ? collapsible : ''"
      :data-variant="variant"
      :data-side="side"
    >
      <div
        class="flex h-full w-full flex-col bg-sidebar group-data-[variant=floating]/sidebar-wrapper:rounded-xl group-data-[variant=floating]/sidebar-wrapper:border group-data-[variant=floating]/sidebar-wrapper:shadow"
      >
        <slot />
      </div>
    </div>
  </template>
</template>
