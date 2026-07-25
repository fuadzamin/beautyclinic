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
const router = useRouter();

const routeName = computed(() => {
if (route.name === 
return route.name
? route.name.replace(
: 
});

const handleLogout = async () => {
await auth.logout();
router.push(
};
</script>

<template>
<SidebarProvider>
<div
class=
>
<AppSidebar />

<SidebarInset
class=
},
{
label: 
to: 
icon: markRaw(BarChart3),
show: auth.hasRole([
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
<Sidebar collapsible=
<SidebarHeader class=
<div class=
<div class=
<Sparkles class=
</div>
<span v-if=
</div>
</SidebarHeader>

<SidebarContent class=
<SidebarGroup v-for=

<div
class=
></div>

<!-- Branch Info -->
<div
class=
>
<MapPin class=
<span>{{
auth.user?.branch?.name || 
}}</span>
</div>

to: 
icon: markRaw(ShoppingCart),
show: true,
},
],
},
{
title: 
show: auth.isManagerOrOwner,
items: [
{
label: 
to: 
icon: markRaw(Users),
show: true,
},
{
label: 
to: 
icon: markRaw(MapPin),
show: auth.isOwner,
},
{
label: 
to: 
icon: markRaw(Settings),
show: true,
},
],
},
]);

const handleLogout = async () => {
await auth.logout();
router.push(
};
</script>

<template>
<Sidebar
collapsible=
class=
>
<SidebarHeader
class=
>
<div cl
// MISSING LINE 148
// MISSING LINE 149
// MISSING LINE 150
// MISSING LINE 151
// MISSING LINE 152
// MISSING LINE 153
// MISSING LINE 154
// MISSING LINE 155
// MISSING LINE 156
// MISSING LINE 157
// MISSING LINE 158
// MISSING LINE 159
// MISSING LINE 160
// MISSING LINE 161
// MISSING LINE 162
// MISSING LINE 163
// MISSING LINE 164
// MISSING LINE 165
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
// MISSING LINE 199
// MISSING LINE 200
</SidebarMenu>
</SidebarGroup>
</SidebarContent>

<SidebarFooter class=
<div
v-if=
class=
>
<div
class=
>
<User class=
</div>
<div class=
<p class=
{{ auth.user?.name }}
</p>
<p class=
{{ auth.user?.role?.replace(
</p>
</div>
</div>
<Button
variant=
@click=
class=
:class=
>
<LogOut
class=
/>
<span v-if=
>Keluar</span
>
</Button>
</SidebarFooter>
</Sidebar>
</template>
