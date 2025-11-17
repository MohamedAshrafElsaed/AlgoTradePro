<script lang="ts" setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useTranslation } from '@/composables/useTranslation';
import { router, Head, Link } from '@inertiajs/vue3';
import { Heart, TrendingDown, TrendingUp, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { show as companyShow } from '@/routes/companies';
import { store as addFavorite, destroy as removeFavorite } from '@/routes/companies/favorite';

defineOptions({ layout: AppSidebarLayout });

interface CompanyType {
    id: number;
    name_en: string;
    name_ar: string;
    slug: string;
}

interface Company {
    id: number;
    symbol: string;
    name_en: string;
    name_ar: string;
    current_price: string | null;
    price_change: string | null;
    change_percentage: string | null;
    is_favorited: boolean;
    type?: CompanyType;
}

interface PaginatedCompanies {
    data: Company[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

interface Props {
    companies: PaginatedCompanies;
    types: CompanyType[];
    filters: {
        search?: string;
        type?: number;
    };
}

const props = defineProps<Props>();
const { t, isRTL } = useTranslation();

const searchQuery = ref(props.filters.search || '');
const selectedType = ref(props.filters.type?.toString() || 'all');

// Debounced search
let searchTimeout: NodeJS.Timeout;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/companies', {
            search: newValue || undefined,
            type: selectedType.value !== 'all' ? selectedType.value : undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }, 300);
});

watch(selectedType, (newValue) => {
    router.get('/companies', {
        search: searchQuery.value || undefined,
        type: newValue !== 'all' ? newValue : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
});

const toggleFavorite = (company: Company) => {
    const route = company.is_favorited
        ? removeFavorite({ company: company.id })
        : addFavorite({ company: company.id });

    router.method(company.is_favorited ? 'delete' : 'post', route, {}, {
        preserveScroll: true,
        preserveState: true,
    });
};

const formatPrice = (price: string | null) => {
    if (!price) return t('companies.not_available', 'N/A');
    return parseFloat(price).toFixed(2);
};

const getCompanyName = (company: Company) => {
    return isRTL() ? company.name_ar : company.name_en;
};

const getTypeName = (type: CompanyType | undefined) => {
    if (!type) return 'N/A';
    return isRTL() ? type.name_ar : type.name_en;
};

const isPositiveChange = (priceChange: string | null) => {
    return parseFloat(priceChange || '0') >= 0;
};
</script>

<template>
    <div>
        <Head :title="t('companies.title', 'Companies')" />

        <div class="container mx-auto space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div :class="isRTL() ? 'text-right' : 'text-left'">
                <h1 class="text-3xl font-bold">{{ t('companies.title', 'Companies') }}</h1>
                <p class="mt-2 text-muted-foreground">
                    {{ t('companies.subtitle', 'Browse stocks and mutual funds with real-time data') }}
                </p>
            </div>

            <!-- Filters -->
            <Card>
                <CardContent class="p-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <Search
                                :class="[
                                    'absolute top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground',
                                    isRTL() ? 'right-3' : 'left-3'
                                ]"
                            />
                            <Input
                                v-model="searchQuery"
                                :placeholder="t('companies.search_placeholder', 'Search by symbol or name...')"
                                :class="isRTL() ? 'pr-10' : 'pl-10'"
                            />
                        </div>

                        <!-- Type Filter -->
                        <div class="w-full md:w-64">
                            <Select v-model="selectedType">
                                <SelectTrigger>
                                    <SelectValue :placeholder="t('companies.all_types', 'All Types')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">
                                        {{ t('companies.all_types', 'All Types') }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="type in types"
                                        :key="type.id"
                                        :value="type.id.toString()"
                                    >
                                        {{ getTypeName(type) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Desktop Table View -->
            <Card class="hidden md:block">
                <div class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                    {{ t('companies.symbol', 'Symbol') }}
                                </TableHead>
                                <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                    {{ t('companies.name', 'Name') }}
                                </TableHead>
                                <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                    {{ t('companies.type', 'Type') }}
                                </TableHead>
                                <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                    {{ t('companies.price', 'Price') }}
                                </TableHead>
                                <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                    {{ t('companies.change', 'Change') }}
                                </TableHead>
                                <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                    {{ t('companies.change_percent', 'Change %') }}
                                </TableHead>
                                <TableHead class="text-center">
                                    {{ t('companies.actions', 'Actions') }}
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="company in companies.data"
                                :key="company.id"
                                class="cursor-pointer hover:bg-muted/50"
                                @click="router.visit(companyShow({ company: company.id }))"
                            >
                                <TableCell :class="isRTL() ? 'text-right' : 'text-left'" class="font-bold">
                                    {{ company.symbol }}
                                </TableCell>
                                <TableCell :class="isRTL() ? 'text-right' : 'text-left'">
                                    {{ getCompanyName(company) }}
                                </TableCell>
                                <TableCell :class="isRTL() ? 'text-right' : 'text-left'">
                                    <Badge
                                        v-if="company.type"
                                        :variant="company.type.slug === 'stock' ? 'default' : 'secondary'"
                                    >
                                        {{ getTypeName(company.type) }}
                                    </Badge>
                                    <span v-else>N/A</span>
                                </TableCell>
                                <TableCell :class="isRTL() ? 'text-right' : 'text-left'" class="font-mono font-semibold">
                                    ${{ formatPrice(company.current_price) }}
                                </TableCell>
                                <TableCell
                                    :class="[
                                        isRTL() ? 'text-right' : 'text-left',
                                        'font-semibold',
                                        isPositiveChange(company.price_change) ? 'text-green-600' : 'text-red-600'
                                    ]"
                                >
                                    <div class="flex items-center gap-1" :class="isRTL() ? 'justify-end' : 'justify-start'">
                                        <TrendingUp v-if="isPositiveChange(company.price_change)" class="h-4 w-4" />
                                        <TrendingDown v-else class="h-4 w-4" />
                                        <span>
                                            {{ parseFloat(company.price_change || '0') >= 0 ? '+' : '' }}{{ parseFloat(company.price_change || '0').toFixed(2) }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell
                                    :class="[
                                        isRTL() ? 'text-right' : 'text-left',
                                        'font-semibold',
                                        isPositiveChange(company.price_change) ? 'text-green-600' : 'text-red-600'
                                    ]"
                                >
                                    {{ parseFloat(company.price_change || '0') >= 0 ? '+' : '' }}{{ company.change_percentage || '0.00' }}%
                                </TableCell>
                                <TableCell class="text-center">
                                    <Button
                                        :variant="company.is_favorited ? 'default' : 'ghost'"
                                        size="icon"
                                        class="h-8 w-8"
                                        @click.stop="toggleFavorite(company)"
                                    >
                                        <Heart
                                            :class="company.is_favorited ? 'fill-current' : ''"
                                            class="h-4 w-4"
                                        />
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </Card>

            <!-- Mobile Card View -->
            <div class="space-y-4 md:hidden">
                <Card
                    v-for="company in companies.data"
                    :key="company.id"
                    class="cursor-pointer transition-shadow hover:shadow-lg"
                    @click="router.visit(companyShow({ company: company.id }))"
                >
                    <CardContent class="p-4">
                        <div class="flex items-start justify-between">
                            <div :class="isRTL() ? 'text-right' : 'text-left'" class="flex-1">
                                <div class="flex items-center gap-2" :class="isRTL() ? 'flex-row-reverse' : ''">
                                    <h3 class="text-lg font-bold">{{ company.symbol }}</h3>
                                    <Badge
                                        v-if="company.type"
                                        :variant="company.type.slug === 'stock' ? 'default' : 'secondary'"
                                        class="text-xs"
                                    >
                                        {{ getTypeName(company.type) }}
                                    </Badge>
                                </div>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ getCompanyName(company) }}
                                </p>
                                <div class="mt-3 space-y-1">
                                    <div class="text-2xl font-bold">
                                        ${{ formatPrice(company.current_price) }}
                                    </div>
                                    <div
                                        v-if="company.price_change"
                                        :class="[
                                            'flex items-center gap-1 text-sm font-semibold',
                                            isPositiveChange(company.price_change) ? 'text-green-600' : 'text-red-600',
                                            isRTL() ? 'justify-end' : 'justify-start'
                                        ]"
                                    >
                                        <TrendingUp v-if="isPositiveChange(company.price_change)" class="h-4 w-4" />
                                        <TrendingDown v-else class="h-4 w-4" />
                                        <span>
                                            {{ parseFloat(company.price_change || '0') >= 0 ? '+' : '' }}{{ parseFloat(company.price_change || '0').toFixed(2) }}
                                            ({{ company.change_percentage || '0.00' }}%)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <Button
                                :variant="company.is_favorited ? 'default' : 'ghost'"
                                size="icon"
                                class="h-8 w-8"
                                @click.stop="toggleFavorite(company)"
                            >
                                <Heart
                                    :class="company.is_favorited ? 'fill-current' : ''"
                                    class="h-4 w-4"
                                />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Empty State -->
            <div
                v-if="companies.data.length === 0"
                class="flex min-h-[400px] flex-col items-center justify-center rounded-lg border-2 border-dashed p-12 text-center"
            >
                <div class="mx-auto max-w-md space-y-3">
                    <h3 class="text-lg font-semibold">
                        {{ t('companies.no_results', 'No companies found') }}
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        {{ t('companies.no_results_description', 'Try adjusting your search or filters to find what you\'re looking for.') }}
                    </p>
                    <Button
                        v-if="searchQuery || selectedType !== 'all'"
                        @click="() => { searchQuery = ''; selectedType = 'all'; }"
                    >
                        {{ t('companies.clear_filters', 'Clear Filters') }}
                    </Button>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="companies.data.length > 0 && companies.last_page > 1"
                class="flex items-center justify-center gap-2"
            >
                <Button
                    v-for="(link, index) in companies.links"
                    :key="index"
                    :variant="link.active ? 'default' : 'outline'"
                    :disabled="!link.url"
                    size="sm"
                    @click="link.url && router.visit(link.url)"
                    v-html="link.label"
                />
            </div>

            <!-- Stats Footer -->
            <div class="text-center text-sm text-muted-foreground">
                {{ t('companies.showing', 'Showing') }}
                {{ companies.data.length }}
                {{ t('companies.of', 'of') }}
                {{ companies.total }}
                {{ t('companies.results', 'companies') }}
            </div>
        </div>
    </div>
</template>
