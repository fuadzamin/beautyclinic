<script setup>
import { computed, markRaw } from "vue";
import { useRouter, useRoute, RouterLink } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import {
    Sparkles,
    LayoutDashboard,
    Calendar,
    ShoppingCart,
    Users,
    Settings,
    User,
    MapPin,
    Receipt,
    BarChart3,
    Stethoscope,
    Boxes,
    History,
    LogOut,
    X,
} from "lucide-vue-next";
import { Button } from "@/components/ui/button";

const props = defineProps({
    isCollapsed: {
        type: Boolean,
        default: false,
    },
    isMobileOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close-mobile", "toggle"]);

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const navGroups = computed(() => [
    {
        title: "General",
        show: true,
        items: [
            {
                label: "Dashboard",
                to: "/admin/dashboard",
                icon: markRaw(LayoutDashboard),
                show: true,
            },
            {
                label: "Laporan",
                to: "/admin/reports",
                icon: markRaw(BarChart3),
                show: auth.hasRole(["owner", "branch_manager"]),
            },
        ],
    },
    {
        title: "Clinic Ops",
        show: auth.hasRole(["owner", "branch_manager", "admin_klinik"]),
        items: [
            {
                label: "Appointments",
                to: "/admin/appointments",
                icon: markRaw(Calendar),
                show: true,
            },
            {
                label: "Treatments",
                to: "/admin/treatments",
                icon: markRaw(Stethoscope),
                show: true,
            },
            {
                label: "Kasir / POS",
                to: "/admin/pos",
                icon: markRaw(Receipt),
                show: true,
            },
            {
                label: "Riwayat Transaksi",
                to: "/admin/transactions",
                icon: markRaw(History),
                show: true,
            },
        ],
    },
    {
        title: "Inventory",
        show: auth.hasRole(["owner", "branch_manager", "admin_produk"]),
        items: [
            {
                label: "Products",
                to: "/admin/products",
                icon: markRaw(Boxes),
                show: true,
            },
            {
                label: "Orders",
                to: "/admin/orders",
                icon: markRaw(ShoppingCart),
                show: true,
            },
        ],
    },
    {
        title: "System",
        show: auth.isManagerOrOwner,
        items: [
            {
                label: "Staff Management",
                to: "/admin/staff",
                icon: markRaw(Users),
                show: true,
            },
            {
                label: "Branches",
                to: "/admin/branches",
                icon: markRaw(MapPin),
                show: auth.isOwner,
            },
            {
                label: "Settings",
                to: "/admin/settings",
                icon: markRaw(Settings),
                show: true,
            },
        ],
    },
]);

const handleLogout = async () => {
    await auth.logout();
    router.push("/login");
};
</script>

<template>
    <!-- Actual Sidebar container -->
    <aside
        class="bg-slate-950 text-slate-400 flex flex-col overflow-hidden transition-all duration-300 z-50 shrink-0
               fixed lg:relative top-0 bottom-0 left-0 h-full lg:h-full rounded-r-xl lg:rounded-none shadow-2xl lg:shadow-none border-r border-slate-800/50 lg:border-none"
        :class="[
            isCollapsed ? 'lg:w-20' : 'lg:w-64',
            isMobileOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0'
        ]"
    >
        <!-- Header -->
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800/50 shrink-0">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-gold-500 flex items-center justify-center shrink-0">
                    <Sparkles class="w-5 h-5 text-white" />
                </div>
                <span v-if="!isCollapsed || isMobileOpen" class="text-lg font-bold text-white tracking-tight truncate">AURA CLINIC</span>
            </div>
            
            <!-- Close button for mobile sidebar -->
            <button 
                @click="emit('close-mobile')" 
                class="lg:hidden p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors"
                aria-label="Close Mobile Menu"
            >
                <X class="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto py-4 scrollbar-hide space-y-4">
            <div
                v-for="group in navGroups"
                :key="group.title"
                v-show="group.show"
                class="px-3"
            >
                <p
                    v-if="!isCollapsed || isMobileOpen"
                    class="px-3 mb-2 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] transition-opacity duration-300"
                >
                    {{ group.title }}
                </p>
                <nav class="space-y-1">
                    <div
                        v-for="item in group.items"
                        :key="item.to"
                        v-show="item.show"
                    >
                        <RouterLink
                            :to="item.to"
                            @click="emit('close-mobile')"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 group font-medium text-sm"
                            :class="route.path.startsWith(item.to) 
                                ? 'bg-gold-500 text-white font-semibold shadow-lg shadow-gold-500/20' 
                                : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200'"
                            :title="item.label"
                        >
                            <component
                                :is="item.icon"
                                class="w-5 h-5 shrink-0 transition-transform duration-300 group-hover:scale-110"
                                :class="route.path.startsWith(item.to) ? 'text-white' : 'text-slate-400 group-hover:text-slate-200'"
                            />
                            <span
                                v-if="!isCollapsed || isMobileOpen"
                                class="transition-opacity duration-300 truncate"
                            >{{ item.label }}</span>
                        </RouterLink>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-800/50 shrink-0">
            <div
                v-if="!isCollapsed || isMobileOpen"
                class="flex items-center gap-3 px-2 py-3 mb-2 rounded-lg bg-slate-900/50 border border-slate-800"
            >
                <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center shrink-0">
                    <User class="w-4 h-4 text-slate-400" />
                </div>
                <div class="flex flex-col min-w-0">
                    <p class="text-xs font-bold text-white truncate">
                        {{ auth.user?.name }}
                    </p>
                    <p class="text-[10px] text-slate-500 truncate capitalize">
                        {{ auth.user?.role?.replace("_", " ") }}
                    </p>
                </div>
            </div>
            <Button
                variant="ghost"
                @click="handleLogout"
                class="w-full justify-start gap-3 text-red-500 hover:text-white hover:bg-red-600/90 h-11 px-3 rounded-xl transition-all duration-300 group"
                :class="isCollapsed && !isMobileOpen ? 'justify-center' : ''"
            >
                <LogOut
                    class="w-5 h-5 shrink-0 text-red-500 group-hover:text-white transition-colors"
                />
                <span v-if="!isCollapsed || isMobileOpen" class="font-semibold text-sm">Keluar</span>
            </Button>
        </div>
    </aside>
</template>
