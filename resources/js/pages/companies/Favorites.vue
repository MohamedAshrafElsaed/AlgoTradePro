<script lang="ts" setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useTranslation } from '@/composables/useTranslation';
import type { Company, CompanyFilters, CompanyType } from '@/types';
import { router, Head, Link } from '@inertiajs/vue3';
import { Heart, Search, TrendingDown, TrendingUp, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { show as companyShow } from '@/routes/companies';
import { store as addFavorite, destroy as removeFavorite } from '@/routes/companies/favorite';

defineOptions({ layout: AppSidebarLayout });

interface Props {
    companies: {
        data: Company[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    types: CompanyType[];
    filters: CompanyFilters;
}

const props = defineProps<Props>();
const { t, isRTL } = useTranslation();

const search = ref(props.filters.search || '');
const selectedType = ref(props.filters.type?.toString() || 'all');
const isLoading = ref(false);

const applyFilters = () => {
    isLoading.value = true;
    router.get(
        '/companies',
        {
            search: search.value || undefined,
            type: selectedType.value === 'all' ? undefined : selectedType.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        }
    );
};

const clearFilters = () => {
    search.value = '';
    selectedType.value = 'all';
    applyFilters();
};

const toggleFavorite = (company: Company) => {
    const route = company.is_favorited
        ? removeFavorite({ company: company.id })
        : addFavorite({ company: company.id });

    router.post(route, {}, { preserveScroll: true });
};

const formatPrice = (price: string | null) => {
    if (!price) return t('companies.not_available', 'N/A');
    return parseFloat(price).toFixed(2);
};

const formatChange = (change: string | null) => {
    if (!change) return '0.00';
    const num = parseFloat(change);
    return num >= 0 ? `+${num.toFixed(2)}` : num.toFixed(2);
};

const getCompanyName = (company: Company) => {
    return isRTL() ? company.name_ar : company.name_en;
};

const getTypeName = (type: CompanyType) => {
    return isRTL() ? type.name_ar : type.name_en;
};

const hasActiveFilters = computed(() => {
    return search.value || (selectedType.value && selectedType.value !== 'all');
});
</script>

<template>
    <div>
        <Head :title="t('companies.title', 'Companies')" />

        <div class="container mx-auto space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div :class="isRTL() ? 'text-right' : 'text-left'">
                <h1 class="text-3xl font-bold tracking-tight">
                    {{ t('companies.title', 'Companies') }}
                </h1>
                <p class="mt-2 text-muted-foreground">
                    {{ t('companies.browse_description', 'Browse and track your favorite companies') }}
                </p>
            </div>

            <!-- Filters Card -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.search_title', 'Search Companies') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-col gap-4 md:flex-row">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <Search :class="isRTL() ? 'right-3' : 'left-3'" class="absolute top-3 h-4 w-4 text-muted-foreground" />
                            <Input
                                v-model="search"
                                :class="isRTL() ? 'pr-10' : 'pl-10'"
                                :placeholder="t('companies.search_placeholder', 'Search by symbol or name...')"
                                @keyup.enter="applyFilters"
                            />
                        </div>

                        <!-- Type Filter -->
                        <Select v-model="selectedType" @update:model-value="applyFilters">
                            <SelectTrigger class="w-full md:w-[200px]">
                                <SelectValue :placeholder="t('companies.filter_by_type', 'Filter by type')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    {{ t('companies.all_types', 'All Types') }}
                                </SelectItem>
                                <SelectItem v-for="type in types" :key="type.id" :value="type.id.toString()">
                                    {{ getTypeName(type) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <!-- Clear Filters -->
                        <Button
                            v-if="hasActiveFilters"
                            variant="outline"
                            @click="clearFilters"
                        >
                            <X class="mr-2 h-4 w-4" />
                            {{ t('companies.clear_filters', 'Clear') }}
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Results Info -->
            <div :class="isRTL() ? 'text-right' : 'text-left'" class="text-sm text-muted-foreground">
                {{ t('companies.showing', 'Showing') }} {{ companies.from || 0 }}-{{ companies.to || 0 }}
                {{ t('companies.of', 'of') }} {{ companies.total }}
                {{ t('companies.results', 'results') }}
            </div>

            <!-- Desktop Table -->
            <Card class="hidden md:block">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                {{ t('companies.symbol', 'Symbol') }}
                            </TableHead>
                            <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                {{ t('companies.name', 'Company Name') }}
                            </TableHead>
                            <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                {{ t('companies.type', 'Type') }}
                            </TableHead>
                            <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                {{ t('companies.current_price', 'Current Price') }}
                            </TableHead>
                            <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                {{ t('companies.change', 'Change') }}
                            </TableHead>
                            <TableHead :class="isRTL() ? 'text-right' : 'text-left'">
                                {{ t('companies.actions', 'Actions') }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <!-- Loading State -->
                        <TableRow v-if="isLoading">
                            <TableCell colspan="6">
                                <div class="space-y-2 py-4">
                                    <Skeleton class="h-8 w-full" />
                                    <Skeleton class="h-8 w-full" />
                                    <Skeleton class="h-8 w-full" />
                                </div>
                            </TableCell>
                        </TableRow>

                        <!-- Data Rows -->
                        <TableRow v-for="company in companies.data" v-else :key="company.id">
                            <TableCell class="font-medium">
                                <Link :href="companyShow({ company: company.id })" class="hover:underline">
                                    {{ company.symbol }}
                                </Link>
                            </TableCell>
                            <TableCell>{{ getCompanyName(company) }}</TableCell>
                            <TableCell>
                                <Badge :variant="company.type?.slug === 'stock' ? 'default' : 'secondary'">
                                    {{ company.type ? getTypeName(company.type) : 'N/A' }}
                                </Badge>
                            </TableCell>
                            <TableCell class="font-mono">
                                ${{ formatPrice(company.current_price) }}
                            </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">
                                    <span
                                        :class="[
                                            'font-mono',
                                            parseFloat(company.price_change || '0') >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        ]"
                                    >
                                        {{ formatChange(company.price_change) }}
                                    </span>
                                    <Badge
                                        :variant="parseFloat(company.price_change || '0') >= 0 ? 'default' : 'destructive'"
                                        class="gap-1"
                                    >
                                        <TrendingUp v-if="parseFloat(company.price_change || '0') >= 0" class="h-3 w-3" />
                                        <TrendingDown v-else class="h-3 w-3" />
                                        {{ company.change_percentage || '0.00' }}%
                                    </Badge>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    @click="toggleFavorite(company)"
                                >
                                    <Heart
                                        :class="company.is_favorited ? 'fill-red-500 text-red-500' : ''"
                                        class="h-4 w-4"
                                    />
                                </Button>
                            </TableCell>
                        </TableRow>

                        <!-- Empty State -->
                        <TableRow v-if="!isLoading && companies.data.length === 0">
                            <TableCell colspan="6">
                                <div class="py-12 text-center">
                                    <p class="text-muted-foreground">
                                        {{ t('companies.no_results', 'No companies found') }}
                                    </p>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        {{ t('companies.try_different_filters', 'Try adjusting your search or filters') }}
                                    </p>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </Card>

            <!-- Mobile Cards -->
            <div class="grid gap-4 md:hidden">
                <!-- Loading State -->
                <Card v-if="isLoading" v-for="i in 3" :key="i">
                    <CardContent class="p-4">
                        <Skeleton class="h-24 w-full" />
                    </CardContent>
                </Card>

                <!-- Data Cards -->
                <Card v-for="company in companies.data" v-else :key="company.id">
                    <CardContent class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <Link :href="companyShow({ company: company.id })">
                                    <div class="font-semibold">{{ company.symbol }}</div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ getCompanyName(company) }}
                                    </div>
                                </Link>
                                <Badge :variant="company.type?.slug === 'stock' ? 'default' : 'secondary'" class="mt-2">
                                    {{ company.type ? getTypeName(company.type) : 'N/A' }}
                                </Badge>
                            </div>
                            <Button
                                size="icon"
                                variant="ghost"
                                @click="toggleFavorite(company)"
                            >
                                <Heart
                                    :class="company.is_favorited ? 'fill-red-500 text-red-500' : ''"
                                    class="h-5 w-5"
                                />
                            </Button>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    {{ t('companies.price', 'Price') }}
                                </div>
                                <div class="font-mono font-semibold">
                                    ${{ formatPrice(company.current_price) }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">
                                    {{ t('companies.change', 'Change') }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <span
                                        :class="[
                                            'font-mono text-sm font-semibold',
                                            parseFloat(company.price_change || '0') >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        ]"
                                    >
                                        {{ company.change_percentage || '0.00' }}%
                                    </span>
                                    <TrendingUp
                                        v-if="parseFloat(company.price_change || '0') >= 0"
                                        class="h-3 w-3 text-green-600"
                                    />
                                    <TrendingDown v-else class="h-3 w-3 text-red-600" />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Empty State -->
                <Card v-if="!isLoading && companies.data.length === 0">
                    <CardContent class="py-12 text-center">
                        <p class="text-muted-foreground">
                            {{ t('companies.no_results', 'No companies found') }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{ t('companies.try_different_filters', 'Try adjusting your search or filters') }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <div
                v-if="companies.last_page > 1"
                :class="isRTL() ? 'flex-row-reverse' : ''"
                class="flex items-center justify-between"
            >
                <Button
                    :disabled="companies.current_page === 1"
                    variant="outline"
                    @click="router.get(`/companies?page=${companies.current_page - 1}`, { search: search, type: selectedType === 'all' ? undefined : selectedType })"
                >
                    {{ t('companies.previous', 'Previous') }}
                </Button>

                <span class="text-sm text-muted-foreground">
                    {{ t('companies.page', 'Page') }} {{ companies.current_page }} / {{ companies.last_page }}
                </span>

                <Button
                    :disabled="companies.current_page === companies.last_page"
                    variant="outline"
                    @click="router.get(`/companies?page=${companies.current_page + 1}`, { search: search, type: selectedType === 'all' ? undefined : selectedType })"
                >
                    {{ t('companies.next', 'Next') }}
                </Button>
            </div>
        </div>
    </div>
</template>
