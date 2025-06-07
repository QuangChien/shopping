<template>
    <GuestLayout>
        <Head :title="translations.login" />

        <div class="mb-4 flex justify-end">
            <LanguageSwitcher />
        </div>

        <h1 class="mb-4 text-center text-2xl font-bold text-gray-700">
            {{ translations.login }}
        </h1>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="translations.email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="translations.password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ translations.remember_me }}</span>
                </label>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ translations.login_button }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const defaultTranslations = {
    login: 'Admin Login',
    email: 'Email',
    password: 'Password',
    remember_me: 'Remember Me',
    login_button: 'Login'
};

const translations = computed(() => {
    if (page.props.translations && page.props.translations.admin && page.props.translations.admin.auth) {
        return {
            login: page.props.translations.admin.auth.login || defaultTranslations.login,
            email: page.props.translations.admin.auth.email || defaultTranslations.email,
            password: page.props.translations.admin.auth.password || defaultTranslations.password,
            remember_me: page.props.translations.admin.auth.remember_me || defaultTranslations.remember_me,
            login_button: page.props.translations.admin.auth.login_button || defaultTranslations.login_button
        };
    }
    return defaultTranslations;
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('admin.login.post'), {
        onFinish: () => form.reset('password'),
    });
};
</script> 