--- PART 4564 ---
Created At: 2026-05-20T07:33:47Z
Completed At: 2026-05-20T07:33:47Z
File Path: `file:///c:/Users/m%20s%20i/Documents/coba%20coba/web%20klinik/frontend/src/components/AdminLayout.vue`
Total Lines: 203
Total Bytes: 8021
Showing lines 1 to 100
The following code has been modified to include a line number before every line, in the format: <line_number>: <original_line>. Please note that any changes targeting the original code should remove the line number, colon, and leading space.
1: <script setup>
2: import { computed } from "vue";
3: import { RouterView, useRoute, useRouter } from "vue-router";
4: import { useAuthStore } from "@/stores/authStore";
5: import { User, Settings, LogOut, MapPin, Search, Bell } from "lucide-vue-next";
6: import { Button } from "@/components/ui/button";
7: import {
8:     DropdownMenu,
9:     DropdownMenuContent,
10:     DropdownMenuGroup,
11:     DropdownMenuItem,
12:     DropdownMenuLabel,
13:     DropdownMenuSeparator,
14:     DropdownMenuTrigger,
15: } from "@/components/ui/dropdown-menu";
16: import { Toaster } from "@/components/ui/sonner";
17: import {
18:     SidebarProvider,
19:     SidebarInset,
20:     SidebarTrigger,
21: } from "@/components/ui/sidebar";
22: import AppSidebar from "@/components/AppSidebar.vue";
23: 
24: const auth = useAuthStore();
25: const route = useRoute();
26: const router = useRouter();
27: 
28: const routeNa
153:                         </DropdownMenu>
154:                     </div>
155:                 </header>
156: 
157:                 <!-- Page Content -->
158:                 <div
159:                     class="flex-1 overflow-auto bg-slate-50/50 p-6 md:p-10 lg:p-12"
160:                 >
161:                     <div class="max-w-7xl mx-auto">
162:                         <RouterView v-slot="{ Component }">
163:                             <transition name="page" mode="out-in">
164:                                 <component :is="Component" />
165:                             </transition>
166:                         </RouterView>
167:                     </div>
168:                 </div>
169:             </SidebarInset>
170:         </div>
171:     </SidebarProvider>
172:     <Toaster />
173: </template>
174: 
175: <style>
176: /* Page Transition */
177: .page-enter-active,
178: .page-leave-active {
179:     transition:
180:         opacity 0.2s ease,
181:         transform 0.2s ease;
182: }
183: 
184: .page-enter-from {
185:     opacity: 0;
186:     transform: translateY(10px);
187: }
188: 
189: .page-leave-to {
190:     opacity: 0;
191:     transform: translateY(-10px);
192: }
193: 
194: /* Custom Scrollbar */
195: .scrollbar-hide::-webkit-scrollbar {
196:     display: none;
197: }
198: .scrollbar-hide {
199:     -ms-overflow-style: none;
200:     scrollbar-width: none;
201: }
202: </style>
203: 
The above content does NOT show the entire file contents. If you need to view any lines of the file which were not shown to complete your task, call this tool again to view those lines.
193: 