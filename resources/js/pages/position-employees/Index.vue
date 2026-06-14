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
import type { BreadcrumbItem, PaginatedResponse, PositionEmployee } from '@/types';

interface Filters {
    search?: string;
    status?: string;
}

const props = defineProps<{
    positions: PaginatedResponse<PositionEmployee>;
    filters: Filters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Jabatan', href: route('positions.index') },
];

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(route('positions.index'), {
        search: search.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilters = () => {
    search.value = '';
    status.value = '';
    router.get(route('positions.index'));
};

const deleteTarget = ref<PositionEmployee | null>(null);
const deleteForm = useForm({});

const confirmDelete = (item: PositionEmployee) => { deleteTarget.value = item; };
const cancelDelete = () => { deleteTarget.value = null; };
const submitDelete = () => {
    deleteForm.delete(route('positions.destroy', deleteTarget.value!.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
};
</script>

<template>
    <Head title="Jabatan Karyawan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">

            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-48">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Cari nama jabatan..."
                        class="pl-8"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <select
                    v-model="status"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>

                <Button v-if="search || status" variant="ghost" size="sm" @click="resetFilters">
                    Reset
                </Button>

                <div class="ml-auto">
                    <Button as-child>
                        <Link :href="route('positions.create')">
                            <Plus class="h-4 w-4" />
                            Tambah Jabatan
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
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Nama Jabatan</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-if="positions.data.length === 0">
                            <td colspan="4" class="px-4 py-8 text-center text-muted-foreground">
                                Tidak ada data jabatan.
                            </td>
                        </tr>
                        <tr
                            v-for="(item, index) in positions.data"
                            :key="item.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ (positions.current_page - 1) * positions.per_page + index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium">{{ item.name_position }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="item.status === 'active'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ item.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="route('positions.edit', item.id)">Edit</Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="confirmDelete(item)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between text-sm text-muted-foreground">
                <span>
                    Menampilkan {{ positions.from ?? 0 }}–{{ positions.to ?? 0 }}
                    dari {{ positions.total }} data
                </span>
                <div class="flex items-center gap-1">
                    <template v-for="link in positions.links" :key="link.label">
                        <Button
                            v-if="link.url"
                            :variant="link.active ? 'default' : 'ghost'"
                            size="sm"
                            as-child
                        >
                            <a :href="link.url" v-html="link.label" />
                        </Button>
                        <span v-else class="px-2 opacity-40" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog :open="!!deleteTarget" @update:open="cancelDelete">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Jabatan</DialogTitle>
                    <DialogDescription>
                        Yakin ingin menghapus jabatan <strong>{{ deleteTarget?.name_position }}</strong>?
                        Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelDelete">Batal</Button>
                    <Button variant="destructive" :disabled="deleteForm.processing" @click="submitDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
