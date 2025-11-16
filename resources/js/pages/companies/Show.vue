<script lang="ts" setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { useTranslation } from '@/composables/useTranslation';
import type { Company, CompanySubscription } from '@/types';
import { router, Head, Link } from '@inertiajs/vue3';
import { Bell, ExternalLink, Heart, TrendingDown, TrendingUp } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { show as companyShow } from '@/routes/companies';
import { store as addFavorite, destroy as removeFavorite } from '@/routes/companies/favorite';
import { store as addSubscription, destroy as removeSubscription } from '@/routes/companies/subscribe';

defineOptions({ layout: AppSidebarLayout });

interface Props {
    company: Company;
    related_companies: Array<{
        id: number;
        symbol: string;
        name_en: string;
        name_ar: string;
        current_price: string;
    }>;
    subscription: CompanySubscription | null;
}

const props = defineProps<Props>();
const { t, isRTL } = useTranslation();

const showSubscriptionDialog = ref(false);
const notificationPrefs = ref({
    notify_recommendations: props.subscription?.notify_recommendations ?? true,
    notify_updates: props.subscription?.notify_updates ?? true,
    notify_news: props.subscription?.notify_news ?? false,
    notify_price_alerts: props.subscription?.notify_price_alerts ?? false,
});

const selectedChartPeriod = ref('1M');
const chartPeriods = ['1D', '1W', '1M', '6M', '1Y', '5Y'];

const toggleFavorite = () => {
    const route = props.company.is_favorited
        ? removeFavorite({ company: props.company.id })
        : addFavorite({ company: props.company.id });

    router.method(props.company.is_favorited ? 'delete' : 'post', route, {}, { preserveScroll: true });
};

const saveSubscription = () => {
    router.post(
        addSubscription({ company: props.company.id }),
        notificationPrefs.value,
        {
            preserveScroll: true,
            onSuccess: () => {
                showSubscriptionDialog.value = false;
            },
        }
    );
};

const unsubscribe = () => {
    router.delete(
        removeSubscription({ company: props.company.id }),
        {
            preserveScroll: true,
            onSuccess: () => {
                showSubscriptionDialog.value = false;
            },
        }
    );
};

const formatPrice = (price: string) => {
    return parseFloat(price).toFixed(5);
};

const formatNumber = (num: string | null) => {
    if (!num) return t('companies.not_available', 'N/A');
    const value = parseFloat(num);
    if (value >= 1000000000) return `$${(value / 1000000000).toFixed(2)}B`;
    if (value >= 1000000) return `$${(value / 1000000).toFixed(2)}M`;
    if (value >= 1000) return `$${(value / 1000).toFixed(2)}K`;
    return `$${value.toFixed(2)}`;
};

const getCompanyName = computed(() => {
    return isRTL() ? props.company.name_ar : props.company.name_en;
});

const getDescription = computed(() => {
    return isRTL() ? props.company.description_ar : props.company.description_en;
});

const getHeadquarter = computed(() => {
    return isRTL() ? props.company.headquarter_ar : props.company.headquarter_en;
});

const getTypeName = computed(() => {
    return isRTL() ? props.company.type.name_ar : props.company.type.name_en;
});

const isPositiveChange = computed(() => {
    return parseFloat(props.company.price_change) >= 0;
});

const statistics = computed(() => {
    if (!props.company.statistics) return [];

    return [
        { label: t('companies.market_cap', 'Market Cap'), value: formatNumber(props.company.statistics.market_cap) },
        { label: t('companies.value_today', 'Value Today'), value: formatNumber(props.company.statistics.value_today) },
        { label: t('companies.adtv_6m', 'ADTV (6M)'), value: formatNumber(props.company.statistics.adtv_6m) },
        { label: t('companies.eps', 'EPS'), value: props.company.statistics.eps || t('companies.not_available', 'N/A') },
        { label: t('companies.pe_ratio', 'P/E Ratio'), value: props.company.statistics.pe_ratio || t('companies.not_available', 'N/A') },
        { label: t('companies.dividend_yield', 'Dividend Yield'), value: props.company.statistics.dividend_yield ? `${props.company.statistics.dividend_yield}%` : t('companies.not_available', 'N/A') },
        { label: t('companies.week_52_high', '52W High'), value: props.company.statistics.week_52_high ? `$${parseFloat(props.company.statistics.week_52_high).toFixed(5)}` : t('companies.not_available', 'N/A') },
        { label: t('companies.week_52_low', '52W Low'), value: props.company.statistics.week_52_low ? `$${parseFloat(props.company.statistics.week_52_low).toFixed(5)}` : t('companies.not_available', 'N/A') },
    ];
});
</script>

<template>
    <div>
        <Head :title="`${company.symbol} - ${getCompanyName}`" />

        <div class="container mx-auto space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <Card>
                <CardContent class="p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="flex-1 space-y-2">
                            <div class="flex items-center gap-3">
                                <h1 :class="isRTL() ? 'text-right' : 'text-left'" class="text-3xl font-bold">
                                    {{ company.symbol }}
                                </h1>
                                <Badge :variant="company.type.slug === 'stock' ? 'default' : 'secondary'">
                                    {{ getTypeName }}
                                </Badge>
                            </div>
                            <p :class="isRTL() ? 'text-right' : 'text-left'" class="text-lg text-muted-foreground">
                                {{ getCompanyName }}
                            </p>
                            <div class="flex flex-wrap items-center gap-4">
                                <div>
                                    <div class="text-4xl font-bold">
                                        ${{ formatPrice(company.current_price) }}
                                    </div>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span
                                            :class="[
                                                'flex items-center gap-1 font-semibold',
                                                isPositiveChange ? 'text-green-600' : 'text-red-600'
                                            ]"
                                        >
                                            <TrendingUp v-if="isPositiveChange" class="h-4 w-4" />
                                            <TrendingDown v-else class="h-4 w-4" />
                                            {{ parseFloat(company.price_change) >= 0 ? '+' : '' }}{{ parseFloat(company.price_change).toFixed(5) }}
                                            ({{ company.change_percentage }}%)
                                        </span>
                                        <span class="text-muted-foreground">
                                            • {{ t('companies.delay_notice', '15 minutes delay') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <Button
                                :variant="company.is_favorited ? 'default' : 'outline'"
                                size="lg"
                                @click="toggleFavorite"
                            >
                                <Heart
                                    :class="company.is_favorited ? 'fill-current' : ''"
                                    class="mr-2 h-5 w-5"
                                />
                                {{ company.is_favorited ? t('companies.favorited', 'Favorited') : t('companies.add_to_favorites', 'Favorite') }}
                            </Button>

                            <Button size="lg" @click="showSubscriptionDialog = true">
                                <Bell class="mr-2 h-5 w-5" />
                                {{ company.is_subscribed ? t('companies.notification_preferences', 'Notifications') : t('companies.subscribe', 'Subscribe') }}
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Chart Section -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.overview', 'Overview') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Period Selector -->
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="period in chartPeriods"
                            :key="period"
                            :variant="selectedChartPeriod === period ? 'default' : 'outline'"
                            size="sm"
                            @click="selectedChartPeriod = period"
                        >
                            {{ period }}
                        </Button>
                    </div>

                    <!-- Chart Placeholder -->
                    <div class="flex h-64 items-center justify-center rounded-lg border-2 border-dashed bg-muted/20">
                        <p class="text-muted-foreground">
                            {{ t('companies.chart_coming_soon', 'Chart visualization coming soon') }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Statistics Grid -->
            <div>
                <h2 :class="isRTL() ? 'text-right' : 'text-left'" class="mb-4 text-2xl font-semibold">
                    {{ t('companies.statistics', 'Statistics') }}
                </h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="(stat, index) in statistics" :key="index">
                        <CardContent class="p-4">
                            <div :class="isRTL() ? 'text-right' : 'text-left'" class="text-sm text-muted-foreground">
                                {{ stat.label }}
                            </div>
                            <div :class="isRTL() ? 'text-right' : 'text-left'" class="mt-1 text-xl font-semibold">
                                {{ stat.value }}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- About Section -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.about', 'About') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <p :class="isRTL() ? 'text-right' : 'text-left'" class="leading-relaxed text-muted-foreground">
                        {{ getDescription || t('companies.not_available', 'N/A') }}
                    </p>

                    <Separator />

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-if="company.ceo">
                            <div class="text-sm font-medium">{{ t('companies.ceo', 'CEO') }}</div>
                            <div class="text-muted-foreground">{{ company.ceo }}</div>
                        </div>
                        <div v-if="getHeadquarter">
                            <div class="text-sm font-medium">{{ t('companies.headquarter', 'Headquarters') }}</div>
                            <div class="text-muted-foreground">{{ getHeadquarter }}</div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Related Companies -->
            <div v-if="related_companies.length > 0">
                <h2 :class="isRTL() ? 'text-right' : 'text-left'" class="mb-4 text-2xl font-semibold">
                    {{ t('companies.related_companies', 'Related Companies') }}
                </h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="related in related_companies" :key="related.id">
                        <CardContent class="p-4">
                            <Link :href="companyShow({ company: related.id })">
                                <div class="font-semibold">{{ related.symbol }}</div>
                                <div class="text-sm text-muted-foreground">
                                    {{ isRTL() ? related.name_ar : related.name_en }}
                                </div>
                                <div class="mt-2 font-mono text-lg font-semibold">
                                    ${{ formatPrice(related.current_price) }}
                                </div>
                            </Link>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Subscription Dialog -->
        <Dialog v-model:open="showSubscriptionDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ t('companies.notification_preferences', 'Notification Preferences') }}</DialogTitle>
                    <DialogDescription>
                        {{ t('companies.manage_notifications', 'Manage your notification preferences for this company') }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="recommendations"
                            v-model:checked="notificationPrefs.notify_recommendations"
                        />
                        <Label for="recommendations" class="flex-1">
                            {{ t('companies.notify_recommendations', 'Recommendations') }}
                        </Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="updates"
                            v-model:checked="notificationPrefs.notify_updates"
                        />
                        <Label for="updates" class="flex-1">
                            {{ t('companies.notify_updates', 'Company Updates') }}
                        </Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="news"
                            v-model:checked="notificationPrefs.notify_news"
                        />
                        <Label for="news" class="flex-1">
                            {{ t('companies.notify_news', 'News') }}
                        </Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="price_alerts"
                            v-model:checked="notificationPrefs.notify_price_alerts"
                        />
                        <Label for="price_alerts" class="flex-1">
                            {{ t('companies.notify_price_alerts', 'Price Alerts') }}
                        </Label>
                    </div>
                </div>

                <div class="flex gap-2 justify-end">
                    <Button v-if="company.is_subscribed" variant="destructive" @click="unsubscribe">
                        {{ t('companies.unsubscribe', 'Unsubscribe') }}
                    </Button>
                    <Button @click="saveSubscription">
                        {{ t('common.save', 'Save') }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
