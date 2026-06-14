<script setup lang="ts">
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { CheckCircle2, X, XCircle } from 'lucide-vue-next';
import type { SharedData } from '@/types';

const page = usePage<SharedData>();

interface Toast {
    id: number;
    type: 'success' | 'error';
    message: string;
}

const toasts = ref<Toast[]>([]);
let nextId = 0;

const addToast = (type: 'success' | 'error', message: string) => {
    const id = nextId++;
    toasts.value.push({ id, type, message });
    setTimeout(() => dismiss(id), 4000);
};

const dismiss = (id: number) => {
    toasts.value = toasts.value.filter(t => t.id !== id);
};

watch(() => page.props.flash?.success, (val) => {
    if (val) addToast('success', val);
});

watch(() => page.props.flash?.error, (val) => {
    if (val) addToast('error', val);
});
</script>

<template>
    <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 pointer-events-none">
        <TransitionGroup name="toast" tag="div" class="flex flex-col gap-2">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto flex items-start gap-3 rounded-lg border px-4 py-3 text-sm shadow-lg w-80"
                :class="toast.type === 'success'
                    ? 'border-green-200 bg-green-50 text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400'
                    : 'border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400'"
            >
                <CheckCircle2 v-if="toast.type === 'success'" class="mt-0.5 h-4 w-4 shrink-0" />
                <XCircle v-else class="mt-0.5 h-4 w-4 shrink-0" />
                <span class="flex-1 leading-snug">{{ toast.message }}</span>
                <button
                    type="button"
                    class="ml-1 shrink-0 opacity-60 transition-opacity hover:opacity-100"
                    @click="dismiss(toast.id)"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateX(110%);
}
.toast-move {
    transition: transform 0.3s ease;
}
</style>
