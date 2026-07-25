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
157:                 <!-- Page Content -->
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
175: <style>
/* Page Transition */
.page-enter-active,
.page-leave-active {
transition:
opacity 0.2s ease,
transform 0.2s ease;
}
184: .page-enter-from {
opacity: 0;
transform: translateY(10px);
}
189: .page-leave-to {
opacity: 0;
transform: translateY(-10px);
}
194: /* Custom Scrollbar */
.scrollbar-hide::-webkit-scrollbar {
display: none;
}
.scrollbar-hide {
-ms-overflow-style: none;
scrollbar-width: none;
}
</style>
The above content shows the entire, complete file contents of the requested file.