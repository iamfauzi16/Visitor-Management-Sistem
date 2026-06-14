import type { SharedData } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watchEffect } from 'vue';

const COLOR_MAP: Record<string, { primary: string; foreground: string }> = {
    blue:   { primary: '221.2 83.2% 53.3%',  foreground: '0 0% 98%' },
    green:  { primary: '142.1 76.2% 36.3%',  foreground: '0 0% 98%' },
    red:    { primary: '0 84.2% 60.2%',       foreground: '0 0% 98%' },
    purple: { primary: '262.1 83.3% 57.8%',   foreground: '0 0% 98%' },
    orange: { primary: '24.6 95% 53.1%',      foreground: '0 0% 98%' },
    slate:  { primary: '215.4 16.3% 46.9%',   foreground: '0 0% 98%' },
};

export function useTheme() {
    const page = usePage<SharedData>();

    watchEffect(() => {
        const color = page.props.siteSettings?.primary_color ?? 'blue';
        const vars = COLOR_MAP[color] ?? COLOR_MAP.blue;
        const root = document.documentElement;

        root.style.setProperty('--primary', vars.primary);
        root.style.setProperty('--primary-foreground', vars.foreground);
        root.style.setProperty('--sidebar-primary', vars.primary);
        root.style.setProperty('--sidebar-primary-foreground', vars.foreground);
        root.style.setProperty('--ring', vars.primary);
    });
}
