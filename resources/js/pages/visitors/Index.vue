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
import { LogIn, LogOut, Plus, Search } from 'lucide-vue-next';
import type { BreadcrumbItem, Employee, PaginatedResponse } from '@/types';
import type { VisitorFilters, VisitorRow } from '@/types/visitors';

const props = defineProps<{
    visitors: PaginatedResponse<VisitorRow>;
    filters: VisitorFilters;
    employees: Pick<Employee, 'id' | 'name' | 'department'>[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Daftar Tamu', href: route('visitors.index') },
];

const search      = ref(props.filters.search ?? '');
const employeeId  = ref(props.filters.employee_id ?? '');
const date        = ref(props.filters.date ?? '');
const status      = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(route('visitors.index'), {
        search:      search.value || undefined,
        employee_id: employeeId.value || undefined,
        date:        date.value || undefined,
        status:      status.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilters = () => {
    search.value     = '';
    employeeId.value = '';
    date.value       = '';
    status.value     = '';
    router.get(route('visitors.index'));
};

const hasFilters = () => search.value || employeeId.value || date.value || status.value;

// Check-in
const checkinTarget = ref<VisitorRow | null>(null);
const checkinForm   = useForm({});
const confirmCheckin  = (v: VisitorRow) => { checkinTarget.value = v; };
const cancelCheckin   = () => { checkinTarget.value = null; };
const submitCheckin   = () => {
    checkinForm.patch(route('visitors.checkin', checkinTarget.value!.id), {
        onSuccess: () => { checkinTarget.value = null; },
    });
};

// Check-out
const checkoutTarget = ref<VisitorRow | null>(null);
const checkoutForm   = useForm({});
const confirmCheckout  = (v: VisitorRow) => { checkoutTarget.value = v; };
const cancelCheckout   = () => { checkoutTarget.value = null; };
const submitCheckout   = () => {
    checkoutForm.patch(route('visitors.checkout', checkoutTarget.value!.id), {
        onSuccess: () => { checkoutTarget.value = null; },
    });
};

const statusClass = (s: string) => ({
    'registered':  'bg-gray-100 text-gray-600',
    'checked_in':  'bg-green-100 text-green-700',
    'checked_out': 'bg-blue-100 text-blue-700',
}[s] ?? '');

const formatTime = (dt: string | null) =>
    dt ? new Date(dt).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' }) : '—';
</script>

<template>
    <Head title="Daftar Tamu" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">

            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-48">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Cari nama tamu atau perusahaan..."
                        class="pl-8"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <select
                    v-model="employeeId"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">Semua Host</option>
                    <option v-for="emp in employees" :key="emp.id" :value="String(emp.id)">
                        {{ emp.name }}
                    </option>
                </select>

                <select
                    v-model="status"
                    class="h-9 rounded-md border border-input bg-background px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">Semua Status</option>
                    <option value="registered">Registered</option>
                    <option value="checked_in">Inside</option>
                    <option value="checked_out">Checked Out</option>
                </select>

                <Input
                    v-model="date"
                    type="date"
                    class="h-9 w-40"
                    @change="applyFilters"
                />

                <Button v-if="hasFilters()" variant="ghost" size="sm" @click="resetFilters">
                    Reset
                </Button>

                <div class="ml-auto">
                    <Button as-child>
                        <Link :href="route('visitors.create')">
                            <Plus class="h-4 w-4" />
                            Registrasi Tamu
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
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Host</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Masuk</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Keluar</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-if="visitors.data.length === 0">
                            <td colspan="8" class="px-4 py-8 text-center text-muted-foreground">
                                Tidak ada data tamu.
                            </td>
                        </tr>
                        <tr
                            v-for="(item, index) in visitors.data"
                            :key="item.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ (visitors.current_page - 1) * visitors.per_page + index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <Link :href="route('visitors.show', item.id)" class="hover:underline">
                                    {{ item.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">{{ item.company ?? '—' }}</td>
                            <td class="px-4 py-3">{{ item.employee?.name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="statusClass(item.status)"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ item.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground text-xs">{{ formatTime(item.checkin_at) }}</td>
                            <td class="px-4 py-3 text-muted-foreground text-xs">{{ formatTime(item.checkout_at) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <Button
                                        v-if="item.status === 'registered'"
                                        variant="outline"
                                        size="sm"
                                        class="text-green-700 border-green-300 hover:bg-green-50"
                                        @click="confirmCheckin(item)"
                                    >
                                        <LogIn class="h-3.5 w-3.5" />
                                        Check In
                                    </Button>
                                    <Button
                                        v-if="item.status === 'checked_in'"
                                        variant="outline"
                                        size="sm"
                                        class="text-blue-700 border-blue-300 hover:bg-blue-50"
                                        @click="confirmCheckout(item)"
                                    >
                                        <LogOut class="h-3.5 w-3.5" />
                                        Check Out
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="route('visitors.show', item.id)">Detail</Link>
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
                    Menampilkan {{ visitors.from ?? 0 }}–{{ visitors.to ?? 0 }}
                    dari {{ visitors.total }} data
                </span>
                <div class="flex items-center gap-1">
                    <template v-for="link in visitors.links" :key="link.label">
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

        <!-- Check-in Dialog -->
        <Dialog :open="!!checkinTarget" @update:open="cancelCheckin">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Konfirmasi Check In</DialogTitle>
                    <DialogDescription>
                        Catat waktu masuk untuk tamu <strong>{{ checkinTarget?.name }}</strong>?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelCheckin">Batal</Button>
                    <Button
                        class="bg-green-600 hover:bg-green-700 text-white"
                        :disabled="checkinForm.processing"
                        @click="submitCheckin"
                    >
                        Check In
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Check-out Dialog -->
        <Dialog :open="!!checkoutTarget" @update:open="cancelCheckout">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Konfirmasi Check Out</DialogTitle>
                    <DialogDescription>
                        Catat waktu keluar untuk tamu <strong>{{ checkoutTarget?.name }}</strong>?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelCheckout">Batal</Button>
                    <Button :disabled="checkoutForm.processing" @click="submitCheckout">
                        Check Out
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
