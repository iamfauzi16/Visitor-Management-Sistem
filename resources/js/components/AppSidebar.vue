<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavGroup, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BarChart2, Briefcase, ClipboardList, LayoutGrid, Settings2, ShieldCheck, UserCheck, UserPlus, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');
const isHost = computed(() => page.props.auth.user?.role === 'host');

const navGroups = computed<NavGroup[]>(() => {
    const groups: NavGroup[] = [];

    groups.push({
        label: 'Utama',
        items: [
            { title: 'Dashboard', url: route('dashboard'), icon: LayoutGrid },
        ],
    });

    if (isHost.value) {
        groups.push({
            label: 'Tamu',
            items: [
                { title: 'Tamu Saya',      url: route('host.index'),  icon: UserPlus },
                { title: 'Daftarkan Tamu', url: route('host.create'), icon: UserCheck },
            ],
        });
    }

    if (!isHost.value) {
        groups.push({
            label: 'Manajemen Tamu',
            items: [
                { title: 'Registrasi Tamu', url: route('visitors.create'), icon: UserCheck },
                { title: 'Daftar Tamu',     url: route('visitors.index'),  icon: ClipboardList },
            ],
        });
    }

    if (isAdmin.value) {
        groups.push({
            label: 'Manajemen',
            items: [
                { title: 'Karyawan', url: route('employees.index'), icon: Users },
                { title: 'Laporan',  url: route('reports.index'),   icon: BarChart2 },
            ],
        });

        groups.push({
            label: 'Pengaturan',
            items: [
                { title: 'Pengguna',          url: route('users.index'),        icon: ShieldCheck },
                { title: 'Jabatan Karyawan',  url: route('positions.index'),    icon: Briefcase },
                { title: 'Pengaturan Situs',  url: route('site-settings.edit'), icon: Settings2 },
            ],
        });
    }

    return groups;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :groups="navGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
