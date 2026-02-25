<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ status: String });
const form = useForm({});
const submit = () => form.post(route('verification.send'));
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verify Email — Civio" />

        <div class="mb-7 text-center">
            <div class="text-5xl mb-4">📧</div>
            <h1 class="text-2xl font-bold text-white">Check your email</h1>
            <p class="text-slate-400 mt-2 text-sm leading-relaxed">
                We sent a verification link to your email address.<br />
                Click it to activate your account.
            </p>
        </div>

        <div v-if="verificationLinkSent" class="mb-5 text-sm font-medium text-green-400 bg-green-500/10 border border-green-500/20 rounded-lg px-4 py-3 text-center">
            ✓ A new verification link has been sent to your email.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <PrimaryButton class="w-full justify-center py-3 text-base" :disabled="form.processing">
                <span v-if="form.processing">Sending...</span>
                <span v-else>Resend Verification Email</span>
            </PrimaryButton>

            <div class="text-center">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-slate-500 hover:text-slate-300 transition-colors"
                >
                    Log out and try a different account
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
