<script setup lang="ts">
import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from './AppLogoIcon.vue';

const page = usePage<SharedData>();
const siteName = computed(() => page.props.siteSettings?.site_name ?? 'VMS');
const logoUrl = computed(() =>
    page.props.siteSettings?.logo_path
        ? `/storage/${page.props.siteSettings.logo_path}`
        : null,
);
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground overflow-hidden">
        <img v-if="logoUrl" :src="logoUrl" :alt="siteName" class="size-8 object-cover" />
        <AppLogoIcon v-else class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate font-semibold leading-none">{{ siteName }}</span>
    </div>
</template>
