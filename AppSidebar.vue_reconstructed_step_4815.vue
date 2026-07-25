<script setup>
import { computed, markRaw } from "vue";
import { useRouter, useRoute } from "vue-router";
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
} from "lucide-vue-next";
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
useSidebar,
} from "@/components/ui/sidebar";
import { Button } from "@/components/ui/button";
35: const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const { state } = useSidebar();
40: const navGroups = computed(() => [
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
<truncated 4097 bytes>
</SidebarContent>
205:         <SidebarFooter class="p-4 border-t border-slate-800/50">
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
The above content does NOT show the entire file contents. If you need to view any lines of the file which were not shown to complete your task, call this tool again to view those lines.