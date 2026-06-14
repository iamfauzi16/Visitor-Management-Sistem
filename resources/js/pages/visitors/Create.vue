<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle } from 'lucide-vue-next';
import type { BreadcrumbItem, EmployeeOption } from '@/types';

const props = defineProps<{
    employees: EmployeeOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Daftar Tamu', href: route('visitors.index') },
    { title: 'Registrasi Tamu', href: route('visitors.create') },
];

const form = useForm({
    name:           '',
    id_type:        '',
    id_number:      '',
    company:        '',
    phone:          '',
    email:          '',
    employee_id:    null as number | null,
    purpose:        '',
    vehicle_number: '',
    badge_number:   '',
});

const submit = () => {
    form.post(route('visitors.store'));
};
</script>

<template>
    <Head title="Registrasi Tamu" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="w-full">

                <form @submit.prevent="submit" class="flex flex-col gap-4">

                    <!-- Card 1: Data Tamu -->
                    <div class="rounded-lg border bg-card shadow-sm">
                        <div class="border-b px-6 py-4">
                            <h2 class="text-lg font-semibold">Data Tamu</h2>
                            <p class="text-sm text-muted-foreground">Identitas tamu yang akan berkunjung.</p>
                        </div>
                        <div class="space-y-4 px-6 py-4">

                            <!-- Nama -->
                            <div class="grid gap-2">
                                <Label for="name">Nama Tamu <span class="text-destructive">*</span></Label>
                                <Input id="name" v-model="form.name" placeholder="Nama lengkap tamu" autofocus />
                                <InputError :message="form.errors.name" />
                            </div>

                            <!-- Identitas -->
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

                            <!-- Perusahaan & No. HP -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="company">Perusahaan</Label>
                                    <Input id="company" v-model="form.company" placeholder="Nama instansi/perusahaan" />
                                    <InputError :message="form.errors.company" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="phone">No. HP</Label>
                                    <Input id="phone" v-model="form.phone" placeholder="08xx-xxxx-xxxx" />
                                    <InputError :message="form.errors.phone" />
                                </div>
                            </div>

                            <!-- Email -->
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
                            <h2 class="text-lg font-semibold">Keperluan</h2>
                            <p class="text-sm text-muted-foreground">Tujuan dan maksud kunjungan tamu.</p>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid gap-2">
                                <Label for="purpose">Keterangan Keperluan</Label>
                                <textarea
                                    id="purpose"
                                    v-model="form.purpose"
                                    rows="4"
                                    placeholder="Keterangan keperluan kunjungan..."
                                    class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                />
                                <InputError :message="form.errors.purpose" />
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Kunjungan -->
                    <div class="rounded-lg border bg-card shadow-sm">
                        <div class="border-b px-6 py-4">
                            <h2 class="text-lg font-semibold">Detail Kunjungan</h2>
                            <p class="text-sm text-muted-foreground">Karyawan yang dituju dan informasi tambahan.</p>
                        </div>
                        <div class="space-y-4 px-6 py-4">

                            <!-- Host -->
                            <div class="grid gap-2">
                                <Label for="employee_id">Karyawan yang Dikunjungi <span class="text-destructive">*</span></Label>
                                <select
                                    id="employee_id"
                                    v-model="form.employee_id"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                                    <option :value="null">-- Pilih Karyawan --</option>
                                    <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                                        {{ emp.name }} — {{ emp.department }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.employee_id" />
                            </div>

                            <!-- Kendaraan & Badge -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="vehicle_number">No. Kendaraan</Label>
                                    <Input id="vehicle_number" v-model="form.vehicle_number" placeholder="Contoh: B 1234 ABC" />
                                    <InputError :message="form.errors.vehicle_number" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="badge_number">No. Badge</Label>
                                    <Input id="badge_number" v-model="form.badge_number" placeholder="Nomor badge tamu" />
                                    <InputError :message="form.errors.badge_number" />
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2">
                        <Button type="button" variant="outline" as-child>
                            <Link :href="route('visitors.index')">Batal</Link>
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
