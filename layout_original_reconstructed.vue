<script setup>
import { computed } from "vue";
import { RouterView, useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { User, Settings, LogOut, MapPin, Search, Bell } from "lucide-vue-next";
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
import {
    SidebarProvider,
    SidebarInset,
    SidebarTrigger,
} from "@/components/ui/sidebar";
import AppSidebar from "@/components/AppSidebar.vue";

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

const routeName = computed(() => {
    if (route.name === "admin-dashboard") return "Dashboard Overview";
    return route.name
        ? route.name.replace("admin-", "").replace("-", " ")
        : "Dashboard";
});

const handleLogout = async () => {
    await auth.logout();
    router.push("/login");
};
</script>

<template>
    <SidebarProvider>
        <div
            class="flex h-screen w-full bg-slate-100 overflow-hidden p-4 md:p-6 gap-4 md:gap-6"
        >
            <AppSidebar />

            <SidebarInset
                class="flex flex-col min-w-0 overflow-hidden r
            >
                <!-- Topbar -->
                <header
                    class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shrink-0 sticky top-0 z-30"
                >
                    <div class="flex items-center gap-4">
                        <SidebarTrigger class="-ml-2" />
                        <div
                            class="h-4 w-[1px] bg-slate-200 mx-1 hidden md:block"
                        ></div>
                        <h2
// MISSING LINE 61
// MISSING LINE 62
// MISSING LINE 63
                        </h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Search & Notifications (Placeholders for premium feel) -->
                        <Button
                            variant="ghost"
                            size="icon"
                            class="text-slate-400 hover:text-slate-900 hidden sm:flex"
                        >
                            <Search class="w-5 h-5" />
                        </Button>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="text-slate-400 hover:text-slate-900 relative hidden sm:flex"
                        >
                            <Bell class="w-5 h-5" />
                            <span
                                class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"
                            ></span>
                        </Button>

                        <div
                            class="h-6 w-[1px] bg-slate-200 mx-2 hidden sm:block"
                        ></div>

                        <!-- Branch Info -->
                        <div
                            class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-medium text-slate-600"
                        >
                            <MapPin class="w-3.5 h-3.5 text-gold-500" />
                            <span>{{
                                auth.user?.branch?.name || "Pusat"
                            }}</span>
                        </div>

                        <!-- User Menu -->
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    variant="ghost"
                                    class="relative h-10 w-10 rounded-full border-2 border-gold-100 p-0 overflow-hidden hover:border-gold-300 transition-all"
                                >
                                    <div
                                        class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-500"
                                    >
                                        <User class="w-5 h-5" />
                                    </div>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-64" align="end">
                                <DropdownMenuLabel class="font-normal">
                                    <div class="flex flex-col space-y-1">
                                        <p
                                            class="text-sm font-bold leading-none text-slate-900"
                                        >
                                            {{ auth.user?.name }}
                                        </p>
                                        <p
                                            class="
// MISSING LINE 125
// MISSING LINE 126
// MISSING LINE 127
// MISSING LINE 128
// MISSING LINE 129
// MISSING LINE 130
// MISSING LINE 131
// MISSING LINE 132
// MISSING LINE 133
// MISSING LINE 134
// MISSING LINE 135
// MISSING LINE 136
// MISSING LINE 137
// MISSING LINE 138
// MISSING LINE 139
// MISSING LINE 140
// MISSING LINE 141
// MISSING LINE 142
// MISSING LINE 143
// MISSING LINE 144
                                <DropdownMenuItem
                                    @click="handleLogout"
                                    class="text-rose-600 focus:text-rose-600"
                                >
                                    <LogOut class="mr-2 h-4 w-4" />
                                    <span>Keluar</span>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </header>

                <!-- Page Content -->
                <div
                    class="flex-1 overflow-auto bg-slate-50/50 p-6 md:p-10 lg:p-12"
                >
                    <div class="max-w-7xl mx-auto">
                        <RouterView v-slot="{ Component }">
                            <transition name="page" mode="out-in">
                                <component :is="Component" />
                            </transition>
                        </RouterView>
                    </div>
                </div>
            </SidebarInset>
        </div>
    </SidebarProvider>
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
