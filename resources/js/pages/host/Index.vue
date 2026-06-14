<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Search, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItem, PaginatedResponse, Visitor, VisitorStatus } from '@/types';
import type { HostFilters } from '@/types/host';

const props = defineProps<{
    visitors: PaginatedResponse<Visitor> | null;
    filters: HostFilters;
    noEmployee: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Tamu Saya', href: route('host.index') },
];

const search = ref(props.filters.search ?? '');
const date   = ref(props.filters.date ?? '');
const status = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(route('host.index'), {
        search: search.value || undefined,
        date:   date.value   || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilters = () => {
    search.value = '';
    date.value   = '';
    status.value = '';
    router.get(route('host.index'));
};

const statusBadge: Record<VisitorStatus, string> = {
    registered:  'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
    checked_in:  'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    checked_out: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
};

const statusLabel: Record<VisitorStatus, string> = {
    registered:  'Terdaftar',
    checked_in:  'Di Dalam',
    checked_out: 'Selesai',
};

const deleteTarget = ref<Visitor | null>(null);
const deleteForm   = useForm({});

const confirmDelete  = (item: Visitor) => { deleteTarget.value = item; };
const cancelDelete   = () => { deleteTarget.value = null; };
const submitDelete   = () => {
    deleteForm.delete(route('host.destroy', { visitor: deleteTarget.value!.id }), {
        onSuccess: () => { deleteTarget.value = null; },
    });
};
</script>

<template>
    <Head title="Tamu Saya" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">

            <!-- Peringatan: akun belum ditautkan ke karyawan -->
            <div v-if="noEmployee" class="rounded-lg border border-yellow-200 bg-yellow-50 px-6 py-5 dark:border-yellow-800 dark:bg-yellow-900/20">
                <p class="font-medium text-yellow-800 dark:text-yellow-300">Akun belum ditautkan ke data karyawan</p>
                <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-400">
                    Hubungi Admin untuk menautkan akun Anda ke data karyawan agar bisa mendaftarkan tamu.
                </p>
            </div>

            <template v-else>
                <!-- Toolbar -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative flex-1 min-w-48">
                        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Cari nama atau perusahaan..."
                            class="pl-8"
                            @keyup.enter="applyFilters"
                        />
                    </div>

                    <input
                        v-model="date"
                        type="date"
                        class="h-9 rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        @change="applyFilters"
                    />

                    <select
                        v-model="status"
                        class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="">Semua Status</option>
                        <option value="registered">Terdaftar</option>
                        <option value="checked_in">Di Dalam</option>
                        <option value="checked_out">Selesai</option>
                    </select>

                    <Button v-if="search || date || status" variant="ghost" size="sm" @click="resetFilters">
                        Reset
                    </Button>

                    <div class="ml-auto">
                        <Button as-child>
                            <Link :href="route('host.create')">
                                <Plus class="h-4 w-4" />
                                Daftarkan Tamu
                            </Link>
                        </Button>
                    </div>
                </div>

                <!-- Table -->
                <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="border-b bg-muted/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">No</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Nama Tamu</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Perusahaan</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">No. HP</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Keperluan</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Tanggal</th>
                                <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-if="visitors!.data.length === 0">
                                <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">
                                    Belum ada tamu terdaftar.
                                </td>
                            </tr>
                            <tr
                                v-for="(item, index) in visitors!.data"
                                :key="item.id"
                                class="hover:bg-muted/30 transition-colors"
                            >
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ (visitors!.current_page - 1) * visitors!.per_page + index + 1 }}
                                </td>
                                <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                                <td class="px-4 py-3">{{ item.company ?? '—' }}</td>
                                <td class="px-4 py-3">{{ item.phone ?? '—' }}</td>
                                <td class="px-4 py-3 max-w-48 truncate">{{ item.purpose ?? '—' }}</td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="statusBadge[item.status]"
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                    >
                                        {{ statusLabel[item.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button
                                            v-if="item.status === 'registered'"
                                            variant="ghost"
                                            size="sm"
                                            class="text-destructive hover:text-destructive"
                                            @click="confirmDelete(item)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                        <span v-else class="text-xs text-muted-foreground">—</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between text-sm text-muted-foreground">
                    <span>
                        Menampilkan {{ visitors!.from ?? 0 }}–{{ visitors!.to ?? 0 }}
                        dari {{ visitors!.total }} data
                    </span>
                    <div class="flex items-center gap-1">
                        <template v-for="link in visitors!.links" :key="link.label">
                            <Button
                                v-if="link.url"
                                :variant="link.active ? 'default' : 'ghost'"
                                size="sm"
                                as-child
                            >
                                <Link :href="link.url" preserve-scroll v-html="link.label" />
                            </Button>
                            <span v-else class="px-2 opacity-40" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </template>

        </div>

        <!-- Dialog Konfirmasi Batalkan -->
        <Dialog :open="!!deleteTarget" @update:open="cancelDelete">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Batalkan Undangan</DialogTitle>
                    <DialogDescription>
                        Yakin ingin membatalkan undangan untuk <strong>{{ deleteTarget?.name }}</strong>?
                        Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelDelete">Kembali</Button>
                    <Button variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">
                        Batalkan Undangan
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
