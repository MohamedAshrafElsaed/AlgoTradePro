<script lang="ts" setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useTranslation } from '@/composables/useTranslation';
import type { Company } from '@/types';
import { router, Head, Link } from '@inertiajs/vue3';
import { Heart, Star, TrendingDown, TrendingUp } from 'lucide-vue-next';
import { show as companyShow, index as companiesIndex } from '@/routes/companies';
import { destroy as removeFavorite } from '@/routes/companies/favorite';

defineOptions({ layout: AppSidebarLayout });

interface Props {
    companies: {
        data: Company[];
        current_page: number;
        last_page: number;
        total: number;
    };
}

const props = defineProps<Props>();
const { t, isRTL } = useTranslation();

const handleRemoveFavorite = (company: Company) => {
    router.delete(removeFavorite({ company: company.id }), { preserveScroll: true });
};

const formatPrice = (price: string) => {
    return parseFloat(price).toFixed(5);
};

const getCompanyName = (company: Company) => {
    return isRTL() ? company.name_ar : company.name_en;
};

const getTypeName = (company: Company) => {
    return isRTL() ? company.type.name_ar : company.type.name_en;
};
</script>

<template>
    <div>
        <Head :title="t('companies.favorites_title', 'Favorite Companies')" />

        <div class="container mx-auto space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div :class="isRTL() ? 'text-right' : 'text-left'">
                <div class="flex items-center gap-2">
                    <Star class="h-6 w-6 fill-yellow-500 text-yellow-500" />
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ t('companies.favorites_title', 'Favorite Companies') }}
                    </h1>
                </div>
                <p class="mt-2 text-muted-foreground">
                    {{ t('companies.favorites_description', 'Companies you are tracking') }}
                </p>
            </div>

            <!-- Empty State -->
            <Card v-if="companies.data.length === 0">
                <CardContent class="flex flex-col items-center py-16">
                    <Star class="mb-4 h-16 w-16 text-muted-foreground" />
                    <h3 class="mb-2 text-lg font-semibold">
                        {{ t('companies.no_favorites', 'No favorite companies yet') }}
                    </h3>
                    <p class="mb-4 text-center text-sm text-muted-foreground">
                        {{ t('companies.start_adding_favorites', 'Start adding companies to your favorites') }}
                    </p>
                    <Link :href="companiesIndex()">
                        <Button>
                            {{ t('companies.explore_companies', 'Explore Companies') }}
                        </Button>
                    </Link>
                </CardContent>
            </Card>

            <!-- Desktop Table -->
            <Card v-else class="hidden md:block">
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
                        <TableRow v-for="company in companies.data" :key="company.id">
                            <TableCell class="font-medium">
                                <Link :href="companyShow({ company: company.id })" class="hover:underline">
                                    {{ company.symbol }}
                                </Link>
                            </TableCell>
                            <TableCell>{{ getCompanyName(company) }}</TableCell>
                            <TableCell>
                                <Badge :variant="company.type.slug === 'stock' ? 'default' : 'secondary'">
                                    {{ getTypeName(company) }}
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
                                            parseFloat(company.price_change) >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        ]"
                                    >
                                        {{ parseFloat(company.price_change) >= 0 ? '+' : '' }}{{ parseFloat(company.price_change).toFixed(5) }}
                                    </span>
                                    <Badge
                                        :variant="parseFloat(company.price_change) >= 0 ? 'default' : 'destructive'"
                                        class="gap-1"
                                    >
                                        <TrendingUp v-if="parseFloat(company.price_change) >= 0" class="h-3 w-3" />
                                        <TrendingDown v-else class="h-3 w-3" />
                                        {{ company.change_percentage }}%
                                    </Badge>
                                </div>
                            </TableCell>
                            <TableCell>
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    @click="handleRemoveFavorite(company)"
                                >
                                    <Heart class="h-4 w-4 fill-red-500 text-red-500" />
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </Card>

            <!-- Mobile Cards -->
            <div v-if="companies.data.length > 0" class="grid gap-4 md:hidden">
                <Card v-for="company in companies.data" :key="company.id">
                    <CardContent class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <Link :href="companyShow({ company: company.id })">
                                    <div class="font-semibold">{{ company.symbol }}</div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ getCompanyName(company) }}
                                    </div>
                                </Link>
                                <Badge :variant="company.type.slug === 'stock' ? 'default' : 'secondary'" class="mt-2">
                                    {{ getTypeName(company) }}
                                </Badge>
                            </div>
                            <Button
                                size="icon"
                                variant="ghost"
                                @click="handleRemoveFavorite(company)"
                            >
                                <Heart class="h-5 w-5 fill-red-500 text-red-500" />
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
                                            parseFloat(company.price_change) >= 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        ]"
                                    >
                                        {{ company.change_percentage }}%
                                    </span>
                                    <TrendingUp
                                        v-if="parseFloat(company.price_change) >= 0"
                                        class="h-3 w-3 text-green-600"
                                    />
                                    <TrendingDown v-else class="h-3 w-3 text-red-600" />
                                </div>
                            </div>
                        </div>
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
                    @click="router.get(`/companies/favorites?page=${companies.current_page - 1}`)"
                >
                    {{ t('companies.previous', 'Previous') }}
                </Button>

                <span class="text-sm text-muted-foreground">
                    {{ t('companies.page', 'Page') }} {{ companies.current_page }} / {{ companies.last_page }}
                </span>

                <Button
                    :disabled="companies.current_page === companies.last_page"
                    variant="outline"
                    @click="router.get(`/companies/favorites?page=${companies.current_page + 1}`)"
                >
                    {{ t('companies.next', 'Next') }}
                </Button>
            </div>
        </div>
    </div>
</template>
