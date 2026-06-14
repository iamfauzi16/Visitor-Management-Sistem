<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle, Trash2, Upload } from 'lucide-vue-next';

import type { BreadcrumbItem, SiteSettings } from '@/types';

interface Props {
    settings: SiteSettings;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Pengaturan Situs', href: '/settings/site' },
];

const COLOR_OPTIONS = [
    { value: 'blue',   label: 'Biru',   tw: 'bg-blue-500' },
    { value: 'green',  label: 'Hijau',  tw: 'bg-green-600' },
    { value: 'red',    label: 'Merah',  tw: 'bg-red-500' },
    { value: 'purple', label: 'Ungu',   tw: 'bg-purple-600' },
    { value: 'orange', label: 'Oranye', tw: 'bg-orange-500' },
    { value: 'slate',  label: 'Abu-abu', tw: 'bg-slate-500' },
];

const form = useForm({
    _method: 'PATCH',
    site_name:     props.settings.site_name     ?? '',
    primary_color: props.settings.primary_color ?? 'blue',
    logo:          null as File | null,
    remove_logo:   false,
});

const logoPreview = ref<string | null>(
    props.settings.logo_path ? `/storage/${props.settings.logo_path}` : null,
);

function onLogoChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.logo = file;
    form.remove_logo = false;
    logoPreview.value = URL.createObjectURL(file);
}

function removeLogo() {
    form.logo = null;
    form.remove_logo = true;
    logoPreview.value = null;
}

function submit() {
    form.post(route('site-settings.update'), { forceFormData: true });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Pengaturan Situs" />

        <div class="px-4 py-6">
            <HeadingSmall
                title="Pengaturan Situs"
                description="Konfigurasi nama, logo, dan warna tema dashboard"
            />

            <form class="mt-6 max-w-xl space-y-8" @submit.prevent="submit">
                <!-- Nama Situs -->
                <div class="space-y-2">
                    <Label for="site_name">Nama Dashboard</Label>
                    <Input
                        id="site_name"
                        v-model="form.site_name"
                        type="text"
                        placeholder="VMS — Visitor Management System"
                    />
                    <p v-if="form.errors.site_name" class="text-sm text-destructive">
                        {{ form.errors.site_name }}
                    </p>
                </div>

                <!-- Warna Tema -->
                <div class="space-y-3">
                    <Label>Warna Tema</Label>
                    <div class="flex flex-wrap gap-3">
                        <button
                            v-for="opt in COLOR_OPTIONS"
                            :key="opt.value"
                            type="button"
                            :title="opt.label"
                            :class="[
                                'flex h-10 w-10 items-center justify-center rounded-full border-2 transition-all',
                                opt.tw,
                                form.primary_color === opt.value
                                    ? 'border-foreground scale-110 shadow-md'
                                    : 'border-transparent opacity-70 hover:opacity-100',
                            ]"
                            @click="form.primary_color = opt.value"
                        >
                            <span v-if="form.primary_color === opt.value" class="block h-3 w-3 rounded-full bg-white/80" />
                        </button>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        Dipilih: <span class="font-medium capitalize">{{ form.primary_color }}</span>
                    </p>
                    <p v-if="form.errors.primary_color" class="text-sm text-destructive">
                        {{ form.errors.primary_color }}
                    </p>
                </div>

                <!-- Logo -->
                <div class="space-y-3">
                    <Label>Logo</Label>

                    <div v-if="logoPreview" class="flex items-center gap-4">
                        <img
                            :src="logoPreview"
                            alt="Logo preview"
                            class="h-16 w-16 rounded-lg border object-contain p-1"
                        />
                        <Button type="button" variant="destructive" size="sm" @click="removeLogo">
                            <Trash2 class="mr-1.5 h-4 w-4" />
                            Hapus Logo
                        </Button>
                    </div>

                    <label
                        class="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed px-4 py-3 text-sm text-muted-foreground transition-colors hover:border-primary hover:text-primary"
                    >
                        <Upload class="h-4 w-4" />
                        {{ logoPreview ? 'Ganti logo' : 'Upload logo' }}
                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/svg+xml,image/webp"
                            class="hidden"
                            @change="onLogoChange"
                        />
                    </label>
                    <p class="text-xs text-muted-foreground">PNG, JPG, SVG, WebP — maks 2 MB</p>
                    <p v-if="form.errors.logo" class="text-sm text-destructive">{{ form.errors.logo }}</p>
                </div>

                <!-- Submit -->
                <Button type="submit" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                    Simpan Pengaturan
                </Button>
            </form>
        </div>
    </AppLayout>
</template>
