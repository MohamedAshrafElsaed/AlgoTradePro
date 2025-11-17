<script lang="ts" setup>
import LanguageToggle from '@/components/LanguageToggle.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useTranslation } from '@/composables/useTranslation';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Combine, LayoutGrid } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

interface Props {
    side?: 'left' | 'right';
}

withDefaults(defineProps<Props>(), {
    side: 'left',
});

const { t } = useTranslation();
const page = usePage();
const locale = computed(() => page.props.locale || 'en');

const mainNavItems: NavItem[] = [
    {
        title: t('nav.dashboard', 'Dashboard'),
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: t('nav.dashboard', 'Companies'),
        href: 'companies',
        icon: Combine,
    },
];

const footerNavItems: NavItem[] = [];
</script>
<template>
    <Sidebar :side="side" collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child size="lg">
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- Language Toggle -->
            <div class="px-3 py-2">
                <LanguageToggle />
            </div>

            <NavFooter v-if="footerNavItems.length" :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
