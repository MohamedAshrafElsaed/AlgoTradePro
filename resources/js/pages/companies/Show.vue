<script lang="ts" setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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

const formatPrice = (price: string | null) => {
    if (!price) return t('companies.not_available', 'N/A');
    return parseFloat(price).toFixed(2);
};

const formatNumber = (num: string | null) => {
    if (!num) return t('companies.not_available', 'N/A');
    const value = parseFloat(num);
    if (value >= 1000000000) return `$${(value / 1000000000).toFixed(2)}B`;
    if (value >= 1000000) return `$${(value / 1000000).toFixed(2)}M`;
    if (value >= 1000) return `$${(value / 1000).toFixed(2)}K`;
    return `$${value.toFixed(2)}`;
};

const formatPercent = (value: string | null) => {
    if (!value) return t('companies.not_available', 'N/A');
    return `${parseFloat(value).toFixed(2)}%`;
};

const formatDate = (date: string | null) => {
    if (!date) return t('companies.not_available', 'N/A');
    return new Date(date).toLocaleDateString(isRTL() ? 'ar-EG' : 'en-US');
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
    if (!props.company.type) return 'N/A';
    return isRTL() ? props.company.type.name_ar : props.company.type.name_en;
});

const isPositiveChange = computed(() => {
    return parseFloat(props.company.price_change || '0') >= 0;
});

const keyStatistics = computed(() => {
    if (!props.company.statistics) return [];

    return [
        { label: t('companies.market_cap', 'Market Cap'), value: formatNumber(props.company.statistics.market_cap) },
        { label: t('companies.enterprise_value', 'Enterprise Value'), value: formatNumber(props.company.statistics.enterprise_value) },
        { label: t('companies.pe_ratio', 'P/E Ratio'), value: props.company.statistics.pe_ratio || t('companies.not_available', 'N/A') },
        { label: t('companies.forward_pe', 'Forward P/E'), value: props.company.statistics.forward_pe || t('companies.not_available', 'N/A') },
        { label: t('companies.price_to_sales', 'P/S Ratio'), value: props.company.statistics.price_to_sales_ratio || t('companies.not_available', 'N/A') },
        { label: t('companies.price_to_book', 'P/B Ratio'), value: props.company.statistics.price_to_book_ratio || t('companies.not_available', 'N/A') },
        { label: t('companies.dividend_yield', 'Dividend Yield'), value: formatPercent(props.company.statistics.dividend_yield) },
        { label: t('companies.beta', 'Beta'), value: props.company.statistics.beta || t('companies.not_available', 'N/A') },
        { label: t('companies.week_52_high', '52W High'), value: formatPrice(props.company.statistics.week_52_high) },
        { label: t('companies.week_52_low', '52W Low'), value: formatPrice(props.company.statistics.week_52_low) },
        { label: t('companies.profit_margin', 'Profit Margin'), value: formatPercent(props.company.statistics.profit_margin) },
        { label: t('companies.operating_margin', 'Operating Margin'), value: formatPercent(props.company.statistics.operating_margin) },
        { label: t('companies.return_on_assets', 'ROA'), value: formatPercent(props.company.statistics.return_on_assets) },
        { label: t('companies.return_on_equity', 'ROE'), value: formatPercent(props.company.statistics.return_on_equity) },
        { label: t('companies.revenue', 'Revenue'), value: formatNumber(props.company.statistics.revenue) },
        { label: t('companies.eps', 'EPS'), value: props.company.statistics.eps || t('companies.not_available', 'N/A') },
    ];
});

const getRecommendationBadgeVariant = (rec: string) => {
    if (rec === 'STRONG_BUY' || rec === 'BUY') return 'default';
    if (rec === 'HOLD') return 'secondary';
    return 'destructive';
};

const getRecommendationLabel = (rec: string) => {
    const labels: Record<string, string> = {
        'STRONG_BUY': t('companies.strong_buy', 'Strong Buy'),
        'BUY': t('companies.buy', 'Buy'),
        'HOLD': t('companies.hold', 'Hold'),
        'SELL': t('companies.sell', 'Sell'),
        'STRONG_SELL': t('companies.strong_sell', 'Strong Sell'),
    };
    return labels[rec] || rec;
};

const getNewsTitle = (news: any) => {
    return isRTL() ? news.title_ar : news.title_en;
};
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
                                <Badge :variant="company.type?.slug === 'stock' ? 'default' : 'secondary'">
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
                                            {{ parseFloat(company.price_change || '0') >= 0 ? '+' : '' }}{{ parseFloat(company.price_change || '0').toFixed(2) }}
                                            ({{ company.change_percentage || '0.00' }}%)
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

            <!-- Tabs for Different Data Sections -->
            <Tabs default-value="overview" class="w-full">
                <TabsList class="grid w-full grid-cols-4 lg:grid-cols-8">
                    <TabsTrigger value="overview">{{ t('companies.overview', 'Overview') }}</TabsTrigger>
                    <TabsTrigger value="financials">{{ t('companies.financials', 'Financials') }}</TabsTrigger>
                    <TabsTrigger value="technical">{{ t('companies.technical', 'Technical') }}</TabsTrigger>
                    <TabsTrigger value="recommendations">{{ t('companies.recommendations', 'Recommendations') }}</TabsTrigger>
                    <TabsTrigger value="news">{{ t('companies.news', 'News') }}</TabsTrigger>
                    <TabsTrigger value="earnings">{{ t('companies.earnings', 'Earnings') }}</TabsTrigger>
                    <TabsTrigger value="dividends">{{ t('companies.dividends', 'Dividends') }}</TabsTrigger>
                    <TabsTrigger value="history">{{ t('companies.history', 'History') }}</TabsTrigger>
                </TabsList>

                <!-- Overview Tab -->
                <TabsContent value="overview" class="space-y-6">
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

                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div v-if="company.ceo">
                                    <div class="text-sm font-medium">{{ t('companies.ceo', 'CEO') }}</div>
                                    <div class="text-muted-foreground">{{ company.ceo }}</div>
                                </div>
                                <div v-if="getHeadquarter">
                                    <div class="text-sm font-medium">{{ t('companies.headquarter', 'Headquarters') }}</div>
                                    <div class="text-muted-foreground">{{ getHeadquarter }}</div>
                                </div>
                                <div v-if="company.exchange">
                                    <div class="text-sm font-medium">{{ t('companies.exchange', 'Exchange') }}</div>
                                    <div class="text-muted-foreground">{{ company.exchange }}</div>
                                </div>
                                <div v-if="company.country">
                                    <div class="text-sm font-medium">{{ t('companies.country', 'Country') }}</div>
                                    <div class="text-muted-foreground">{{ company.country }}</div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Key Statistics -->
                    <div>
                        <h2 :class="isRTL() ? 'text-right' : 'text-left'" class="mb-4 text-2xl font-semibold">
                            {{ t('companies.statistics', 'Key Statistics') }}
                        </h2>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <Card v-for="(stat, index) in keyStatistics" :key="index">
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
                </TabsContent>

                <!-- Other tabs would go here - keeping them minimal for now -->
                <TabsContent value="financials">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('companies.financials', 'Financial Data') }}</CardTitle>
                            <CardDescription>{{ t('companies.coming_soon', 'Coming soon') }}</CardDescription>
                        </CardHeader>
                    </Card>
                </TabsContent>

                <TabsContent value="technical">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('companies.technical', 'Technical Analysis') }}</CardTitle>
                            <CardDescription>{{ t('companies.coming_soon', 'Coming soon') }}</CardDescription>
                        </CardHeader>
                    </Card>
                </TabsContent>

                <TabsContent value="recommendations">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('companies.recommendations', 'Analyst Recommendations') }}</CardTitle>
                            <CardDescription>{{ t('companies.coming_soon', 'Coming soon') }}</CardDescription>
                        </CardHeader>
                    </Card>
                </TabsContent>

                <TabsContent value="news">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('companies.news', 'News & Updates') }}</CardTitle>
                            <CardDescription>{{ t('companies.coming_soon', 'Coming soon') }}</CardDescription>
                        </CardHeader>
                    </Card>
                </TabsContent>

                <TabsContent value="earnings">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('companies.earnings', 'Earnings History') }}</CardTitle>
                            <CardDescription>{{ t('companies.coming_soon', 'Coming soon') }}</CardDescription>
                        </CardHeader>
                    </Card>
                </TabsContent>

                <TabsContent value="dividends">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('companies.dividends', 'Dividend History') }}</CardTitle>
                            <CardDescription>{{ t('companies.coming_soon', 'Coming soon') }}</CardDescription>
                        </CardHeader>
                    </Card>
                </TabsContent>

                <TabsContent value="history">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('companies.history', 'Price History') }}</CardTitle>
                            <CardDescription>{{ t('companies.coming_soon', 'Coming soon') }}</CardDescription>
                        </CardHeader>
                    </Card>
                </TabsContent>
            </Tabs>
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
                        <Label for="recommendations" class="flex-1 cursor-pointer">
                            {{ t('companies.notify_recommendations', 'Recommendations') }}
                        </Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="updates"
                            v-model:checked="notificationPrefs.notify_updates"
                        />
                        <Label for="updates" class="flex-1 cursor-pointer">
                            {{ t('companies.notify_updates', 'Company Updates') }}
                        </Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="news"
                            v-model:checked="notificationPrefs.notify_news"
                        />
                        <Label for="news" class="flex-1 cursor-pointer">
                            {{ t('companies.notify_news', 'News') }}
                        </Label>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="price_alerts"
                            v-model:checked="notificationPrefs.notify_price_alerts"
                        />
                        <Label for="price_alerts" class="flex-1 cursor-pointer">
                            {{ t('companies.notify_price_alerts', 'Price Alerts') }}
                        </Label>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
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
