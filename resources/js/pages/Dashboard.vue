<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { CalendarCheck, LogIn, Plus, Users } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { VisitorRow } from '@/types/visitors';

const props = defineProps<{
    total_today: number;
    checked_in: number;
    checked_out: number;
    visitors_today: VisitorRow[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
];

const statusClass = (s: string) => ({
    'registered':  'bg-gray-100 text-gray-600',
    'checked_in':  'bg-green-100 text-green-700',
    'checked_out': 'bg-blue-100 text-blue-700',
}[s] ?? '');

const formatTime = (dt: string | null) =>
    dt ? new Date(dt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '—';
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">

            <!-- Stat Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Total Hari Ini -->
                <div class="rounded-xl border bg-card p-5 shadow-sm flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                        <Users class="h-6 w-6 text-primary" />
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Total Tamu Hari Ini</p>
                        <p class="text-3xl font-bold">{{ total_today }}</p>
                    </div>
                </div>

                <!-- Sedang Berkunjung -->
                <div class="rounded-xl border bg-card p-5 shadow-sm flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                        <LogIn class="h-6 w-6 text-green-600" />
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Sedang Berkunjung</p>
                        <p class="text-3xl font-bold text-green-600">{{ checked_in }}</p>
                    </div>
                </div>

                <!-- Sudah Selesai -->
                <div class="rounded-xl border bg-card p-5 shadow-sm flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                        <CalendarCheck class="h-6 w-6 text-blue-600" />
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Sudah Check Out</p>
                        <p class="text-3xl font-bold text-blue-600">{{ checked_out }}</p>
                    </div>
                </div>
            </div>

            <!-- Table Tamu Hari Ini -->
            <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b">
                    <h2 class="font-semibold text-base">Tamu Hari Ini</h2>
                    <Button as-child size="sm">
                        <Link :href="route('visitors.create')">
                            <Plus class="h-4 w-4" />
                            Registrasi Tamu
                        </Link>
                    </Button>
                </div>

                <table class="w-full text-sm">
                    <thead class="border-b bg-muted/50">
                        <tr>
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
                        <tr v-if="visitors_today.length === 0">
                            <td colspan="7" class="px-4 py-10 text-center text-muted-foreground">
                                Belum ada tamu hari ini.
                            </td>
                        </tr>
                        <tr
                            v-for="item in visitors_today"
                            :key="item.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
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
                            <td class="px-4 py-3 text-right">
                                <Button variant="ghost" size="sm" as-child>
                                    <Link :href="route('visitors.show', item.id)">Detail</Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="visitors_today.length > 0" class="px-5 py-3 border-t text-right">
                    <Link :href="route('visitors.index')" class="text-sm text-primary hover:underline">
                        Lihat semua tamu →
                    </Link>
                </div>
            </div>

        </div>
    </AppLayout>
</template>
