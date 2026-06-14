<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { LoaderCircle } from 'lucide-vue-next';
import type { BreadcrumbItem, PositionEmployee } from '@/types';

const props = defineProps<{ position: PositionEmployee }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Jabatan', href: route('positions.index') },
    { title: 'Edit', href: route('positions.edit', props.position.id) },
];

const form = useForm({
    name_position: props.position.name_position,
    status: props.position.status,
});

const submit = () => {
    form.patch(route('positions.update', props.position.id));
};
</script>

<template>
    <Head title="Edit Jabatan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">
                <div class="rounded-lg border bg-card shadow-sm">
                    <div class="border-b px-6 py-4">
                        <h2 class="text-lg font-semibold">Edit Jabatan</h2>
                        <p class="text-sm text-muted-foreground">Perbarui nama jabatan karyawan.</p>
                    </div>

                    <form class="space-y-4 px-6 py-4" @submit.prevent="submit">

                        <div class="grid gap-2">
                            <Label for="name_position">Nama Jabatan <span class="text-destructive">*</span></Label>
                            <Input
                                id="name_position"
                                v-model="form.name_position"
                                placeholder="Contoh: IT Manager"
                                autofocus
                            />
                            <InputError :message="form.errors.name_position" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">Status <span class="text-destructive">*</span></Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="h-9 w-full rounded-md border border-input bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div class="flex items-center justify-end gap-2 border-t pt-4">
                            <Button type="button" variant="outline" as-child>
                                <Link :href="route('positions.index')">Batal</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Perbarui
                            </Button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
