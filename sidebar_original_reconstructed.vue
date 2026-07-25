<script setup>
import { computed, markRaw } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import {
  Sparkles, LayoutDashboard, Calendar, ShoppingCart,
  Users, Settings, User, MapPin, Receipt, BarChart3,
  Stethoscope, Boxes, History, LogOut
} from 'lucide-vue-next'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar
} from '@/components/ui/sidebar'
import { Button } from '@/components/ui/button'

const auth = useAuthStore()
const router = useRouter()
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from "@/components/ui/sidebar";
import { Button } from "@/components/ui/button";

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const { state } = useSidebar();

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
      { label: 'Staff Management', to: '/admin/staff', icon: markRaw(Users), show: true },
      { label: 'Branches', to: '/admin/branches', icon: markRaw(MapPin), show: auth.isOwner },
      { label: 'Settings', to: '/admin/settings', icon: markRaw(Settings), show: true },
    ]
  }
])

const handleLogout = async () => {
  await auth.logout()
  router.push('/login')
}
</script>

<template>
  <Sidebar collapsible="icon" class="bg-slate-950 text-slate-400 border-r border-slate-800 gap-10">
    <SidebarHeader class="h-16 flex items-center justify-between px-4 border-b border-slate-800/50">
      <div class="flex items-center gap-3 overflow-hidden">
        <div class="w-8 h-8 rounded-lg bg-gold-500 flex items-center justify-center shrink-0">
          <Sparkles class="w-5 h-5 text-white" />
        </div>
        <span v-if="state === 'expanded'" class="text-lg font-bold text-white tracking-tight">AURA CLINIC</span>
      </div>
    </SidebarHeader>

    <SidebarContent class="py-4 scrollbar-hide">
      <SidebarGroup v-for="group in navGroups" :key="group.title" v-show="group.show">
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
    <Sidebar
        collapsible="icon"
        class="bg-slate-950 text-slate-400 border-none md:fixed md:top-6 md:bottom-6 md:left-6 md:h-[calc(100vh-3rem)] md:rounded-2xl md:shadow-xl md:border md:border-slate-800/50 overflow-hidden md:inset-y-auto"
    >
        <SidebarHeader
            class="h-16 flex items-center justify-between px-4 border-b border-slate-800/50"
        >
            <div cl
                <div
                    class="w-8 h-8 rounded-lg bg-gold-500 flex items-center justify-center shrink-0"
                >
                    <Sparkles class="w-5 h-5 text-white" />
                </div>
                <span
                    v-if="state === 'expanded'"
                    class="text-lg font-bold text-white tracking-tight"
                    >AURA CLINIC</span
                >
            </div>
        </SidebarHeader>

        <SidebarContent class="py-4 scrollbar-hide">
            <SidebarGroup
                v-for="group in navGroups"
                :key="group.title"
                v-show="group.show"
// MISSING LINE 166
// MISSING LINE 167
// MISSING LINE 168
// MISSING LINE 169
// MISSING LINE 170
// MISSING LINE 171
// MISSING LINE 172
// MISSING LINE 173
// MISSING LINE 174
// MISSING LINE 175
// MISSING LINE 176
// MISSING LINE 177
// MISSING LINE 178
// MISSING LINE 179
// MISSING LINE 180
// MISSING LINE 181
// MISSING LINE 182
// MISSING LINE 183
// MISSING LINE 184
// MISSING LINE 185
// MISSING LINE 186
// MISSING LINE 187
// MISSING LINE 188
// MISSING LINE 189
// MISSING LINE 190
// MISSING LINE 191
// MISSING LINE 192
// MISSING LINE 193
// MISSING LINE 194
// MISSING LINE 195
// MISSING LINE 196
// MISSING LINE 197
// MISSING LINE 198
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter class="p-4 border-t border-slate-800/50">
            <div
                v-if="state === 'expanded'"
                class="flex items-center gap-3 px-2 py-3 mb-2 rounded-lg bg-slate-900/50 border border-slate-800"
            >
                <div
                    class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center"
                >
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
                :class="state === 'collapsed' ? 'justify-center' : ''"
            >
                <LogOut
                    class="w-5 h-5 shrink-0 text-red-500 group-hover:text-white transition-colors"
                />
                <span v-if="state === 'expanded'" class="font-semibold text-sm"
                    >Keluar</span
                >
            </Button>
        </SidebarFooter>
    </Sidebar>
</template>
