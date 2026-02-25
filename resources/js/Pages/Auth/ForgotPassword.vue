<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ status: String });

const form = useForm({ email: '' });
const submit = () => form.post(route('password.email'));
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password — Civio" />

        <div class="mb-7 text-center">
            <h1 class="text-2xl font-bold text-white">Forgot your password? 🔐</h1>
            <p class="text-slate-400 mt-1 text-sm">Enter your email and we'll send a reset link.</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-400 bg-green-500/10 border border-green-500/20 rounded-lg px-4 py-3">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email address" />
                <TextInput
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                />
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <PrimaryButton class="w-full justify-center py-3 text-base" :disabled="form.processing">
                <span v-if="form.processing">Sending link...</span>
                <span v-else">Send Reset Link →</span>
            </PrimaryButton>

            <p class="text-center text-sm text-slate-500">
                Remembered your password?
                <Link :href="route('login')" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">
                    Sign in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
