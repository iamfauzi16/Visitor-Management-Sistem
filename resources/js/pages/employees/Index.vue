<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Download, Plus, Search, Trash2, Upload } from 'lucide-vue-next';
import type { BreadcrumbItem, Employee, PaginatedResponse } from '@/types';
import type { EmployeeFilters } from '@/types/employees';

const props = defineProps<{
    employees: PaginatedResponse<Employee>;
    filters: EmployeeFilters;
    departments: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Karyawan', href: route('employees.index') },
];

const search = ref(props.filters.search ?? '');
const department = ref(props.filters.department ?? '');
const status = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(route('employees.index'), {
        search: search.value || undefined,
        department: department.value || undefined,
        status: status.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilters = () => {
    search.value = '';
    department.value = '';
    status.value = '';
    router.get(route('employees.index'));
};

const deleteTarget = ref<Employee | null>(null);
const deleteForm = useForm({});

const confirmDelete = (item: Employee) => { deleteTarget.value = item; };
const cancelDelete = () => { deleteTarget.value = null; };
const submitDelete = () => {
    deleteForm.delete(route('employees.destroy', deleteTarget.value!.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
};

const importRef = ref<HTMLInputElement | null>(null);
const importForm = useForm({ file: null as File | null });

const triggerImport = () => importRef.value?.click();
const handleImport = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    importForm.file = file;
    importForm.post(route('employees.import'), {
        onSuccess: () => { importForm.reset(); },
    });
};

const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (search.value) params.set('search', search.value);
    if (department.value) params.set('department', department.value);
    if (status.value) params.set('status', status.value);
    return `${route('employees.export')}?${params.toString()}`;
});
</script>

<template>
    <Head title="Karyawan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">

            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative min-w-48 flex-1">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Cari nama, NIK, atau email..."
                        class="pl-8"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <select
                    v-model="department"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">Semua Departemen</option>
                    <option v-for="d in departments" :key="d" :value="d">{{ d }}</option>
                </select>

                <select
                    v-model="status"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>

                <Button v-if="search || department || status" variant="ghost" size="sm" @click="resetFilters">
                    Reset
                </Button>

                <div class="ml-auto flex items-center gap-2">
                    <input ref="importRef" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="handleImport" />
                    <Button variant="outline" size="sm" :disabled="importForm.processing" @click="triggerImport">
                        <Upload class="h-4 w-4" />
                        Import
                    </Button>

                    <Button variant="outline" size="sm" as-child>
                        <a :href="exportUrl">
                            <Download class="h-4 w-4" />
                            Export
                        </a>
                    </Button>

                    <Button as-child>
                        <Link :href="route('employees.create')">
                            <Plus class="h-4 w-4" />
                            Tambah
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-lg border bg-card shadow-sm">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">No</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">NIK</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Nama</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Departemen</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Jabatan</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-if="employees.data.length === 0">
                            <td colspan="7" class="px-4 py-8 text-center text-muted-foreground">
                                Tidak ada data.
                            </td>
                        </tr>
                        <tr
                            v-for="(item, index) in employees.data"
                            :key="item.id"
                            class="transition-colors hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ (employees.current_page - 1) * employees.per_page + index + 1 }}
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">{{ item.nik ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium">{{ item.name }}</td>
                            <td class="px-4 py-3">{{ item.department }}</td>
                            <td class="px-4 py-3">{{ item.position }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="item.is_active
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="route('employees.edit', item.id)">Edit</Link>
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
                    Menampilkan {{ employees.from ?? 0 }}–{{ employees.to ?? 0 }}
                    dari {{ employees.total }} data
                </span>
                <div class="flex items-center gap-1">
                    <template v-for="link in employees.links" :key="link.label">
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
        </div>

        <!-- Delete Dialog -->
        <Dialog :open="!!deleteTarget" @update:open="cancelDelete">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Karyawan</DialogTitle>
                    <DialogDescription>
                        Yakin ingin menghapus <strong>{{ deleteTarget?.name }}</strong>?
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
