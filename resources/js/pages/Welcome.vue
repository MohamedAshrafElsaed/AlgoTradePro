<script lang="ts" setup>
import LanguageToggle from '@/components/LanguageToggle.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useTranslation } from '@/composables/useTranslation';
import { login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { TrendingUp, BarChart, Shield, Brain, Target } from 'lucide-vue-next';

interface Props {
    canRegister: boolean;
}

defineProps<Props>();

const { t, isRTL } = useTranslation();

const features = [
    {
        icon: Brain,
        title: t('landing.features.ai.title', 'AI-Powered Analysis'),
        description: t('landing.features.ai.desc', 'Advanced algorithms analyze market trends in real-time'),
    },
    {
        icon: TrendingUp,
        title: t('landing.features.signals.title', 'Smart Trading Signals'),
        description: t('landing.features.signals.desc', 'Get instant buy/sell recommendations'),
    },
    {
        icon: BarChart,
        title: t('landing.features.analytics.title', 'Real-time Analytics'),
        description: t('landing.features.analytics.desc', 'Track portfolio performance with detailed insights'),
    },
    {
        icon: Shield,
        title: t('landing.features.security.title', 'Secure & Reliable'),
        description: t('landing.features.security.desc', 'Bank-level security for your trading data'),
    },
];

const stats = [
    { value: '10K+', label: t('landing.stats.users', 'Active Traders') },
    { value: '$50M+', label: t('landing.stats.volume', 'Trading Volume') },
    { value: '95%', label: t('landing.stats.accuracy', 'Signal Accuracy') },
];
</script>

<template>
    <Head>
        <title>{{ t('landing.meta.title', 'AlgoTradePro - AI-Powered Trading Recommendations') }}</title>
        <meta name="description" :content="t('landing.meta.description', 'Professional algorithmic trading platform with AI-powered stock recommendations')" />
    </Head>

    <div :class="{ rtl: isRTL() }" class="min-h-screen bg-background">
        <!-- Header -->
        <header class="sticky top-0 z-50 border-b border-border bg-background/95 backdrop-blur">
            <div class="container mx-auto flex h-16 items-center justify-between px-4">
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center rounded-lg bg-primary p-2">
                        <TrendingUp class="size-6 text-primary-foreground" />
                    </div>
                    <span class="text-xl font-bold">{{ t('landing.brand_name', 'AlgoTradePro') }}</span>
                </div>

                <nav class="hidden items-center gap-6 md:flex">
                    <a class="text-sm font-medium transition-colors hover:text-primary" href="#features">
                        {{ t('landing.nav.features', 'Features') }}
                    </a>
                    <a class="text-sm font-medium transition-colors hover:text-primary" href="#how-it-works">
                        {{ t('landing.nav.how_it_works', 'How It Works') }}
                    </a>
                </nav>

                <div class="flex items-center gap-3">
                    <LanguageToggle />
                    <Link :href="login()" class="hidden text-sm font-medium md:inline-flex">
                        {{ t('landing.nav.login', 'Login') }}
                    </Link>
                    <Link v-if="canRegister" :href="register()">
                        <Button class="bg-primary hover:bg-primary/90">
                            {{ t('landing.nav.get_started', 'Get Started') }}
                        </Button>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="container mx-auto px-4 py-20 text-center">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-4xl font-bold tracking-tight md:text-6xl">
                    {{ t('landing.hero.title', 'Trade Smarter with') }}
                    <span class="text-primary">{{ t('landing.hero.subtitle', 'AI-Powered Insights') }}</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground">
                    {{ t('landing.hero.description', 'Get real-time stock recommendations powered by advanced algorithms. Make informed trading decisions with confidence.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <Link v-if="canRegister" :href="register()">
                        <Button size="lg" class="bg-primary hover:bg-primary/90">
                            {{ t('landing.hero.cta', 'Start Free Trial') }}
                        </Button>
                    </Link>
                    <Link :href="login()">
                        <Button size="lg" variant="outline">
                            {{ t('landing.hero.cta_secondary', 'Login') }}
                        </Button>
                    </Link>
                </div>

                <!-- Stats -->
                <div class="mt-16 grid gap-8 sm:grid-cols-3">
                    <div v-for="(stat, index) in stats" :key="index" class="text-center">
                        <div class="text-3xl font-bold text-primary">{{ stat.value }}</div>
                        <div class="mt-1 text-sm text-muted-foreground">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="border-t bg-muted/50">
            <div class="container mx-auto px-4 py-20">
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-bold">{{ t('landing.features.title', 'Powerful Features') }}</h2>
                    <p class="mt-2 text-muted-foreground">{{ t('landing.features.subtitle', 'Everything you need for successful trading') }}</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <Card v-for="(feature, index) in features" :key="index" class="border-2">
                        <CardHeader>
                            <div class="mb-2 flex size-12 items-center justify-center rounded-lg bg-primary/10">
                                <component :is="feature.icon" class="size-6 text-primary" />
                            </div>
                            <CardTitle class="text-lg">{{ feature.title }}</CardTitle>
                            <CardDescription>{{ feature.description }}</CardDescription>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="container mx-auto px-4 py-20">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold">{{ t('landing.how_it_works.title', 'How It Works') }}</h2>
                <p class="mt-2 text-muted-foreground">{{ t('landing.how_it_works.subtitle', 'Start trading in three simple steps') }}</p>
            </div>

            <div class="mx-auto max-w-3xl space-y-8">
                <div class="flex gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">1</div>
                    <div>
                        <h3 class="text-xl font-semibold">{{ t('landing.how_it_works.step1.title', 'Create Your Account') }}</h3>
                        <p class="mt-2 text-muted-foreground">{{ t('landing.how_it_works.step1.desc', 'Sign up in seconds and access our trading platform') }}</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">2</div>
                    <div>
                        <h3 class="text-xl font-semibold">{{ t('landing.how_it_works.step2.title', 'Get AI Recommendations') }}</h3>
                        <p class="mt-2 text-muted-foreground">{{ t('landing.how_it_works.step2.desc', 'Receive real-time trading signals based on market analysis') }}</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary text-lg font-bold text-primary-foreground">3</div>
                    <div>
                        <h3 class="text-xl font-semibold">{{ t('landing.how_it_works.step3.title', 'Execute Trades') }}</h3>
                        <p class="mt-2 text-muted-foreground">{{ t('landing.how_it_works.step3.desc', 'Make informed decisions and track your portfolio performance') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="border-t bg-muted/50">
            <div class="container mx-auto px-4 py-20 text-center">
                <h2 class="text-3xl font-bold">{{ t('landing.cta.title', 'Ready to Start Trading?') }}</h2>
                <p class="mt-2 text-muted-foreground">{{ t('landing.cta.subtitle', 'Join thousands of traders making smarter decisions') }}</p>
                <Link v-if="canRegister" :href="register()" class="mt-8 inline-block">
                    <Button size="lg" class="bg-primary hover:bg-primary/90">
                        {{ t('landing.cta.button', 'Create Free Account') }}
                    </Button>
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t">
            <div class="container mx-auto px-4 py-8 text-center text-sm text-muted-foreground">
                <p>© {{ new Date().getFullYear() }} {{ t('landing.brand_name', 'AlgoTradePro') }}. {{ t('landing.footer.rights', 'All rights reserved.') }}</p>
            </div>
        </footer>
    </div>
</template>
