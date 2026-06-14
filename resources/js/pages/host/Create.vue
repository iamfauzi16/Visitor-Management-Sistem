<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Tamu Saya', href: route('host.index') },
    { title: 'Daftarkan Tamu', href: route('host.create') },
];

const form = useForm({
    name:           '',
    id_type:        '',
    id_number:      '',
    company:        '',
    phone:          '',
    email:          '',
    purpose:        '',
    vehicle_number: '',
});

const submit = () => {
    form.post(route('host.store'));
};
</script>

<template>
    <Head title="Daftarkan Tamu" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="w-full">

                <form @submit.prevent="submit" class="flex flex-col gap-4">

                    <!-- Card 1: Data Tamu -->
                    <div class="rounded-lg border bg-card shadow-sm">
                        <div class="border-b px-6 py-4">
                            <h2 class="text-lg font-semibold">Data Tamu</h2>
                            <p class="text-sm text-muted-foreground">Identitas tamu yang akan berkunjung ke Anda.</p>
                        </div>
                        <div class="space-y-4 px-6 py-4">

                            <div class="grid gap-2">
                                <Label for="name">Nama Tamu <span class="text-destructive">*</span></Label>
                                <Input id="name" v-model="form.name" placeholder="Nama lengkap tamu" autofocus />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="id_type">Jenis Identitas</Label>
                                    <select
                                        id="id_type"
                                        v-model="form.id_type"
                                        class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                    >
                                        <option value="">-- Pilih --</option>
                                        <option value="ktp">KTP</option>
                                        <option value="sim">SIM</option>
                                        <option value="passport">Passport</option>
                                    </select>
                                    <InputError :message="form.errors.id_type" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="id_number">Nomor Identitas</Label>
                                    <Input id="id_number" v-model="form.id_number" placeholder="Nomor KTP / SIM / Passport" />
                                    <InputError :message="form.errors.id_number" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="company">Perusahaan / Instansi</Label>
                                    <Input id="company" v-model="form.company" placeholder="Nama instansi atau perusahaan" />
                                    <InputError :message="form.errors.company" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="phone">No. HP</Label>
                                    <Input id="phone" v-model="form.phone" placeholder="08xx-xxxx-xxxx" />
                                    <InputError :message="form.errors.phone" />
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input id="email" type="email" v-model="form.email" placeholder="email@example.com" />
                                <InputError :message="form.errors.email" />
                            </div>

                        </div>
                    </div>

                    <!-- Card 2: Keperluan -->
                    <div class="rounded-lg border bg-card shadow-sm">
                        <div class="border-b px-6 py-4">
                            <h2 class="text-lg font-semibold">Keperluan Kunjungan</h2>
                            <p class="text-sm text-muted-foreground">Tujuan tamu datang menemui Anda.</p>
                        </div>
                        <div class="space-y-4 px-6 py-4">

                            <div class="grid gap-2">
                                <Label for="purpose">Keterangan Keperluan</Label>
                                <textarea
                                    id="purpose"
                                    v-model="form.purpose"
                                    rows="3"
                                    placeholder="Contoh: Meeting diskusi proyek, presentasi produk, dll."
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                />
                                <InputError :message="form.errors.purpose" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="vehicle_number">No. Kendaraan (opsional)</Label>
                                <Input id="vehicle_number" v-model="form.vehicle_number" placeholder="Contoh: B 1234 ABC" />
                                <InputError :message="form.errors.vehicle_number" />
                            </div>

                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('host.index')">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Daftarkan Tamu
                        </Button>
                    </div>

                </form>

            </div>
        </div>
    </AppLayout>
</template>
