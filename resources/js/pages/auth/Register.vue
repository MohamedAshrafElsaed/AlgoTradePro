<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useTranslation } from '@/composables/useTranslation';
import LandingLayout from '@/layouts/LandingLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head, Link } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';

const { t } = useTranslation();
</script>

<template>
    <LandingLayout>
        <Head :title="t('auth.register.title', 'Register')" />

        <div class="mx-auto max-w-md px-4 py-16">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold tracking-tight">
                    {{ t('auth.register.title', 'Create an account') }}
                </h1>
                <p class="mt-2 text-muted-foreground">
                    {{ t('auth.register.description', 'Start trading with AI-powered recommendations') }}
                </p>
            </div>

            <!-- Registration Form -->
            <div class="rounded-lg border bg-card p-8 shadow-sm">
                <Form
                    v-slot="{ errors, processing }"
                    :reset-on-success="['password', 'password_confirmation']"
                    class="flex flex-col gap-6"
                    v-bind="store.form()"
                >
                    <div class="grid gap-6">
                        <!-- Name -->
                        <div class="grid gap-2">
                            <Label for="name">
                                {{ t('auth.register.name', 'Full Name') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                :class="{ 'border-destructive': errors.name }"
                                :placeholder="t('auth.placeholders.name', 'John Doe')"
                                :tabindex="1"
                                autocomplete="name"
                                autofocus
                                name="name"
                                required
                                type="text"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <!-- Email -->
                        <div class="grid gap-2">
                            <Label for="email">
                                {{ t('auth.register.email', 'Email Address') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="email"
                                :class="{ 'border-destructive': errors.email }"
                                :placeholder="t('auth.placeholders.email', 'email@example.com')"
                                :tabindex="2"
                                autocomplete="email"
                                name="email"
                                required
                                type="email"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <!-- Password -->
                        <div class="grid gap-2">
                            <Label for="password">
                                {{ t('auth.register.password', 'Password') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="password"
                                :class="{ 'border-destructive': errors.password }"
                                :placeholder="t('auth.placeholders.password', 'Min 8 characters')"
                                :tabindex="3"
                                autocomplete="new-password"
                                name="password"
                                required
                                type="password"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <!-- Password Confirmation -->
                        <div class="grid gap-2">
                            <Label for="password_confirmation">
                                {{ t('auth.register.password_confirmation', 'Confirm Password') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="password_confirmation"
                                :class="{ 'border-destructive': errors.password_confirmation }"
                                :placeholder="t('auth.placeholders.password_confirmation', 'Confirm password')"
                                :tabindex="4"
                                autocomplete="new-password"
                                name="password_confirmation"
                                required
                                type="password"
                            />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            :disabled="processing"
                            :tabindex="5"
                            class="mt-2 w-full bg-primary hover:bg-primary/90"
                            data-test="register-user-button"
                            type="submit"
                        >
                            <Loader2 v-if="processing" class="mr-2 h-4 w-4 animate-spin" />
                            {{ t('auth.register.submit', 'Create Account') }}
                        </Button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center text-sm text-muted-foreground">
                        {{ t('auth.register.have_account', 'Already have an account?') }}
                        <Link
                            :href="login()"
                            :tabindex="6"
                            class="font-medium text-primary hover:underline"
                        >
                            {{ t('auth.register.login', 'Log in') }}
                        </Link>
                    </div>
                </Form>
            </div>
        </div>
    </LandingLayout>
</template>
