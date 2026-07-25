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
24: const auth = useAuthStore()
const router = useRouter()