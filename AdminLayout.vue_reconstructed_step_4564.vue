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
24: const auth = useAuthStore();
const route = useRoute();
const router = useRouter();
28: const routeName = computed(() => {
if (route.name === "admin-dashboard") return "Dashboard Overview";
return route.name
? route.name.replace("admin-", "").replace("-", " ")
: "Dashboard";
});
35: const handleLogout = async () => {
await auth.logout();
router.push("/login");
};
</script>
41: <template>
<SidebarProvider>
<div
class="flex h-screen w-full bg-slate-100 overflow-hidden p-4 md:p-6 gap-4 md:gap-6"
>
<AppSidebar />
48:             <SidebarInset
class="flex flex-col min-w-0 overflow-hidden r
</h2>
</div>
67:                     <div class="flex items-center gap-3">
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
87:                         <div
class="h-6 w-[1px] bg-slate-200 mx-2 hidden sm:block"
></div>
91:                         <!-- Branch Info -->
<div
class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-full border border-slate-100 text-xs font-medium text-slate-600"
>
<MapPin class="w-3.5 h-3.5 text-gold-500" />
<span>{{
auth.user?.branch?.name || "Pusat"
}}</span>
</div>
The above content does NOT show the entire file contents. If you need to view any lines of the file which were not shown to complete your task, call this tool again to view those lines.