<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useNotify } from '@/composables/useNotify';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const notify = useNotify();
const page = usePage();
const isAuthenticated = !!page.props.auth?.user;
const isPro = page.props.auth?.user?.plan === 'pro';
const activeTab = ref('monthly');
const isScrolled = ref(false);

// Coupon logic
const couponCode = ref('');
const couponValid = ref(false);
const discountPercent = ref(0);
const isCheckingCoupon = ref(false);
const couponError = ref('');

const checkCoupon = async () => {
    if (!couponCode.value) return;
    isCheckingCoupon.value = true;
    couponError.value = '';
    
    try {
        const response = await axios.post(route('stripe.validate-coupon'), { code: couponCode.value });
        if (response.data.valid) {
            couponValid.value = true;
            discountPercent.value = response.data.discount_percent;
        } else {
            couponError.value = 'Invalid or expired coupon.';
            couponValid.value = false;
        }
    } catch (e) {
        couponError.value = 'Error validating coupon.';
    } finally {
        isCheckingCoupon.value = false;
    }
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    // Load Stripe.js if needed
    if (!window.Stripe) {
        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        document.head.appendChild(script);
    }
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const isProcessing = ref(false);

const handleSubscription = async (plan) => {
    if (!isAuthenticated) {
        window.location.href = route('register');
        return;
    }

    if (plan.name === 'Free') {
        window.location.href = route('dashboard');
        return;
    }

    isProcessing.value = true;

    try {
        const response = await axios.post(route('stripe.checkout'), {
            plan: 'pro',
            billing: activeTab.value,
            coupon: couponCode.value
        });

        const stripe = window.Stripe(page.props.stripe_key || 'pk_test_placeholder');
        await stripe.redirectToCheckout({ sessionId: response.data.id });
    } catch (e) {
        console.error(e);
        notify.alert(t('payment_failed'), t('payment_init_failed'), 'error');
    } finally {
        isProcessing.value = false;
    }
};

const plans = [
    {
        name: t('plan_free'),
        price: { monthly: 0, yearly: 0 },
        description: t('free_plan_desc'),
        color: 'slate',
        features: [
            { text: t('3 Professional Resumes'), included: true },
            { text: t('Classic & Minimal Templates'), included: true },
            { text: t('Standard PDF Export'), included: true },
            { text: t('3 Languages (EN/AR/FR)'), included: true },
            { text: t('Universal AI Intelligence'), included: false },
            { text: t('ATS Optimization Score'), included: false },
        ],
        cta: t('start_free_trial'),
        ctaRoute: 'register',
        highlight: false,
    },
    {
        name: t('plan_starter'),
        price: { monthly: 2, yearly: 20 },
        description: t('starter_plan_desc'),
        color: 'blue',
        features: [
            { text: t('5 Professional Resumes'), included: true },
            { text: t('Premium Templates Access'), included: true },
            { text: t('High-Quality PDF Export'), included: true },
            { text: t('AI Content Assistance'), included: true },
            { text: t('ATS Optimization Score'), included: true },
            { text: t('Priority Email Support'), included: false },
        ],
        cta: t('upgrade_plan'),
        ctaRoute: 'subscription.upgrade',
        highlight: false,
    },
    {
        name: t('plan_pro'),
        price: { monthly: 5, yearly: 49 },
        description: t('pro_plan_desc'),
        color: 'indigo',
        features: [
            { text: t('Unlimited Resumes'), included: true },
            { text: t('All 6 Premium Templates'), included: true },
            { text: t('High-Quality PDF Export'), included: true },
            { text: t('8 Languages & RTL Support'), included: true },
            { text: t('Full AI Power'), included: true },
            { text: t('Real-time ATS Score Checker'), included: true },
        ],
        cta: isPro ? '✓ ' + t('current') : t('upgrade_plan'),
        ctaRoute: isPro ? 'dashboard' : 'subscription.upgrade',
        highlight: true,
    },
    {
        name: t('plan_lifetime'),
        price: { monthly: 29, yearly: 29 }, // Represented as one-time
        description: t('lifetime_plan_desc'),
        color: 'purple',
        isLifetime: true,
        features: [
            { text: t('Everything in Pro'), included: true },
            { text: t('One-time Payment'), included: true },
            { text: t('Perpetual Access'), included: true },
            { text: t('All Future Templates'), included: true },
            { text: t('Priority Support Forever'), included: true },
            { text: t('Early Access Features'), included: true },
        ],
        cta: t('get_lifetime'),
        ctaRoute: 'subscription.upgrade',
        highlight: false,
    },
];

const comparisonFeatures = [
    { name: t('Number of Resumes'), free: '3', pro: t('Unlimited') },
    { name: t('Number of Templates'), free: '2', pro: t('All 6') },
    { name: t('AI Content Assistant'), free: '✕', pro: '✓' },
    { name: t('ATS Score Checker'), free: '✕', pro: '✓' },
    { name: t('Cover Letter Builder'), free: '✕', pro: '✓' },
    { name: t('Public Share Links'), free: '✕', pro: '✓' },
    { name: t('Download Format'), free: t('Standard PDF'), pro: t('HQ PDF / Word') },
    { name: t('RTL (Arabic) Support'), free: '✓', pro: '✓' },
    { name: t('Ad-Free Experience'), free: '✓', pro: '✓' },
    { name: t('Priority Support'), free: '✕', pro: '✓' },
];
</script>

<template>
    <Head :title="t('pricing_title')">
        <meta name="description" :content="t('pricing_meta')" />
    </Head>

    <div class="min-h-screen bg-white dark:bg-slate-900 text-slate-900 dark:text-white selection:bg-indigo-500/30 transition-colors duration-300">
        <!-- Background Decor -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] bg-indigo-600/10 rounded-full blur-[160px] animate-pulse"></div>
            <div class="absolute -bottom-[20%] -right-[10%] w-[70%] h-[70%] bg-purple-600/10 rounded-full blur-[160px] animate-pulse" style="animation-delay: 3s"></div>
            <div class="absolute top-0 left-0 w-full h-full opacity-[0.03] pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png')"></div>
        </div>

        <!-- Navbar -->
        <nav 
            class="fixed w-full z-50 transition-all duration-500 border-b"
            :class="isScrolled ? 'bg-white/90 dark:bg-slate-900/90 backdrop-blur-2xl border-black/5 dark:border-white/10 py-3 shadow-lg shadow-black/5' : 'bg-transparent border-transparent py-6'"
        >
            <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-indigo-500 blur-lg opacity-0 group-hover:opacity-40 transition-opacity"></div>
                        <img src="/images/logo.png" alt="Logo" class="relative w-10 h-10 object-contain group-hover:scale-110 transition-transform" />
                    </div>
                    <span class="text-2xl font-black tracking-tighter bg-clip-text text-transparent bg-gradient-to-r from-slate-900 dark:from-white via-slate-800 dark:via-white to-slate-400 uppercase">
                        CIVIO
                    </span>
                </Link>

                <div class="flex items-center gap-6">
                    <Link v-if="!isAuthenticated" :href="route('login')" class="hidden md:block text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white transition-colors">
                        {{ t('sign_in') }}
                    </Link>
                    <Link 
                        :href="isAuthenticated ? route('dashboard') : route('register')" 
                        class="px-7 py-3 rounded-full text-[10px] font-black tracking-[0.2em] bg-indigo-600 dark:bg-white text-white dark:text-slate-900 hover:bg-indigo-500 dark:hover:bg-white/90 transition-all shadow-2xl hover:shadow-indigo-500/30 dark:hover:shadow-white/20 active:scale-95 uppercase shimmer-effect"
                    >
                        {{ isAuthenticated ? t('dashboard') : t('sign_up') }}
                    </Link>
                </div>
            </div>
        </nav>

        <main class="relative z-10 pt-40 pb-32">
            <!-- Hero Section -->
            <div class="max-w-7xl mx-auto px-6 text-center mb-24">
                <div class="inline-flex items-center gap-3 px-5 py-2 bg-white/5 border border-white/10 rounded-full text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400 mb-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
                    <span class="flex h-2 w-2 rounded-full bg-indigo-500 animate-ping"></span>
                    {{ t('pricing_hero_badge') }}
                </div>
                <h1 class="text-5xl md:text-8xl font-black tracking-tight mb-8 bg-clip-text text-transparent bg-gradient-to-b from-slate-900 dark:from-white to-slate-500 animate-in fade-in slide-in-from-bottom-8 duration-700 delay-100" v-html="t('pricing_hero_title')">
                </h1>
                <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto leading-relaxed font-bold animate-in fade-in slide-in-from-bottom-12 duration-700 delay-200">
                    {{ t('pricing_hero_subtitle') }}
                </p>

                <!-- Toggle -->
                <div class="mt-16 inline-flex items-center p-2 bg-slate-100 dark:bg-slate-800/40 backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[2rem] shadow-xl animate-in fade-in zoom-in duration-500 delay-300">
                    <button
                        @click="activeTab = 'monthly'"
                        class="px-10 py-3.5 rounded-[1.5rem] text-sm font-black transition-all duration-500"
                        :class="activeTab === 'monthly' ? 'bg-white text-slate-950 shadow-xl' : 'text-slate-500 hover:text-slate-300'"
                    >
                        {{ t('monthly') }}
                    </button>
                    <button
                        @click="activeTab = 'yearly'"
                        class="px-10 py-3.5 rounded-[1.5rem] text-sm font-black transition-all duration-500 flex items-center gap-3"
                        :class="activeTab === 'yearly' ? 'bg-white dark:bg-slate-900 text-slate-950 dark:text-white shadow-xl' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                    >
                        {{ t('yearly') }}
                        <span class="px-2.5 py-1 bg-indigo-500 text-white text-[9px] rounded-full uppercase tracking-tighter shadow-lg shadow-indigo-500/20">{{ t('save_percent') }}</span>
                    </button>
                </div>
            </div>

            <!-- Trusted By (Marquee style) -->
            <div class="max-w-7xl mx-auto px-6 mb-32 opacity-40 hover:opacity-60 transition-opacity whitespace-nowrap overflow-hidden">
                <p class="text-center text-[10px] font-black uppercase tracking-[0.3em] text-slate-500 mb-10">{{ t('trusted_by') }}</p>
                <div class="flex justify-center gap-12 md:gap-24 grayscale brightness-200">
                    <span class="text-xl font-black tracking-widest italic opacity-60">GOOGLE</span>
                    <span class="text-xl font-black tracking-widest italic opacity-60">META</span>
                    <span class="text-xl font-black tracking-widest italic opacity-60">AMAZON</span>
                    <span class="text-xl font-black tracking-widest italic opacity-60">MICROSOFT</span>
                    <span class="text-xl font-black tracking-widest italic opacity-60">TESLA</span>
                </div>
            </div>

            <!-- Plans Grid -->
            <div class="max-w-7xl mx-auto px-6 mb-40">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="plan in plans" :key="plan.name"
                        class="group relative rounded-[3rem] p-px transition-all duration-700 hover:scale-[1.03]"
                        :class="plan.highlight ? 'bg-gradient-to-b from-indigo-400 via-purple-400 to-transparent shadow-2xl shadow-indigo-500/10' : 'bg-white/5 hover:bg-white/10'"
                    >
                        <!-- Badge -->
                        <div v-if="plan.highlight" class="absolute -top-5 left-1/2 -translate-x-1/2 px-6 py-2 bg-white text-slate-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-2xl z-20">
                            {{ t('recommended') }}
                        </div>

                        <div class="relative h-full bg-slate-50 dark:bg-slate-900/60 backdrop-blur-3xl rounded-[2.95rem] overflow-hidden px-10 py-12 flex flex-col border border-black/5 dark:border-white/5">
                            <!-- Premium Glow for Pro -->
                            <div v-if="plan.highlight" class="absolute -top-32 -right-32 w-64 h-64 bg-indigo-500/10 blur-[100px] pointer-events-none group-hover:bg-indigo-500/20 transition-all duration-700"></div>

                            <div class="mb-10">
                                <h3 class="text-3xl font-black tracking-tight mb-2" :class="plan.highlight ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-400'">
                                    {{ plan.name }}
                                </h3>
                                <div class="flex items-baseline gap-2 mt-4">
                                    <span class="text-6xl font-black text-slate-900 dark:text-white tracking-tighter">${{ plan.price[activeTab] }}</span>
                                    <span class="text-slate-500 text-sm font-bold uppercase tracking-widest">
                                        / {{ plan.isLifetime ? t('lifetime') : (activeTab === 'monthly' ? t('price_mo') : t('price_yr')) }}
                                    </span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-400 text-sm mt-6 leading-relaxed font-bold">
                                    {{ plan.description }}
                                </p>
                            </div>

                            <!-- Coupon Section (Only for Pro) -->
                            <div v-if="plan.name === 'Pro'" class="mb-8 p-4 bg-white/5 rounded-2xl border border-white/10">
                                <label class="block text-[10px] font-black tracking-widest text-slate-500 uppercase mb-3">{{ t('have_coupon') }}</label>
                                <div class="flex gap-2">
                                    <input 
                                        v-model="couponCode" 
                                        type="text" 
                                        placeholder="CODE"
                                        class="flex-1 bg-slate-900/50 border-white/10 rounded-xl px-4 py-2 text-xs font-mono uppercase text-white focus:ring-1 focus:ring-indigo-500 transition-all"
                                    />
                                    <button 
                                        @click="checkCoupon"
                                        :disabled="isCheckingCoupon"
                                        class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                                    >
                                        {{ isCheckingCoupon ? '...' : t('apply') }}
                                    </button>
                                </div>
                                <p v-if="couponValid" class="mt-2 text-[10px] text-emerald-400 font-bold uppercase tracking-widest animate-pulse">
                                    {{ discountPercent }}% {{ t('discount_applied') }}
                                </p>
                                <p v-if="couponError" class="mt-2 text-[10px] text-rose-400 font-bold uppercase tracking-widest">
                                    {{ couponError }}
                                </p>
                            </div>

                            <div class="laser-btn-wrapper w-full group/btn transition-all hover:shadow-indigo-500/30 active:scale-[0.98] mb-12">
                                <button
                                    @click="handleSubscription(plan)"
                                    :disabled="isProcessing"
                                    class="laser-btn-content w-full py-5 flex items-center justify-center gap-3 text-white"
                                >
                                    <span class="text-[10px] font-black tracking-[0.25em] uppercase">
                                        {{ isProcessing ? 'Initializing...' : plan.cta }}
                                    </span>
                                    <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>

                            <div class="space-y-6">
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em]">{{ t('core_features') }}</p>
                                <ul class="space-y-5">
                                    <li v-for="feature in plan.features" :key="feature.text" class="flex items-start gap-4 text-sm group/item">
                                        <div class="mt-0.5">
                                            <div v-if="feature.included" class="w-5 h-5 rounded-full bg-indigo-500 text-white shadow-lg shadow-indigo-500/30 flex items-center justify-center scale-90 transition-transform group-hover/item:scale-110">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div v-else class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-400 dark:text-slate-600 flex items-center justify-center scale-90">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </div>
                                        </div>
                                        <span :class="feature.included ? 'text-slate-700 dark:text-slate-300 font-bold' : 'text-slate-400 dark:text-slate-600 line-through font-medium transition-colors group-hover/item:text-slate-500'">
                                            {{ feature.text }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Comparison Table -->
            <div class="max-w-4xl mx-auto px-6 mb-40">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-4 uppercase">{{ t('full_comparison') }}</h2>
                    <p class="text-slate-500 font-medium">{{ t('comparison_subtitle') }}</p>
                </div>
                <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-50 dark:bg-slate-800/20 backdrop-blur-xl border border-black/5 dark:border-white/5 shadow-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/5">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">{{ t('feature') }}</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 text-center">{{ t('plan_free') }}</th>
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400 text-center">{{ t('plan_pro') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/5 font-medium">
                            <tr v-for="cf in comparisonFeatures" :key="cf.name" class="hover:bg-indigo-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-8 py-5 text-sm text-slate-600 dark:text-slate-400">{{ cf.name }}</td>
                                <td class="px-8 py-5 text-sm text-slate-500 text-center">{{ cf.free }}</td>
                                <td class="px-8 py-5 text-sm text-slate-900 dark:text-white text-center font-black">{{ cf.pro }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Benefit Pillars -->
            <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-12 text-center">
                <div v-for="benefit in [
                    { title: t('global_reach_title'), desc: t('global_reach_desc'), icon: '🌍' },
                    { title: t('ai_powered_title'), desc: t('ai_powered_desc'), icon: '🤖' },
                    { title: t('privacy_first_title'), desc: t('privacy_first_desc'), icon: '🔐' },
                ]" :key="benefit.title" class="group">
                    <div class="text-5xl mb-6 transition-transform group-hover:scale-125 duration-500">{{ benefit.icon }}</div>
                    <h3 class="text-xl font-black text-white mb-3 uppercase tracking-wider">{{ benefit.title }}</h3>
                    <p class="text-slate-500 leading-relaxed font-medium">{{ benefit.desc }}</p>
                </div>
            </div>

            <!-- Help CTA -->
            <div class="mt-40 text-center">
                <div class="max-w-2xl mx-auto p-12 rounded-[3.5rem] bg-gradient-to-b from-white/[0.03] to-transparent border border-white/5">
                    <h2 class="text-3xl font-black text-white mb-6 uppercase tracking-tight">{{ t('still_undecided') }}</h2>
                    <p class="text-slate-500 mb-10 font-medium">{{ t('join_pros') }}</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-6">
                        <div class="laser-btn-wrapper shadow-2xl shadow-indigo-600/30 active:scale-95 transition-all">
                            <Link :href="route('register')" class="laser-btn-content px-10 py-4 text-white font-black text-xs uppercase tracking-[0.2em]">{{ t('start_free_trial') }}</Link>
                        </div>
                        <div class="laser-btn-wrapper laser-silver active:scale-95 transition-all">
                            <Link href="/contact" class="laser-btn-content px-10 py-4 text-white font-black text-xs uppercase tracking-[0.2em]">{{ t('talk_to_support') }}</Link>
                        </div>
                    </div>
                </div>
                <div class="mt-16 flex flex-wrap justify-center gap-10 text-[10px] font-black uppercase tracking-[0.2em] text-slate-600">
                    <div class="flex items-center gap-2.5">
                         <div class="w-2 h-2 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></div>
                         {{ t('no_card_required') }}
                    </div>
                    <div class="flex items-center gap-2.5">
                         <div class="w-2 h-2 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></div>
                         {{ t('secure_payment') }}
                    </div>
                    <div class="flex items-center gap-2.5">
                         <div class="w-2 h-2 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/50"></div>
                         {{ t('money_back') }}
                    </div>
                </div>
            </div>
        </main>

        <!-- Premium Footer -->
        <footer class="relative z-10 pt-24 pb-16 px-6 border-t border-white/5 overflow-hidden">
             <!-- Ambient Glow -->
             <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full h-80 bg-indigo-600/5 blur-[120px] pointer-events-none"></div>
             
             <div class="max-w-7xl mx-auto">
                 <div class="flex flex-col md:flex-row items-center justify-between gap-12 mb-16">
                     <div class="flex items-center gap-3">
                         <img src="/images/logo.png" alt="Logo" class="w-10 h-10 object-contain" />
                         <span class="text-3xl font-black tracking-tighter text-white uppercase">CIVIO</span>
                     </div>
                      <div class="flex flex-wrap justify-center gap-10 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                         <Link href="/privacy" class="hover:text-white transition-colors">{{ t('privacy_policy') }}</Link>
                         <Link href="/terms" class="hover:text-white transition-colors">{{ t('terms_of_service') }}</Link>
                         <Link href="/about" class="hover:text-white transition-colors">{{ t('about_us') }}</Link>
                         <Link href="/contact" class="hover:text-white transition-colors">{{ t('contact_us') }}</Link>
                      </div>
                 </div>
                 <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
                     <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">
                         © {{ new Date().getFullYear() }} CIVIO GLOBAL. ALL RIGHTS RESERVED.
                     </p>
                     <div class="flex items-center gap-6">
                         <div class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center transition-colors cursor-pointer">
                             <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                         </div>
                         <div class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center transition-colors cursor-pointer">
                             <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                         </div>
                     </div>
                 </div>
             </div>
        </footer>
    </div>
</template>

<style scoped>
.selection\:bg-indigo-500\/30 ::selection {
    background-color: rgba(99, 102, 241, 0.3);
}

/* Smooth entry animations */
.animate-in {
    animation-fill-mode: both;
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-in-from-bottom-4 {
    from { transform: translateY(1rem); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slide-in-from-bottom-8 {
    from { transform: translateY(2rem); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes slide-in-from-bottom-12 {
    from { transform: translateY(3rem); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes zoom-in {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.fade-in { animation: fade-in 0.8s ease-out; }
.slide-in-from-bottom-4 { animation: slide-in-from-bottom-4 0.8s ease-out; }
.slide-in-from-bottom-8 { animation: slide-in-from-bottom-8 0.8s ease-out; }
.slide-in-from-bottom-12 { animation: slide-in-from-bottom-12 0.8s ease-out; }
.zoom-in { animation: zoom-in 0.6s ease-out; }

/* Custom Scrollbar for better UX */
::-webkit-scrollbar {
    width: 10px;
}
::-webkit-scrollbar-track {
    background: #0f172a;
}
::-webkit-scrollbar-thumb {
    background: #1e293b;
    border-radius: 5px;
}
::-webkit-scrollbar-thumb:hover {
    background: #334155;
}
</style>
