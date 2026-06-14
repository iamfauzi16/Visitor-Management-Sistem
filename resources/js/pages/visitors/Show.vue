<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog, DialogContent, DialogDescription,
    DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import { LogIn, LogOut } from 'lucide-vue-next';
import type { BreadcrumbItem, Visitor } from '@/types';

const props = defineProps<{
    visitor: Visitor;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Daftar Tamu', href: route('visitors.index') },
    { title: props.visitor.name, href: route('visitors.show', props.visitor.id) },
];

const checkinForm  = useForm({});
const checkoutForm = useForm({});

const showCheckin  = ref(false);
const showCheckout = ref(false);

const submitCheckin = () => {
    checkinForm.patch(route('visitors.checkin', props.visitor.id), {
        onSuccess: () => { showCheckin.value = false; },
    });
};

const submitCheckout = () => {
    checkoutForm.patch(route('visitors.checkout', props.visitor.id), {
        onSuccess: () => { showCheckout.value = false; },
    });
};

const statusClass = (s: string) => ({
    'registered':  'bg-gray-100 text-gray-600',
    'checked_in':  'bg-green-100 text-green-700',
    'checked_out': 'bg-blue-100 text-blue-700',
}[s] ?? '');

const idTypeLabel = (t: string | null) => ({
    ktp: 'KTP', sim: 'SIM', passport: 'Passport',
}[t ?? ''] ?? '—');

const formatDt = (dt: string | null) =>
    dt ? new Date(dt).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) : '—';
</script>

<template>
    <Head :title="visitor.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl space-y-4">

                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-semibold">{{ visitor.name }}</h1>
                        <span
                            :class="statusClass(visitor.status)"
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                        >
                            {{ visitor.status_label }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button
                            v-if="visitor.status === 'registered'"
                            class="bg-green-600 hover:bg-green-700 text-white"
                            @click="showCheckin = true"
                        >
                            <LogIn class="h-4 w-4" />
                            Check In
                        </Button>
                        <Button
                            v-if="visitor.status === 'checked_in'"
                            @click="showCheckout = true"
                        >
                            <LogOut class="h-4 w-4" />
                            Check Out
                        </Button>
                    </div>
                </div>

                <!-- Detail Card -->
                <div class="rounded-lg border bg-card shadow-sm divide-y">
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Perusahaan</dt>
                        <dd class="col-span-2 text-sm">{{ visitor.company ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">No. HP</dt>
                        <dd class="col-span-2 text-sm">{{ visitor.phone ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Email</dt>
                        <dd class="col-span-2 text-sm">{{ visitor.email ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Identitas</dt>
                        <dd class="col-span-2 text-sm">
                            {{ idTypeLabel(visitor.id_type) }}
                            <span v-if="visitor.id_number"> — {{ visitor.id_number }}</span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Host (Karyawan)</dt>
                        <dd class="col-span-2 text-sm">
                            {{ visitor.employee?.name ?? '—' }}
                            <span v-if="visitor.employee?.department" class="text-muted-foreground">
                                — {{ visitor.employee.department }}
                            </span>
                        </dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Keperluan</dt>
                        <dd class="col-span-2 text-sm whitespace-pre-wrap">{{ visitor.purpose ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">No. Kendaraan</dt>
                        <dd class="col-span-2 text-sm">{{ visitor.vehicle_number ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">No. Badge</dt>
                        <dd class="col-span-2 text-sm">{{ visitor.badge_number ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Waktu Registrasi</dt>
                        <dd class="col-span-2 text-sm">{{ formatDt(visitor.created_at) }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Check In</dt>
                        <dd class="col-span-2 text-sm">{{ formatDt(visitor.checkin_at) }}</dd>
                    </div>
                    <div class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Check Out</dt>
                        <dd class="col-span-2 text-sm">{{ formatDt(visitor.checkout_at) }}</dd>
                    </div>
                    <div v-if="visitor.creator" class="grid grid-cols-3 px-6 py-3 gap-4">
                        <dt class="text-sm font-medium text-muted-foreground">Didaftarkan oleh</dt>
                        <dd class="col-span-2 text-sm">{{ visitor.creator.name }}</dd>
                    </div>
                </div>

                <div>
                    <Button variant="ghost" size="sm" as-child>
                        <Link :href="route('visitors.index')">← Kembali ke Daftar</Link>
                    </Button>
                </div>

            </div>
        </div>

        <!-- Check-in Dialog -->
        <Dialog :open="showCheckin" @update:open="showCheckin = false">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Konfirmasi Check In</DialogTitle>
                    <DialogDescription>
                        Catat waktu masuk untuk tamu <strong>{{ visitor.name }}</strong>?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showCheckin = false">Batal</Button>
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
        <Dialog :open="showCheckout" @update:open="showCheckout = false">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Konfirmasi Check Out</DialogTitle>
                    <DialogDescription>
                        Catat waktu keluar untuk tamu <strong>{{ visitor.name }}</strong>?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="showCheckout = false">Batal</Button>
                    <Button :disabled="checkoutForm.processing" @click="submitCheckout">
                        Check Out
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
