<template>
  <div class="space-y-6 animate-in fade-in duration-500">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="space-y-1">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
          <div class="p-2 rounded-2xl bg-gold-500 text-white shadow-lg shadow-gold-500/20">
            <Package class="w-6 h-6" />
          </div>
          Kelola Produk
        </h1>
        <p class="text-slate-500 text-sm font-medium">Tambah, perbarui, atau hapus produk skincare dari katalog Anda.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64 group">
          <Search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-gold-500 transition-colors" />
          <Input 
            v-model="searchQuery" 
            @keyup.enter="fetchProducts(1)" 
            placeholder="Cari produk..." 
            class="pl-10 h-11 rounded-2xl bg-white border-slate-200 focus:ring-gold-500/10 transition-all" 
          />
        </div>
        
        <Select v-model="selectedBranch" @update:modelValue="fetchProducts(1)">
          <SelectTrigger class="w-full sm:w-48 h-11 rounded-2xl bg-white border-slate-200">
            <SelectValue placeholder="Semua Cabang" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Semua Cabang</SelectItem>
            <SelectItem v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</SelectItem>
          </SelectContent>
        </Select>

        <Button @click="openModal()" class="w-full sm:w-auto h-11 px-6 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-xl shadow-slate-900/10 gap-2">
          <Plus class="w-4 h-4" /> Tambah Produk
        </Button>
      </div>
    </div>

    <!-- Main Content -->
    <Card class="border-none shadow-sm overflow-hidden bg-white/50 backdrop-blur-sm">
      <div class="overflow-x-auto w-full">
        <Table>
          <TableHeader class="bg-slate-50/50">
            <TableRow>
              <TableHead class="font-bold text-slate-900 py-5 pl-6">Produk</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Harga</TableHead>
            <TableHead class="font-bold text-slate-900 py-5">Stok per Cabang</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-center">Status</TableHead>
            <TableHead class="font-bold text-slate-900 py-5 text-right pr-6">Aksi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="isLoading">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <Loader2 class="w-8 h-8 animate-spin text-gold-500" />
                <p class="text-sm font-medium text-slate-500">Memuat katalog produk...</p>
              </div>
            </TableCell>
          </TableRow>
          
          <TableRow v-else-if="products.length === 0">
            <TableCell colspan="5" class="h-64 text-center">
              <div class="flex flex-col items-center justify-center gap-4 opacity-40">
                <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center">
                  <PackageSearch class="w-10 h-10" />
                </div>
                <div class="space-y-1">
                  <p class="font-bold text-slate-900">Produk tidak ditemukan</p>
                  <p class="text-xs">Coba ubah kata kunci atau filter cabang</p>
                </div>
              </div>
            </TableCell>
          </TableRow>

          <TableRow v-else v-for="product in products" :key="product.id" class="group hover:bg-slate-50/50 transition-colors">
            <TableCell class="py-4 pl-6">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white overflow-hidden shrink-0 border border-slate-100 shadow-sm group-hover:scale-105 transition-transform duration-300">
                  <img :src="product.image_url || '/placeholder-product.jpg'" class="w-full h-full object-cover" />
                </div>
                <div class="space-y-0.5">
                  <p class="font-black text-slate-900 group-hover:text-gold-600 transition-colors">{{ product.name }}</p>
                  <Badge variant="outline" class="text-[10px] uppercase font-bold text-slate-400 border-slate-200">
                    {{ product.category }}
                  </Badge>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <p class="text-lg font-black text-slate-900">Rp {{ formatPrice(product.price) }}</p>
            </TableCell>
            <TableCell>
              <div class="flex flex-wrap gap-2">
                <Badge 
                  v-for="b in product.branches" :key="b.id"
                  variant="secondary"
                  :class="[
                    'rounded-xl px-2.5 py-1 text-[11px] font-bold border-none transition-all',
                    b.pivot.stock_quantity > 10 
                      ? 'bg-emerald-50 text-emerald-600' 
                      : 'bg-rose-50 text-rose-600'
                  ]"
                >
                  <Building2 class="w-3 h-3 mr-1.5 opacity-50" />
                  {{ b.name }}: {{ b.pivot.stock_quantity }}
                </Badge>
                <p v-if="!product.branches?.length" class="text-xs text-slate-400 italic">Belum ada stok</p>
              </div>
            </TableCell>
            <TableCell class="text-center">
              <Badge 
                class="rounded-full px-3 py-1 text-[10px] font-black uppercase"
                :class="product.is_active ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-500'"
              >
                {{ product.is_active ? 'Aktif' : 'Non-aktif' }}
              </Badge>
            </TableCell>
            <TableCell class="text-right pr-6">
              <div class="flex items-center justify-end gap-1">
                <Button variant="ghost" size="icon" @click="openStockModal(product)" class="h-9 w-9 rounded-xl hover:bg-emerald-50 hover:text-emerald-600 text-slate-400 transition-all" title="Update Stok">
                  <Boxes class="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="icon" @click="openModal(product)" class="h-9 w-9 rounded-xl hover:bg-blue-50 hover:text-blue-600 text-slate-400 transition-all" title="Edit Produk">
                  <Edit3 class="w-4 h-4" />
                </Button>
                <Button variant="ghost" size="icon" @click="showDeleteConfirm = true; productToDelete = product.id" class="h-9 w-9 rounded-xl hover:bg-rose-50 hover:text-rose-600 text-slate-400 transition-all" title="Hapus/Non-aktif">
                  <Trash2 class="w-4 h-4" />
                </Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
        </Table>
      </div>

      <!-- Pagination -->
      <CardFooter v-if="pagination.last_page > 1" class="px-6 py-4 border-t bg-slate-50/30 flex items-center justify-between">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
          Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
        </p>
        <div class="flex gap-2">
          <Button
            variant="outline"
            size="sm"
            class="rounded-xl h-9 px-4 border-slate-200 bg-white"
            :disabled="pagination.current_page <= 1"
            @click="changePage(pagination.current_page - 1)"
          >
            <ChevronLeft class="w-4 h-4 mr-1" /> Prev
          </Button>
          <Button
            variant="outline"
            size="sm"
            class="rounded-xl h-9 px-4 border-slate-200 bg-white"
            :disabled="pagination.current_page >= pagination.last_page"
            @click="changePage(pagination.current_page + 1)"
          >
            Next <ChevronRight class="w-4 h-4 ml-1" />
          </Button>
        </div>
      </CardFooter>
    </Card>

    <!-- Product Form Dialog -->
    <Dialog v-model:open="isModalOpen">
      <DialogContent class="sm:max-w-xl p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <div class="p-8 border-b bg-slate-50/50 flex items-center justify-between">
          <div class="space-y-1">
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
              {{ form.id ? 'Edit Produk' : 'Tambah Produk Baru' }}
            </h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Detail Informasi Produk</p>
          </div>
          <div class="p-3 rounded-2xl bg-white shadow-sm text-gold-500">
            <Package class="w-6 h-6" />
          </div>
        </div>
        
        <form @submit.prevent="saveProduct" class="p-8 space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Nama Produk</Label>
              <Input v-model="form.name" placeholder="Contoh: Brightening Serum" class="h-12 rounded-2xl bg-slate-50 border-slate-100" required />
            </div>
            <div class="space-y-2">
              <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Kategori</Label>
              <Select v-model="form.category">
                <SelectTrigger class="h-12 rounded-2xl bg-slate-50 border-slate-100">
                  <SelectValue placeholder="Pilih Kategori" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Harga Jual (Rp)</Label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">Rp</span>
              <Input v-model.number="form.price" type="number" min="0" class="h-12 pl-12 rounded-2xl bg-slate-50 border-slate-100 font-black text-lg" required />
            </div>
          </div>

          <!-- Image Upload -->
          <div class="space-y-3">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Foto Produk</Label>
            <div 
              @click="fileInput?.click()"
              @dragover.prevent
              @drop.prevent="onFileDrop"
              class="relative h-48 border-2 border-dashed border-slate-200 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:border-gold-500 hover:bg-gold-50/30 transition-all group overflow-hidden"
            >
              <div v-if="imagePreview || form.image_url" class="absolute inset-0">
                <img :src="imagePreview || form.image_url" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <p class="text-white font-bold flex items-center gap-2 bg-slate-900/50 px-4 py-2 rounded-2xl backdrop-blur-md">
                    <Camera class="w-4 h-4" /> Ganti Foto
                  </p>
                </div>
                <Button 
                  type="button" 
                  variant="destructive"
                  size="icon"
                  class="absolute top-4 right-4 h-8 w-8 rounded-xl shadow-lg"
                  @click.stop="clearImage"
                >
                  <X class="w-4 h-4" />
                </Button>
              </div>
              <div v-else class="text-center space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center mx-auto text-slate-400 group-hover:text-gold-500 transition-colors">
                  <Upload class="w-6 h-6" />
                </div>
                <p class="text-xs font-bold text-slate-500">Klik atau seret foto ke sini</p>
                <p class="text-[10px] text-slate-400">PNG, JPG, WEBP (Maks. 2MB)</p>
              </div>
              <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileSelect" />
            </div>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Deskripsi Produk</Label>
            <Textarea v-model="form.description" class="rounded-2xl bg-slate-50 border-slate-100 h-28 resize-none focus-visible:ring-gold-500/10" placeholder="Jelaskan detail produk, manfaat, dan cara pemakaian..." />
          </div>

          <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-slate-100">
            <Label for="isActive" class="text-sm font-bold text-slate-700">Tampilkan di katalog (Aktif)</Label>
            <Switch id="isActive" :checked="form.is_active" @update:checked="form.is_active = $event" />
          </div>

          <div class="flex gap-4 pt-4">
            <Button type="button" variant="ghost" class="flex-1 h-14 rounded-2xl font-bold text-slate-500" @click="closeModal">Batal</Button>
            <Button type="submit" class="flex-1 h-14 rounded-2xl bg-gold-500 hover:bg-gold-600 text-white font-black text-lg shadow-xl shadow-gold-500/20" :disabled="isSaving">
              <Loader2 v-if="isSaving" class="mr-2 w-5 h-5 animate-spin" />
              {{ isSaving ? 'Menyimpan...' : 'Simpan Produk' }}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Stock Management Dialog -->
    <Dialog v-model:open="isStockModalOpen">
      <DialogContent class="sm:max-w-md p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <div class="p-8 border-b bg-emerald-50/50 flex items-center justify-between">
          <div class="space-y-1">
            <h2 class="text-2xl font-black text-emerald-900 tracking-tight">Update Stok</h2>
            <p class="text-xs font-bold text-emerald-600/60 uppercase tracking-widest">Penyesuaian Inventori</p>
          </div>
          <div class="p-3 rounded-2xl bg-white shadow-sm text-emerald-500">
            <Boxes class="w-6 h-6" />
          </div>
        </div>
        
        <form @submit.prevent="saveStock" class="p-8 space-y-6">
          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Cabang Klinik</Label>
            <Select v-model="stockForm.branch_id">
              <SelectTrigger class="h-12 rounded-2xl bg-slate-50 border-slate-100 font-bold">
                <SelectValue placeholder="Pilih Cabang" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="space-y-2">
            <Label class="text-xs font-bold text-slate-700 uppercase tracking-wide ml-1">Jumlah Stok Baru</Label>
            <div class="relative">
              <Input v-model.number="stockForm.stock_quantity" type="number" min="0" class="h-14 rounded-2xl bg-slate-50 border-slate-100 font-black text-3xl text-center" required />
              <div class="absolute right-4 top-1/2 -translate-y-1/2 px-3 py-1 rounded-lg bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase">Unit</div>
            </div>
          </div>

          <div class="flex gap-4 pt-4">
            <Button type="submit" class="w-full h-14 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-lg shadow-xl shadow-emerald-500/20" :disabled="isSaving">
              <Loader2 v-if="isSaving" class="mr-2 w-5 h-5 animate-spin" />
              {{ isSaving ? 'Menyimpan...' : 'Perbarui Stok' }}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="showDeleteConfirm">
      <DialogContent class="sm:max-w-md p-0 overflow-hidden border-none rounded-3xl shadow-2xl bg-white">
        <div class="p-8 text-center space-y-4">
          <div class="mx-auto w-16 h-16 rounded-full bg-rose-50 flex items-center justify-center">
            <AlertTriangle class="w-8 h-8 text-rose-500" />
          </div>
          <div class="space-y-2">
            <h2 class="text-xl font-black text-slate-900">Nonaktifkan Produk?</h2>
            <p class="text-sm text-slate-500">Produk akan dinonaktifkan dan tidak akan muncul di katalog. Tindakan ini dapat dibalik dengan mengaktifkannya kembali.</p>
          </div>
          <div class="flex gap-4 pt-4">
            <Button variant="ghost" class="flex-1 h-12 rounded-2xl font-bold" @click="showDeleteConfirm = false">Batal</Button>
            <Button class="flex-1 h-12 rounded-2xl bg-rose-500 hover:bg-rose-600 text-white font-bold" @click="deleteProduct">
              Ya, Nonaktifkan
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import client from '@/api/client'
import { 
  Plus, Edit3, Trash2, X, Search, Package, Upload, 
  Loader2, PackageSearch, Building2, Boxes, Camera,
  ChevronLeft, ChevronRight, AlertTriangle
} from 'lucide-vue-next'
import { Card, CardFooter } from '@/components/ui/card'
import { Table, TableHeader, TableBody, TableHead, TableRow, TableCell } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select'
import { Dialog, DialogContent } from '@/components/ui/dialog'
import { Textarea } from '@/components/ui/textarea'
import { Switch } from '@/components/ui/switch'
import { toast } from 'vue-sonner'

const products = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const pagination = ref({ current_page: 1, last_page: 1 })

const searchQuery = ref('')
const selectedBranch = ref('all')

const isModalOpen = ref(false)
const isStockModalOpen = ref(false)
const showDeleteConfirm = ref(false)
const productToDelete = ref(null)
const branches = ref([])

const imagePreview = ref(null)
const fileInput = ref(null)

const categories = ['Cleanser', 'Toner', 'Serum', 'Moisturizer', 'Sunscreen', 'Mask', 'Lainnya']

const form = ref({
  id: null,
  name: '',
  description: '',
  price: 0,
  category: 'Cleanser',
  image_url: '',
  imageFile: null,
  is_active: true
})

const onFileSelect = (e) => {
  const file = e.target.files[0]
  if (!file) return
  form.value.imageFile = file
  form.value.image_url = ''
  imagePreview.value = URL.createObjectURL(file)
}

const onFileDrop = (e) => {
  const file = e.dataTransfer.files[0]
  if (!file || !file.type.startsWith('image/')) return
  form.value.imageFile = file
  form.value.image_url = ''
  imagePreview.value = URL.createObjectURL(file)
}

const clearImage = () => {
  form.value.imageFile = null
  form.value.image_url = ''
  imagePreview.value = null
}

const stockForm = ref({
  product_id: null,
  branch_id: null,
  stock_quantity: 0
})

const formatPrice = (n) => new Intl.NumberFormat('id-ID').format(n || 0)

const fetchProducts = async (page = 1) => {
  isLoading.value = true
  try {
    const params = { page, show_all: 1 }
    if (searchQuery.value) params.search = searchQuery.value
    if (selectedBranch.value && selectedBranch.value !== 'all') params.branch_id = Number(selectedBranch.value)

    const res = await client.get('/products', { params })
    products.value = res.data.data
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page
    }
  } catch (err) {
    toast.error('Gagal memuat produk')
  } finally {
    isLoading.value = false
  }
}

const fetchBranches = async () => {
  try {
    const res = await client.get('/admin/branches')
    branches.value = res.data || []
  } catch (err) {
    console.error('Failed to load branches', err)
    branches.value = []
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchProducts(page)
  }
}

const openModal = (product = null) => {
  imagePreview.value = null
  if (product) {
    form.value = { ...product, imageFile: null }
  } else {
    form.value = {
      id: null,
      name: '',
      description: '',
      price: 0,
      category: 'Cleanser',
      image_url: '',
      imageFile: null,
      is_active: true
    }
  }
  isModalOpen.value = true
}

const openStockModal = (product) => {
  stockForm.value = {
    product_id: product.id,
    branch_id: null,
    stock_quantity: 0
  }
  isStockModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const saveProduct = async () => {
  isSaving.value = true
  try {
    let payload
    const hasFile = !!form.value.imageFile

    if (hasFile) {
      payload = new FormData()
      payload.append('name', form.value.name)
      payload.append('description', form.value.description || '')
      payload.append('price', form.value.price)
      payload.append('category', form.value.category)
      payload.append('is_active', form.value.is_active ? 1 : 0)
      payload.append('image', form.value.imageFile)
      if (form.value.id) payload.append('_method', 'PUT')
    } else {
      payload = {
        name: form.value.name,
        description: form.value.description,
        price: form.value.price,
        category: form.value.category,
        image_url: form.value.image_url,
        is_active: form.value.is_active,
      }
    }

    const headers = hasFile ? { 'Content-Type': 'multipart/form-data' } : {}

    if (form.value.id) {
      const method = hasFile ? 'post' : 'put'
      await client[method](`/admin/products/${form.value.id}`, payload, { headers })
      toast.success('Produk berhasil diperbarui')
    } else {
      await client.post('/admin/products', payload, { headers })
      toast.success('Produk baru berhasil ditambahkan')
    }
    closeModal()
    fetchProducts(pagination.value.current_page)
  } catch (err) {
    toast.error(err?.response?.data?.message || 'Gagal menyimpan produk')
  } finally {
    isSaving.value = false
  }
}

const deleteProduct = async () => {
  if (!productToDelete.value) return
  try {
    await client.delete(`/admin/products/${productToDelete.value}`)
    toast.success('Produk berhasil dinonaktifkan')
    fetchProducts(pagination.value.current_page)
  } catch (err) {
    toast.error('Gagal menghapus produk')
  } finally {
    showDeleteConfirm.value = false
    productToDelete.value = null
  }
}

const saveStock = async () => {
  if (!stockForm.value.branch_id) {
    toast.error('Pilih cabang terlebih dahulu')
    return
  }
  isSaving.value = true
  try {
    await client.put(`/admin/products/${stockForm.value.product_id}/stock`, {
      branch_id: Number(stockForm.value.branch_id),
      stock_quantity: stockForm.value.stock_quantity
    })
    toast.success('Stok berhasil diperbarui')
    isStockModalOpen.value = false
    fetchProducts(pagination.value.current_page)
  } catch (err) {
    toast.error(err?.response?.data?.message || 'Gagal memperbarui stok')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchProducts()
  fetchBranches()
})
</script>
