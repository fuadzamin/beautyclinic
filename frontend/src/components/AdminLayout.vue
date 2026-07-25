<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { RouterView, RouterLink, useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { User, Settings, LogOut, MapPin, Search, Bell, Menu, X, Loader2 } from "lucide-vue-next";
import client from "@/api/client";
import { Button } from "@/components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Toaster } from "@/components/ui/sonner";
import AppSidebar from "@/components/AppSidebar.vue";

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

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

const breadcrumbs = computed(() => {
    const parts = route.path.split('/').filter(Boolean);
    return parts.map((part, i) => ({
        label: part === 'admin' ? 'Home' : part.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
        path: '/' + parts.slice(0, i + 1).join('/'),
        isLast: i === parts.length - 1,
    }));
});

const handleLogout = async () => {
    await auth.logout();
    router.push("/login");
};

// Notifications
const notifications = ref([]);
const unreadCount = ref(0);
const showNotifications = ref(false);
const notifLoading = ref(false);

const fetchNotifications = async () => {
    notifLoading.value = true;
    try {
        const res = await client.get('/admin/notifications');
        notifications.value = res.data?.notifications?.data || [];
        unreadCount.value = res.data?.unread_count || 0;
    } catch (_) {}
    finally { notifLoading.value = false; }
};

const markAsRead = async (id) => {
    try {
        await client.put(`/admin/notifications/${id}/read`);
        const notif = notifications.value.find(n => n.id === id);
        if (notif) notif.is_read = true;
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (_) {}
};

let notifInterval = null;
onMounted(() => {
    fetchNotifications();
    notifInterval = setInterval(fetchNotifications, 30000);
});
onUnmounted(() => clearInterval(notifInterval));
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
            <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-4 sm:px-6 shrink-0 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <!-- Toggle buttons -->
                    <button 
                        @click="toggleSidebar" 
                        class="hidden lg:flex p-2 hover:bg-slate-50 rounded-lg text-slate-500 hover:text-slate-900 transition-colors"
                        aria-label="Toggle Sidebar"
                    >
                        <Menu class="w-5 h-5" />
                    </button>
                    <button 
                        @click="toggleMobileSidebar" 
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
                    <div class="relative hidden sm:block">
                        <Button variant="ghost" size="icon" class="text-slate-400 hover:text-slate-900 relative" @click="showNotifications = !showNotifications">
                            <Bell class="w-5 h-5" />
                            <span v-if="unreadCount > 0" class="absolute top-2 right-2 w-4 h-4 bg-rose-500 rounded-full border-2 border-white flex items-center justify-center text-[8px] font-black text-white">{{ unreadCount > 9 ? '9+' : unreadCount }}</span>
                        </Button>
                        
                        <!-- Notification Dropdown -->
                        <div v-if="showNotifications" @click.outside="showNotifications = false" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50 animate-in slide-in-from-top-2 duration-200">
                            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="text-sm font-black text-slate-900">Notifikasi</h3>
                                <span class="text-[10px] font-bold text-slate-400">{{ unreadCount }} belum dibaca</span>
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                <div v-if="notifLoading" class="flex items-center justify-center py-8">
                                    <Loader2 class="w-5 h-5 animate-spin text-gold-500" />
                                </div>
                                <div v-else-if="notifications.length === 0" class="py-8 text-center text-xs font-medium text-slate-400">
                                    Belum ada notifikasi
                                </div>
                                <div v-else v-for="n in notifications" :key="n.id"
                                    @click="markAsRead(n.id)"
                                    class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition-colors"
                                    :class="n.is_read ? 'opacity-60' : ''"
                                >
                                    <p class="text-xs font-bold text-slate-900">{{ n.title }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ n.message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="h-6 w-[1px] bg-slate-200 mx-2 hidden sm:block"></div>

                    <!-- Branch Info -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-medium text-slate-600">
                        <MapPin class="w-3.5 h-3.5 text-gold-500" />
                        <span>{{ auth.user?.branch?.name || "Pusat" }}</span>
                    </div>

                    <!-- User Menu -->
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" class="relative h-10 w-10 rounded-full border-2 border-gold-100 p-0 overflow-hidden hover:border-gold-300 transition-all">
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-500">
                                    <User class="w-5 h-5" />
                                </div>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-64" align="end">
                            <DropdownMenuLabel class="font-normal">
                                <div class="flex flex-col space-y-1">
                                    <p class="text-sm font-bold leading-none text-slate-900">{{ auth.user?.name }}</p>
                                    <p class="text-xs leading-none text-slate-500 capitalize">{{ auth.user?.role?.replace("_", " ") }}</p>
                                </div>
                            </DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuGroup>
                                <DropdownMenuItem @click="router.push('/admin/settings')">
                                    <Settings class="mr-2 h-4 w-4" />
                                    <span>Pengaturan Akun</span>
                                </DropdownMenuItem>
                            </DropdownMenuGroup>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem @click="handleLogout" class="text-rose-600 focus:text-rose-600">
                                <LogOut class="mr-2 h-4 w-4" />
                                <span>Keluar</span>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </header>

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
