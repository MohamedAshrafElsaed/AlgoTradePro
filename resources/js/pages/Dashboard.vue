<script lang="ts" setup>
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { useTranslation } from '@/composables/useTranslation';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { TrendingUp, TrendingDown, DollarSign, BarChart, AlertCircle, Target } from 'lucide-vue-next';
import { computed } from 'vue';

const { t, isRTL } = useTranslation();
const page = usePage();
const user = computed(() => page.props.auth?.user);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('dashboard.title', 'Dashboard'),
        href: dashboard().url,
    },
];

const stats = [
    {
        label: t('dashboard.stats.portfolio_value', 'Portfolio Value'),
        value: '$0.00',
        change: '+0%',
        trend: 'up',
        icon: DollarSign,
    },
    {
        label: t('dashboard.stats.active_positions', 'Active Positions'),
        value: '0',
        change: '0',
        trend: 'neutral',
        icon: Target,
    },
    {
        label: t('dashboard.stats.total_return', 'Total Return'),
        value: '+0%',
        change: '$0.00',
        trend: 'up',
        icon: TrendingUp,
    },
];

const quickActions = [
    {
        title: t('dashboard.actions.view_signals', 'View Trading Signals'),
        description: t('dashboard.actions.view_signals_desc', 'See latest AI recommendations'),
        icon: AlertCircle,
        href: '#',
    },
    {
        title: t('dashboard.actions.portfolio', 'Manage Portfolio'),
        description: t('dashboard.actions.portfolio_desc', 'Track your positions'),
        icon: BarChart,
        href: '#',
    },
    {
        title: t('dashboard.actions.market_analysis', 'Market Analysis'),
        description: t('dashboard.actions.market_analysis_desc', 'View market insights'),
        icon: TrendingUp,
        href: '#',
    },
];

const recentSignals = [
    {
        stock: 'AAPL',
        action: 'BUY',
        price: '$175.50',
        confidence: 85,
    },
    {
        stock: 'TSLA',
        action: 'SELL',
        price: '$245.30',
        confidence: 78,
    },
    {
        stock: 'MSFT',
        action: 'BUY',
        price: '$380.25',
        confidence: 92,
    },
];
</script>

<template>
    <Head :title="t('dashboard.title', 'Dashboard')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div :class="isRTL() ? 'text-right' : 'text-left'" class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
            <!-- Welcome Section -->
            <div class="space-y-2">
                <h1 class="text-3xl font-bold tracking-tight">
                    {{ t('dashboard.welcome', 'Welcome Back') }}{{ user?.name ? `, ${user.name}` : '' }}!
                </h1>
                <p class="text-muted-foreground">
                    {{ t('dashboard.subtitle', "Here's your trading overview for today") }}
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card v-for="(stat, index) in stats" :key="index">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">{{ stat.label }}</CardTitle>
                        <component :is="stat.icon" class="size-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stat.value }}</div>
                        <p class="text-xs text-muted-foreground">
                            <span :class="stat.trend === 'up' ? 'text-green-600' : stat.trend === 'down' ? 'text-red-600' : ''">
                                {{ stat.change }}
                            </span>
                            {{ t('dashboard.stats.from_yesterday', 'from yesterday') }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Quick Actions -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('dashboard.quick_actions', 'Quick Actions') }}</CardTitle>
                        <CardDescription>{{ t('dashboard.quick_actions_desc', 'Access key trading features') }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <Link
                                v-for="(action, index) in quickActions"
                                :key="index"
                                :href="action.href"
                                class="flex items-center gap-4 rounded-lg border p-4 transition-all hover:border-primary hover:bg-accent"
                            >
                                <div class="flex size-10 items-center justify-center rounded-full bg-primary/10">
                                    <component :is="action.icon" class="size-5 text-primary" />
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-medium">{{ action.title }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ action.description }}</p>
                                </div>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Trading Signals -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('dashboard.recent_signals', 'Recent Trading Signals') }}</CardTitle>
                        <CardDescription>{{ t('dashboard.recent_signals_desc', 'Latest AI-powered recommendations') }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="(signal, index) in recentSignals" :key="index" class="flex items-center justify-between rounded-lg border p-4">
                                <div class="flex items-center gap-3">
                                    <div class="font-bold">{{ signal.stock }}</div>
                                    <Badge :variant="signal.action === 'BUY' ? 'default' : 'destructive'">
                                        {{ signal.action }}
                                    </Badge>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">{{ signal.price }}</div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ t('dashboard.confidence', 'Confidence') }}: {{ signal.confidence }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Getting Started Guide -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('dashboard.getting_started', 'Getting Started') }}</CardTitle>
                    <CardDescription>{{ t('dashboard.getting_started_desc', 'Follow these steps to start trading') }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 rounded-lg p-3">
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">1</div>
                            <div>
                                <h3 class="font-medium">{{ t('dashboard.step1', 'Set Up Your Profile') }}</h3>
                                <p class="text-sm text-muted-foreground">{{ t('dashboard.step1_desc', 'Complete your trading preferences') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-lg p-3">
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">2</div>
                            <div>
                                <h3 class="font-medium">{{ t('dashboard.step2', 'Connect Your Broker') }}</h3>
                                <p class="text-sm text-muted-foreground">{{ t('dashboard.step2_desc', 'Link your trading account') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-lg p-3">
                            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">3</div>
                            <div>
                                <h3 class="font-medium">{{ t('dashboard.step3', 'Start Receiving Signals') }}</h3>
                                <p class="text-sm text-muted-foreground">{{ t('dashboard.step3_desc', 'Get real-time trading recommendations') }}</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
