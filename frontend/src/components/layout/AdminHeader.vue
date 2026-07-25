<script setup>
import { computed } from "vue";
import { RouterLink, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { MapPin, Search, Menu } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import NotificationDropdown from "./NotificationDropdown.vue";
import UserMenuDropdown from "./UserMenuDropdown.vue";

const props = defineProps({
    isCollapsed: Boolean
});

const emit = defineEmits(['toggle', 'toggle-mobile']);

const auth = useAuthStore();
const route = useRoute();

const breadcrumbs = computed(() => {
    const parts = route.path.split('/').filter(Boolean);
    return parts.map((part, i) => ({
        label: part === 'admin' ? 'Home' : part.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
        path: '/' + parts.slice(0, i + 1).join('/'),
        isLast: i === parts.length - 1,
    }));
});
</script>

<template>
    <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-4 sm:px-6 shrink-0 sticky top-0 z-30">
        <div class="flex items-center gap-4">
            <!-- Toggle buttons -->
            <button 
                @click="emit('toggle')" 
                class="hidden lg:flex p-2 hover:bg-slate-50 rounded-lg text-slate-500 hover:text-slate-900 transition-colors"
                aria-label="Toggle Sidebar"
            >
                <Menu class="w-5 h-5" />
            </button>
            <button 
                @click="emit('toggle-mobile')" 
                class="flex lg:hidden p-2 hover:bg-slate-50 rounded-lg text-slate-500 hover:text-slate-900 transition-colors"
                aria-label="Toggle Mobile Menu"
            >
                <Menu class="w-5 h-5" />
            </button>
            
            <div class="h-4 w-[1px] bg-slate-200 mx-1 hidden sm:block"></div>
            <nav class="hidden sm:flex items-center gap-1.5 text-xs font-medium text-slate-400">
                <template v-for="(crumb, i) in breadcrumbs" :key="i">
                    <RouterLink v-if="!crumb.isLast" :to="crumb.path" class="hover:text-gold-600 transition-colors capitalize">{{ crumb.label }}</RouterLink>
                    <span v-if="!crumb.isLast" class="text-slate-300">/</span>
                    <span v-else class="text-slate-700 font-bold capitalize">{{ crumb.label }}</span>
                </template>
            </nav>
        </div>

        <div class="flex items-center gap-3">
            <!-- Search & Notifications -->
            <Button variant="ghost" size="icon" class="text-slate-400 hover:text-slate-900 hidden sm:flex">
                <Search class="w-5 h-5" />
            </Button>
            
            <NotificationDropdown />

            <div class="h-6 w-[1px] bg-slate-200 mx-2 hidden sm:block"></div>

            <!-- Branch Info -->
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-medium text-slate-600">
                <MapPin class="w-3.5 h-3.5 text-gold-500" />
                <span>{{ auth.user?.branch?.name || "Pusat" }}</span>
            </div>

            <!-- User Menu -->
            <UserMenuDropdown />
        </div>
    </header>
</template>
