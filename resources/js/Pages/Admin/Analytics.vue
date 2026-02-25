<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    stats:          Object,
    monthlyRevenue: Array,   // last 6 months [{month: 'Jan', revenue: 120}]
    topTemplates:   Array,   // [{slug, name, count}]
    recentUsers:    Array,
});

// Calculate maxRevenue for chart scaling
const maxRevenue = computed(() => {
    if (!props.monthlyRevenue?.length) return 1;
    return Math.max(...props.monthlyRevenue.map(m => m.revenue), 1);
});

const barHeight = (val) => Math.max((val / maxRevenue.value) * 100, 4);
</script>

<template>
    <Head :title="t('analytics') + ' — Admin'" />

    <div class="min-h-screen bg-slate-900 text-white">
        <!-- Header -->
        <div class="bg-slate-800/60 border-b border-white/10 px-6 py-4 flex items-center gap-4">
            <Link :href="route('admin.dashboard')" class="text-slate-400 hover:text-white text-sm transition-colors">← {{ t('admin') }}</Link>
            <h1 class="text-xl font-bold text-white">{{ t('analytics') }}</h1>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">

            <!-- Primary SaaS KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-2xl p-5 shadow-lg shadow-indigo-500/5 transition-transform hover:scale-[1.02]">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-widest">Monthly Recurring Revenue (MRR)</p>
                        <div class="p-1.5 bg-indigo-500/20 rounded-lg text-indigo-400">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-12c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-12V6m0 12v2m-4-10a4 4 0 118 0a4 4 0 01-8 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-white mt-4">${{ Number(stats?.mrr ?? 0).toLocaleString() }}</p>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="text-[10px] text-emerald-400 font-bold">↑ 12%</span>
                        <span class="text-[10px] text-slate-500 font-medium">vs last month</span>
                    </div>
                </div>

                <div class="bg-slate-800/60 border border-white/10 rounded-2xl p-5 transition-transform hover:scale-[1.02]">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Annual Revenue (ARR)</p>
                    <p class="text-3xl font-bold text-white mt-4">${{ Number(stats?.arr ?? 0).toLocaleString() }}</p>
                    <p class="text-[10px] text-slate-500 mt-2 font-medium">Forward-looking projection</p>
                </div>

                <div class="bg-slate-800/60 border border-white/10 rounded-2xl p-5 transition-transform hover:scale-[1.02]">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Churn Rate</p>
                    <p class="text-3xl font-bold text-red-400 mt-4">{{ stats?.churn_rate ?? 0 }}%</p>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="text-[10px] text-red-400 font-bold">↓ 0.5%</span>
                        <span class="text-[10px] text-slate-500 font-medium">Revenue retention target: 95%</span>
                    </div>
                </div>

                <div class="bg-slate-800/60 border border-white/10 rounded-2xl p-5 transition-transform hover:scale-[1.02]">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Lifetime Value (LTV)</p>
                    <p class="text-3xl font-bold text-emerald-400 mt-4">${{ Number(stats?.ltv ?? 0).toFixed(2) }}</p>
                    <p class="text-[10px] text-slate-500 mt-2 font-medium">Avg revenue per active account</p>
                </div>
            </div>

            <!-- Secondary Metrics -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-slate-800/40 border border-white/5 rounded-xl p-4">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ t('total_users_kpi') }}</p>
                    <p class="text-xl font-bold text-white mt-1">{{ stats?.total_users ?? 0 }}</p>
                </div>
                <div class="bg-slate-800/40 border border-white/5 rounded-xl p-4">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ t('pro_users_kpi') }}</p>
                    <p class="text-xl font-bold text-indigo-400 mt-1">{{ stats?.pro_users ?? 0 }}</p>
                </div>
                <div class="bg-slate-800/40 border border-white/5 rounded-xl p-4">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ t('total_resumes_kpi') }}</p>
                    <p class="text-xl font-bold text-teal-400 mt-1">{{ stats?.total_resumes ?? 0 }}</p>
                </div>
                <div class="bg-slate-800/40 border border-white/5 rounded-xl p-4">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">{{ t('total_revenue_kpi') }}</p>
                    <p class="text-xl font-bold text-amber-400 mt-1">${{ Number(stats?.total_revenue ?? 0).toLocaleString() }}</p>
                </div>
            </div>

            <!-- Revenue chart + top templates side by side -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Revenue bar chart -->
                <div class="lg:col-span-2 bg-slate-800/60 border border-white/10 rounded-2xl p-6">
                    <h2 class="text-sm font-bold text-white mb-6">{{ t('monthly_revenue') }}</h2>
                    <div v-if="monthlyRevenue?.length" class="flex items-end gap-2 h-40">
                        <div v-for="m in monthlyRevenue" :key="m.month"
                            class="flex-1 flex flex-col items-center gap-1 group">
                            <span class="text-xs text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                ${{ m.revenue }}
                            </span>
                            <div class="w-full bg-indigo-600 hover:bg-indigo-500 rounded-t-lg transition-all duration-700 ease-out"
                                :style="{ height: barHeight(m.revenue) + '%' }">
                            </div>
                            <span class="text-xs text-slate-500">{{ m.month }}</span>
                        </div>
                    </div>
                    <div v-else class="h-40 flex items-center justify-center text-slate-600 text-sm">
                        {{ t('no_revenue_data') }}
                    </div>
                </div>

                <!-- Top templates -->
                <div class="bg-slate-800/60 border border-white/10 rounded-2xl p-6">
                    <h2 class="text-sm font-bold text-white mb-5">{{ t('top_templates') }}</h2>
                    <div v-if="topTemplates?.length" class="space-y-3">
                        <div v-for="(tpl, idx) in topTemplates" :key="tpl.slug" class="flex items-center gap-3">
                            <span class="text-xs text-slate-500 w-4 text-right">{{ idx + 1 }}</span>
                            <div class="flex-1">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-white font-medium">{{ tpl.name }}</span>
                                    <span class="text-slate-400">{{ tpl.count }}</span>
                                </div>
                                <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 rounded-full"
                                        :style="{ width: (tpl.count / (topTemplates[0]?.count || 1) * 100) + '%' }">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-slate-600 text-sm">{{ t('no_data_yet') }}</div>
                </div>
            </div>

            <!-- Recent users -->
            <div class="bg-slate-800/60 border border-white/10 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <h2 class="text-sm font-bold text-white">{{ t('recent_users') }}</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 text-xs uppercase tracking-wider border-b border-white/5">
                            <th class="px-5 py-3 font-medium">{{ t('user') }}</th>
                            <th class="px-5 py-3 font-medium">{{ t('plan') }}</th>
                            <th class="px-5 py-3 font-medium">{{ t('resumes') }}</th>
                            <th class="px-5 py-3 font-medium">{{ t('joined') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in recentUsers" :key="user.id"
                            class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-indigo-600/30 flex items-center justify-center text-xs font-bold text-indigo-300">
                                        {{ user.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-white text-xs">{{ user.name }}</div>
                                        <div class="text-slate-500 text-xs">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span :class="user.plan === 'pro' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-700 text-slate-400'"
                                    class="px-2 py-0.5 rounded-full text-xs font-medium uppercase">
                                    {{ user.plan === 'pro' ? t('pro') : t('free') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-400 text-xs">{{ user.resumes_count ?? 0 }}</td>
                            <td class="px-5 py-3.5 text-slate-500 text-xs">
                                {{ user.created_at ? new Date(user.created_at).toLocaleDateString() : '—' }}
                            </td>
                        </tr>
                        <tr v-if="!recentUsers?.length">
                            <td colspan="4" class="px-5 py-8 text-center text-slate-600 italic">{{ t('no_users_data') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
