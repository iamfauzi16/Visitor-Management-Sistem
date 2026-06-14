<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Search, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItem, PaginatedResponse, SharedData, User } from '@/types';
import type { UserFilters } from '@/types/users';

const props = defineProps<{
    users: PaginatedResponse<User>;
    filters: UserFilters;
}>();

const page = usePage<SharedData>();
const currentUserId = page.props.auth.user.id;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Pengguna', href: route('users.index') },
];

const search = ref(props.filters.search ?? '');
const role = ref(props.filters.role ?? '');

const applyFilters = () => {
    router.get(route('users.index'), {
        search: search.value || undefined,
        role: role.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilters = () => {
    search.value = '';
    role.value = '';
    router.get(route('users.index'));
};

const deleteTarget = ref<User | null>(null);
const deleteForm = useForm({});

const confirmDelete = (item: User) => { deleteTarget.value = item; };
const cancelDelete = () => { deleteTarget.value = null; };
const submitDelete = () => {
    deleteForm.delete(route('users.destroy', deleteTarget.value!.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
};

const roleLabel = (r: string) => {
    const map: Record<string, string> = {
        admin: 'Admin',
        receptionist: 'Resepsionis',
    };
    return map[r] ?? r;
};
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">

            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-48">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Cari nama atau email..."
                        class="pl-8"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <select
                    v-model="role"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="receptionist">Resepsionis</option>
                </select>

                <Button v-if="search || role" variant="ghost" size="sm" @click="resetFilters">
                    Reset
                </Button>

                <div class="ml-auto">
                    <Button as-child>
                        <Link :href="route('users.create')">
                            <Plus class="h-4 w-4" />
                            Tambah Pengguna
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
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Nama</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Email</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Role</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Terdaftar</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-if="users.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                        <tr
                            v-for="(item, index) in users.data"
                            :key="item.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ (users.current_page - 1) * users.per_page + index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ item.name }}
                                <span v-if="item.id === currentUserId" class="ml-1 text-xs text-muted-foreground">(Anda)</span>
                            </td>
                            <td class="px-4 py-3">{{ item.email }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="item.role === 'admin'
                                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
                                        : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ roleLabel(item.role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="route('users.edit', item.id)">Edit</Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        :disabled="item.id === currentUserId"
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
                    Menampilkan {{ users.from ?? 0 }}–{{ users.to ?? 0 }}
                    dari {{ users.total }} data
                </span>
                <div class="flex items-center gap-1">
                    <template v-for="link in users.links" :key="link.label">
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

        <!-- Delete Confirmation Dialog -->
        <Dialog :open="!!deleteTarget" @update:open="cancelDelete">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Pengguna</DialogTitle>
                    <DialogDescription>
                        Yakin ingin menghapus pengguna <strong>{{ deleteTarget?.name }}</strong>?
                        Akun ini akan dihapus permanen dan tidak dapat dipulihkan.
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
