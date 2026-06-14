<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{ positions: string[] }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Karyawan', href: route('employees.index') },
    { title: 'Tambah', href: route('employees.create') },
];

const form = useForm({
    nik: '',
    name: '',
    department: '',
    position: '',
    email: '',
    phone: '',
    is_active: true,
});

const submit = () => {
    form.post(route('employees.store'));
};
</script>

<template>
    <Head title="Tambah Karyawan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-full">
                <div class="rounded-lg border bg-card shadow-sm">
                    <div class="border-b px-6 py-4">
                        <h2 class="text-lg font-semibold">Tambah Karyawan</h2>
                        <p class="text-sm text-muted-foreground">Isi data karyawan baru.</p>
                    </div>

                    <form class="space-y-4 px-6 py-4" @submit.prevent="submit">

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="nik">NIK</Label>
                                <Input id="nik" v-model="form.nik" placeholder="Nomor induk karyawan" autofocus />
                                <InputError :message="form.errors.nik" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="name">Nama <span class="text-destructive">*</span></Label>
                                <Input id="name" v-model="form.name" placeholder="Nama lengkap" />
                                <InputError :message="form.errors.name" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="department">Departemen <span class="text-destructive">*</span></Label>
                                <Input id="department" v-model="form.department" placeholder="Contoh: IT Department" />
                                <InputError :message="form.errors.department" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="position">Jabatan <span class="text-destructive">*</span></Label>
                                <select
                                    id="position"
                                    v-model="form.position"
                                    class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                                >
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option v-for="pos in props.positions" :key="pos" :value="pos">{{ pos }}</option>
                                </select>
                                <InputError :message="form.errors.position" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="email" placeholder="email@example.com" />
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="phone">No. HP</Label>
                                <Input id="phone" v-model="form.phone" placeholder="08xx-xxxx-xxxx" />
                                <InputError :message="form.errors.phone" />
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                id="is_active"
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 rounded border-input"
                            />
                            <Label for="is_active" class="cursor-pointer">Status Aktif</Label>
                        </div>

                        <div class="flex items-center justify-end gap-2 border-t pt-4">
                            <Button type="button" variant="outline" as-child>
                                <Link :href="route('employees.index')">Batal</Link>
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
