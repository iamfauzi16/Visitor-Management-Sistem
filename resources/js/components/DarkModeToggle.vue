<script setup lang="ts">
import { useAppearance } from '@/composables/useAppearance';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

const { appearance, updateAppearance } = useAppearance();

const currentIcon = computed(() => {
    if (appearance.value === 'dark') return Moon;
    if (appearance.value === 'light') return Sun;
    return Monitor;
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="h-8 w-8">
                <component :is="currentIcon" class="h-4 w-4" />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem @click="updateAppearance('light')">
                <Sun class="mr-2 h-4 w-4" />
                Light
            </DropdownMenuItem>
            <DropdownMenuItem @click="updateAppearance('dark')">
                <Moon class="mr-2 h-4 w-4" />
                Dark
            </DropdownMenuItem>
            <DropdownMenuItem @click="updateAppearance('system')">
                <Monitor class="mr-2 h-4 w-4" />
                System
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
