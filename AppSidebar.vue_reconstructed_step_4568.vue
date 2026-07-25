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