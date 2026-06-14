<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="grid min-h-screen lg:grid-cols-2">
        <!-- Left: Visual Panel -->
        <div class="relative hidden overflow-hidden bg-primary lg:flex">
            <!-- Background gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary/90 to-blue-800" />

            <!-- Decorative circles -->
            <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-white/5" />
            <div class="absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-white/5" />
            <div class="absolute right-16 top-1/2 h-48 w-48 -translate-y-1/2 rounded-full bg-white/5" />

            <!-- Grid pattern -->
            <div
                class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px"
            />

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-between p-12 text-white">
                <!-- Top: branding -->
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20 backdrop-blur">
                        <AppLogoIcon class="size-6 fill-current text-white" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white/80">Sistem Manajemen</p>
                        <p class="text-base font-bold">Visitor Management System</p>
                    </div>
                </div>

                <!-- Center: headline -->
                <div class="space-y-6">
                    <h2 class="text-4xl font-bold leading-tight">
                        Kelola Tamu &amp;<br />
                        Karyawan dengan<br />
                        <span class="text-white/70">Mudah &amp; Efisien</span>
                    </h2>
                    <p class="max-w-xs text-sm leading-relaxed text-white/70">
                        Platform terpadu untuk manajemen pengunjung, kehadiran karyawan, dan pelaporan realtime.
                    </p>

                    <!-- Feature list -->
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Manajemen pengunjung &amp; check-in digital
                        </li>
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Pencatatan kehadiran karyawan
                        </li>
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Laporan &amp; analitik realtime
                        </li>
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Kontrol akses berbasis peran
                        </li>
                    </ul>
                </div>

                <!-- Bottom: tagline -->
                <p class="text-xs text-white/40">&copy; {{ new Date().getFullYear() }} VMS &mdash; Visitor Management System</p>
            </div>
        </div>

        <!-- Right: Form Panel -->
        <div class="flex flex-col items-center justify-center bg-background px-6 py-12 sm:px-10 lg:px-16">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <Link :href="route('home')" class="mb-8 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary">
                        <AppLogoIcon class="size-6 fill-current text-primary-foreground" />
                    </div>
                    <span class="text-lg font-semibold tracking-tight">VMS</span>
                </Link>

                <!-- Title & Description -->
                <div class="mb-8 space-y-1">
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground">{{ description }}</p>
                </div>

                <!-- Slot: form -->
                <slot />
            </div>
        </div>
    </div>
</template>
