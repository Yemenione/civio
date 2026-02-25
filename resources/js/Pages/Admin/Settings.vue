<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    ai_config: Object,
    google_oauth: Object,
    stripe: Object,
});

const form = useForm({
    api_key: props.ai_config.full_key || '',
});

const googleForm = useForm({
    client_id:     '',
    client_secret: '',
    redirect_uri:  props.google_oauth?.redirect_uri || 'http://localhost:8000/auth/google/callback',
});

const stripeForm = useForm({
    publishable_key: '',
    secret_key:      '',
    webhook_secret:  '',
});

const showKey        = ref(false);
const showGoogleSecret = ref(false);
const showGoogleForm   = ref(false);
const showStripeForm   = ref(false);
const showStripeSecret = ref(false);

const submitAi = () => {
    form.patch(route('admin.settings.ai.update'), { preserveScroll: true });
};

const submitGoogle = () => {
    googleForm.patch(route('admin.settings.google.update'), {
        preserveScroll: true,
        onSuccess: () => { showGoogleForm.value = false; googleForm.reset('client_id', 'client_secret'); }
    });
};

const submitStripe = () => {
    stripeForm.patch(route('admin.settings.stripe.update'), {
        preserveScroll: true,
        onSuccess: () => { showStripeForm.value = false; stripeForm.reset(); }
    });
};
</script>

<template>
    <Head :title="t('system_settings')" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ t('system_settings') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- AI Engine Configuration -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-slate-200">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-600 border border-indigo-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">{{ t('ai_config_title') }}</h3>
                                <p class="text-slate-500 text-sm font-medium">{{ t('ai_config_desc') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <!-- Status Panel -->
                            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 flex flex-col justify-center">
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                        <span class="text-xs font-black uppercase tracking-widest text-slate-400">{{ t('current_provider') }}</span>
                                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-[10px] font-black uppercase tracking-wider">{{ t('universal_ai_core') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                        <span class="text-xs font-black uppercase tracking-widest text-slate-400">{{ t('active_model') }}</span>
                                        <span class="text-sm font-bold text-slate-700">{{ t('high_perf_neural') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-black uppercase tracking-widest text-slate-400">{{ t('connection_status') }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                            <span class="text-sm font-bold text-emerald-600">{{ t('active_operational') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Panel -->
                            <form @submit.prevent="submitAi" class="space-y-6">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ t('ai_api_key') }}</label>
                                    <div class="relative">
                                        <input
                                            :type="showKey ? 'text' : 'password'"
                                            v-model="form.api_key"
                                            class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-mono text-sm pr-12"
                                            :placeholder="t('ai_api_key_placeholder')"
                                        />
                                        <button 
                                            type="button"
                                            @click="showKey = !showKey"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                        >
                                            <svg v-if="!showKey" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                    <p class="mt-2 text-[10px] text-slate-400 font-medium">{{ t('ai_api_key_hint') }}</p>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-6 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-[0.2em] transition-all hover:bg-slate-800 active:scale-95 disabled:opacity-50 flex items-center gap-2"
                                    >
                                        <span v-if="form.processing">{{ t('updating') }}</span>
                                        <span v-else>{{ t('save_config') }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Google OAuth Configuration -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-slate-200">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center border border-slate-200 bg-slate-50 p-2">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Google OAuth Configuration</h3>
                                <p class="text-slate-500 text-sm font-medium">Manage "Sign in with Google" credentials</p>
                            </div>
                            <!-- Status badge -->
                            <div v-if="google_oauth?.is_configured" class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-full">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Enabled</span>
                            </div>
                            <div v-else class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 rounded-full">
                                <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-700">Not Configured</span>
                            </div>
                        </div>

                        <!-- Info rows -->
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4 mb-6">
                            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Client ID</span>
                                <span class="text-sm font-mono font-bold text-slate-700">{{ google_oauth?.client_id }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Client Secret</span>
                                <span class="text-sm font-mono font-bold text-slate-700">{{ google_oauth?.client_secret }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Redirect URI</span>
                                <span class="text-sm font-mono text-slate-600">{{ google_oauth?.redirect_uri }}</span>
                            </div>
                        </div>

                        <!-- Toggle update form -->
                        <div>
                            <button @click="showGoogleForm = !showGoogleForm" type="button" class="text-xs font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800 flex items-center gap-1.5 transition-colors mb-4">
                                <svg :class="['w-3 h-3 transition-transform', showGoogleForm ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                {{ showGoogleForm ? 'Hide' : 'Update' }} Credentials
                            </button>

                            <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition-all duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                                <form v-if="showGoogleForm" @submit.prevent="submitGoogle" class="space-y-4 bg-slate-50 rounded-2xl p-6 border border-slate-100">
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">New Client ID</label>
                                        <input v-model="googleForm.client_id" type="text" placeholder="xxxx.apps.googleusercontent.com" class="w-full border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:ring-2 focus:ring-indigo-500 font-mono text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">New Client Secret</label>
                                        <div class="relative">
                                            <input v-model="googleForm.client_secret" :type="showGoogleSecret ? 'text' : 'password'" placeholder="GOCSPX-..." class="w-full border-slate-200 rounded-xl px-4 py-3 pr-12 text-slate-900 focus:ring-2 focus:ring-indigo-500 font-mono text-sm" />
                                            <button type="button" @click="showGoogleSecret = !showGoogleSecret" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="googleForm.processing" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 active:scale-95 disabled:opacity-50 transition-all">
                                            {{ googleForm.processing ? 'Saving...' : 'Save Credentials' }}
                                        </button>
                                    </div>
                                </form>
                            </transition>
                        </div>
                    </div>
                </div>

                <!-- Stripe Payments Configuration -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-slate-200">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center border border-slate-200 bg-slate-50 p-2">
                                <!-- Stripe logo -->
                                <svg viewBox="0 0 24 24" fill="#635BFF" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.594-7.305h.003z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Stripe Payments</h3>
                                <p class="text-slate-500 text-sm font-medium">Manage payment gateway credentials</p>
                            </div>
                            <div v-if="stripe?.is_configured" class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-full">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Active</span>
                            </div>
                            <div v-else class="flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 rounded-full">
                                <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-700">Not Configured</span>
                            </div>
                        </div>

                        <!-- Info rows -->
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 space-y-4 mb-6">
                            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Publishable Key</span>
                                <span class="text-sm font-mono font-bold text-slate-700">{{ stripe?.publishable_key }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-4 border-b border-slate-200">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Secret Key</span>
                                <span class="text-sm font-mono font-bold text-slate-700">{{ stripe?.secret_key }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Webhook Secret</span>
                                <span class="text-sm font-mono text-slate-600">{{ stripe?.webhook_secret }}</span>
                            </div>
                        </div>

                        <!-- Toggle update form -->
                        <div>
                            <button @click="showStripeForm = !showStripeForm" type="button" class="text-xs font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800 flex items-center gap-1.5 transition-colors mb-4">
                                <svg :class="['w-3 h-3 transition-transform', showStripeForm ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                {{ showStripeForm ? 'Hide' : 'Update' }} Stripe Keys
                            </button>

                            <transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition-all duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                                <form v-if="showStripeForm" @submit.prevent="submitStripe" class="space-y-4 bg-slate-50 rounded-2xl p-6 border border-slate-100">
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Publishable Key (pk_live_... or pk_test_...)</label>
                                        <input v-model="stripeForm.publishable_key" type="text" placeholder="pk_live_..." class="w-full border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:ring-2 focus:ring-indigo-500 font-mono text-sm" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Secret Key (sk_live_... or sk_test_...)</label>
                                        <div class="relative">
                                            <input v-model="stripeForm.secret_key" :type="showStripeSecret ? 'text' : 'password'" placeholder="sk_live_..." class="w-full border-slate-200 rounded-xl px-4 py-3 pr-12 text-slate-900 focus:ring-2 focus:ring-indigo-500 font-mono text-sm" />
                                            <button type="button" @click="showStripeSecret = !showStripeSecret" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Webhook Secret (whsec_...) <span class="text-slate-400 normal-case font-normal">· Optional</span></label>
                                        <input v-model="stripeForm.webhook_secret" type="text" placeholder="whsec_..." class="w-full border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:ring-2 focus:ring-indigo-500 font-mono text-sm" />
                                        <p class="mt-1 text-xs text-slate-400">Get this from Stripe Dashboard → Webhooks. Set endpoint to: <code class="bg-slate-100 px-1 rounded text-slate-600">{{ window?.location?.origin }}/stripe/webhook</code></p>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="stripeForm.processing" class="px-6 py-2.5 bg-[#635BFF] text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#4F46E5] active:scale-95 disabled:opacity-50 transition-all">
                                            {{ stripeForm.processing ? 'Saving...' : 'Save Stripe Keys' }}
                                        </button>
                                    </div>
                                </form>
                            </transition>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-indigo-600 rounded-2xl p-8 text-white flex items-center justify-between shadow-2xl shadow-indigo-500/30">
                    <div class="max-w-xl">
                        <h4 class="text-xl font-bold mb-2">{{ t('ai_integrated_title') }}</h4>
                        <p class="text-indigo-100 text-sm">{{ t('ai_integrated_desc') }}</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="text-5xl opacity-20">🤖🌍</div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
