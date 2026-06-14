<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Download } from 'lucide-vue-next';
import type { BreadcrumbItem, PaginatedResponse } from '@/types';
import type { VisitorRow } from '@/types/visitors';
import type { ReportFilters } from '@/types/reports';

const props = defineProps<{
    visitors: PaginatedResponse<VisitorRow>;
    filters: ReportFilters;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Laporan', href: route('reports.index') },
];

const dateFrom = ref(props.filters.date_from ?? '');
const dateTo   = ref(props.filters.date_to ?? '');
const status   = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(route('reports.index'), {
        date_from: dateFrom.value || undefined,
        date_to:   dateTo.value || undefined,
        status:    status.value || undefined,
    }, { preserveState: true, replace: true });
};

const resetFilters = () => {
    dateFrom.value = '';
    dateTo.value   = '';
    status.value   = '';
    router.get(route('reports.index'));
};

const hasFilters = computed(() => dateFrom.value || dateTo.value || status.value);

const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (dateFrom.value) params.set('date_from', dateFrom.value);
    if (dateTo.value)   params.set('date_to', dateTo.value);
    if (status.value)   params.set('status', status.value);
    const qs = params.toString();
    return route('reports.export') + (qs ? `?${qs}` : '');
});

const statusClass = (s: string) => ({
    'registered':  'bg-gray-100 text-gray-600',
    'checked_in':  'bg-green-100 text-green-700',
    'checked_out': 'bg-blue-100 text-blue-700',
}[s] ?? '');

const formatDate = (dt: string | null) =>
    dt ? new Date(dt).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' }) : '—';
</script>

<template>
    <Head title="Laporan Tamu" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">

            <!-- Toolbar -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="flex items-center gap-1.5">
                    <span class="text-sm text-muted-foreground whitespace-nowrap">Dari</span>
                    <Input
                        v-model="dateFrom"
                        type="date"
                        class="h-9 w-40"
                        @change="applyFilters"
                    />
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-sm text-muted-foreground whitespace-nowrap">s/d</span>
                    <Input
                        v-model="dateTo"
                        type="date"
                        class="h-9 w-40"
                        @change="applyFilters"
                    />
                </div>

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

                <Button v-if="hasFilters" variant="ghost" size="sm" @click="resetFilters">
                    Reset
                </Button>

                <div class="ml-auto">
                    <Button variant="outline" size="sm" as-child>
                        <a :href="exportUrl">
                            <Download class="h-4 w-4" />
                            Export Excel
                        </a>
                    </Button>
                </div>
            </div>

            <!-- Summary -->
            <p class="text-sm text-muted-foreground">
                Total: <strong>{{ visitors.total }}</strong> data
                <template v-if="dateFrom || dateTo">
                    ({{ dateFrom || '…' }} — {{ dateTo || '…' }})
                </template>
            </p>

            <!-- Table -->
            <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">No</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Nama Tamu</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Perusahaan</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Host</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Keperluan</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Masuk</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-if="visitors.data.length === 0">
                            <td colspan="8" class="px-4 py-10 text-center text-muted-foreground">
                                Tidak ada data untuk filter yang dipilih.
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
                            <td class="px-4 py-3 text-muted-foreground max-w-48 truncate">{{ item.purpose ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="statusClass(item.status)"
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                >
                                    {{ item.status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground text-xs">{{ formatDate(item.checkin_at) }}</td>
                            <td class="px-4 py-3 text-muted-foreground text-xs">{{ formatDate(item.checkout_at) }}</td>
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
    </AppLayout>
</template>
