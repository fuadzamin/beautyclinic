<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Bell, Loader2 } from "lucide-vue-next";
import client from "@/api/client";
import { Button } from "@/components/ui/button";

const notifications = ref([]);
const unreadCount = ref(0);
const showNotifications = ref(false);
const notifLoading = ref(false);

const fetchNotifications = async () => {
    notifLoading.value = true;
    try {
        const res = await client.get('/admin/notifications');
        notifications.value = res.data?.notifications?.data || [];
        unreadCount.value = res.data?.unread_count || 0;
    } catch (_) {}
    finally { notifLoading.value = false; }
};

const markAsRead = async (id) => {
    try {
        await client.put(`/admin/notifications/${id}/read`);
        const notif = notifications.value.find(n => n.id === id);
        if (notif) notif.is_read = true;
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (_) {}
};

let notifInterval = null;
onMounted(() => {
    fetchNotifications();
    notifInterval = setInterval(fetchNotifications, 30000);
});
onUnmounted(() => clearInterval(notifInterval));
</script>

<template>
    <div class="relative hidden sm:block">
        <Button variant="ghost" size="icon" class="text-slate-400 hover:text-slate-900 relative" @click="showNotifications = !showNotifications">
            <Bell class="w-5 h-5" />
            <span v-if="unreadCount > 0" class="absolute top-2 right-2 w-4 h-4 bg-rose-500 rounded-full border-2 border-white flex items-center justify-center text-[8px] font-black text-white">{{ unreadCount > 9 ? '9+' : unreadCount }}</span>
        </Button>
        
        <!-- Notification Dropdown -->
        <div v-if="showNotifications" @click.outside="showNotifications = false" class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50 animate-in slide-in-from-top-2 duration-200">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-900">Notifikasi</h3>
                <span class="text-[10px] font-bold text-slate-400">{{ unreadCount }} belum dibaca</span>
            </div>
            <div class="max-h-72 overflow-y-auto">
                <div v-if="notifLoading" class="flex items-center justify-center py-8">
                    <Loader2 class="w-5 h-5 animate-spin text-gold-500" />
                </div>
                <div v-else-if="notifications.length === 0" class="py-8 text-center text-xs font-medium text-slate-400">
                    Belum ada notifikasi
                </div>
                <div v-else v-for="n in notifications" :key="n.id"
                    @click="markAsRead(n.id)"
                    class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition-colors"
                    :class="n.is_read ? 'opacity-60' : ''"
                >
                    <p class="text-xs font-bold text-slate-900">{{ n.title }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ n.message }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
