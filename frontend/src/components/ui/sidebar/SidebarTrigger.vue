<script setup>
import { PanelLeft } from "lucide-vue-next"
import { Button } from "@/components/ui/button"
import { useSidebar } from "./utils"

const { toggleSidebar } = useSidebar()
</script>

<template>
  <Button
    variant="ghost"
    size="icon"
    class="h-7 w-7"
    @click="toggleSidebar"
  >
    <PanelLeft />
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>
