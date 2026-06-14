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
    { title: 'Pengguna', href: route('users.index') },
    { title: 'Tambah', href: route('users.create') },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'receptionist' as 'admin' | 'receptionist' | 'host',
    employee_id: null as number | null,
});

const submit = () => {
    form.post(route('users.store'));
};
</script>

<template>
    <Head title="Tambah Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">

                <div class="rounded-lg border bg-card shadow-sm">
                    <div class="border-b px-6 py-4">
                        <h2 class="text-lg font-semibold">Tambah Pengguna</h2>
                        <p class="text-sm text-muted-foreground">Buat akun pengguna baru dan tentukan role-nya.</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4 px-6 py-4">

                        <div class="grid gap-2">
                            <Label for="name">Nama <span class="text-destructive">*</span></Label>
                            <Input id="name" v-model="form.name" placeholder="Nama lengkap" autofocus />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email <span class="text-destructive">*</span></Label>
                            <Input id="email" type="email" v-model="form.email" placeholder="email@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role">Role <span class="text-destructive">*</span></Label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="admin">Admin — Akses penuh ke semua fitur</option>
                                <option value="receptionist">Resepsionis — Registrasi & manajemen tamu</option>
                                <option value="host">Host — Karyawan yang bisa daftarkan tamunya sendiri</option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <!-- Hanya tampil saat role = host -->
                        <div v-if="form.role === 'host'" class="grid gap-2">
                            <Label for="employee_id">Karyawan yang Ditautkan <span class="text-destructive">*</span></Label>
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

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="password">Password <span class="text-destructive">*</span></Label>
                                <Input id="password" type="password" v-model="form.password" placeholder="Min. 8 karakter" />
                                <InputError :message="form.errors.password" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="password_confirmation">Konfirmasi Password <span class="text-destructive">*</span></Label>
                                <Input id="password_confirmation" type="password" v-model="form.password_confirmation" placeholder="Ulangi password" />
                                <InputError :message="form.errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 border-t pt-4">
                            <Button type="button" variant="outline" as-child>
                                <Link :href="route('users.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Simpan
                            </Button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
