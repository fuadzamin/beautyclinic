<script setup>
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { User, Settings, LogOut } from "lucide-vue-next";
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

const auth = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await auth.logout();
    router.push("/login");
};
</script>

<template>
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
</template>
