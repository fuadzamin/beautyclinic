<script setup>
import { ref } from "vue";
import { RouterView } from "vue-router";
import { Toaster } from "@/components/ui/sonner";
import AppSidebar from "@/components/AppSidebar.vue";
import AdminHeader from "@/components/layout/AdminHeader.vue";

// Sidebar state
const isCollapsed = ref(false);
const isMobileOpen = ref(false);

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};

const toggleMobileSidebar = () => {
    isMobileOpen.value = !isMobileOpen.value;
};

const closeMobileSidebar = () => {
    isMobileOpen.value = false;
};
</script>

<template>
    <div class="flex h-screen w-screen bg-slate-100 overflow-hidden relative">
        <!-- Backdrop for mobile sidebar -->
        <div 
            v-if="isMobileOpen" 
            @click="closeMobileSidebar" 
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden transition-opacity duration-300"
        ></div>

        <!-- Sidebar (Desktop and Mobile) -->
        <AppSidebar 
            :is-collapsed="isCollapsed" 
            :is-mobile-open="isMobileOpen" 
            @close-mobile="closeMobileSidebar" 
            @toggle="toggleSidebar"
        />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full min-h-0 min-w-0 overflow-hidden bg-white border-l border-slate-200">
            <!-- Topbar -->
            <AdminHeader 
                :is-collapsed="isCollapsed" 
                @toggle="toggleSidebar" 
                @toggle-mobile="toggleMobileSidebar" 
            />

            <!-- Page Content -->
            <div class="flex-1 overflow-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <RouterView v-slot="{ Component }">
                        <transition name="page" mode="out-in">
                            <component :is="Component" />
                        </transition>
                    </RouterView>
                </div>
            </div>
        </div>
    </div>
    <Toaster />
</template>

<style>
/* Page Transition */
.page-enter-active,
.page-leave-active {
    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}

.page-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Custom Scrollbar */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
