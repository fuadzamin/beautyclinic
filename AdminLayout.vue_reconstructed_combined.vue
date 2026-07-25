<script setup>
import { computed } from 
import { RouterView, useRoute, useRouter } from 
import { useAuthStore } from 
import { User, Settings, LogOut, MapPin, Search, Bell } from 
import { Button } from 
import {
DropdownMenu,
DropdownMenuContent,
DropdownMenuGroup,
DropdownMenuItem,
DropdownMenuLabel,
DropdownMenuSeparator,
DropdownMenuTrigger,
} from 
import { Toaster } from 
import {
SidebarProvider,
SidebarInset,
SidebarTrigger,
} from 
import AppSidebar from 

const auth = useAuthStore();
const route = useRoute();
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
>
<!-- Topbar -->
<header
class=
>
<div class=
<SidebarTrigger class=
<div
class=
></div>
<h2
// MISSING LINE 61
// MISSING LINE 62
// MISSING LINE 63
</h2>
</div>

<div class=
<!-- Search & Notifications (Placeholders for premium feel) -->
<Button
variant=
size=
class=
>
<Search class=
</Button>
<Button
variant=
size=
class=
>
<Bell class=
<span
class=
></span>
</Button>

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

<!-- User Menu -->
<DropdownMenu>
<DropdownMenuTrigger as-child>
<Button
variant=
class=
>
<div
class=
>
<User class=
</div>
</Button>
</DropdownMenuTrigger>
<DropdownMenuContent class=
<DropdownMenuLabel class=
<div class=
<p
class=
>
{{ auth.user?.name }}
</p>
<p
class=
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
// MISSING LINE 145
// MISSING LINE 146
class=
>
<LogOut class=
<span>Keluar</span>
</DropdownMenuItem>
</DropdownMenuContent>
</DropdownMenu>
</div>
</header>

<!-- Page Content -->
<div
class=
>
<div class=
<RouterView v-slot=
<transition name=
<component :is=
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
