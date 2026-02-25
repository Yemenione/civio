<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
import { useNotify } from '@/composables/useNotify';

const notify = useNotify();

const props = defineProps({
    stats: Object,
    users: Object,
    recentPayments: Array,
});

const deleteUser = (id) => {
    notify.confirm(
        t('delete_user') || 'Delete User?',
        t('delete_user_confirm') || 'Are you sure you want to delete this user?',
        () => router.delete(route('admin.users.destroy', id))
    );
};

const toggleRole = (id) => {
    router.put(route('admin.users.toggleRole', id));
};

const toggleActive = (id) => {
    router.put(route('admin.users.toggleActive', id));
};

const extendSubscription = (id) => {
    router.patch(route('admin.subscriptions.extend', id));
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
    <Head :title="'Admin — ' + t('dashboard')" />
    <AdminLayout>
        <template #header>
            <h1 class="text-slate-900 dark:text-white font-bold text-xl">{{ t('overview') }}</h1>
        </template>

        <div class="p-8 space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800/50 border border-black/5 dark:border-white/10 rounded-xl p-5 shadow-sm">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">{{ t('total_users') }}</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white mt-2">{{ stats.total_users }}</p>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">+{{ stats.new_users_today }} {{ t('new_today') }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800/50 border border-black/5 dark:border-white/10 rounded-xl p-5 shadow-sm">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">{{ t('pro_subscribers') }}</p>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-2">{{ stats.pro_users }}</p>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">{{ stats.total_users > 0 ? Math.round(stats.pro_users / stats.total_users * 100) : 0 }}% {{ t('conversion') }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800/50 border border-black/5 dark:border-white/10 rounded-xl p-5 shadow-sm">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">{{ t('total_resumes') }}</p>
                    <p class="text-3xl font-bold text-teal-600 dark:text-teal-400 mt-2">{{ stats.total_resumes }}</p>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">{{ t('across_all_users') }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800/50 border border-black/5 dark:border-white/10 rounded-xl p-5 shadow-sm">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">{{ t('total_revenue') }}</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-green-400 mt-2">{{ formatCurrency(stats.total_revenue) }}</p>
                    <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">{{ t('all_time') }}</p>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white dark:bg-slate-800/50 border border-black/5 dark:border-white/10 rounded-xl overflow-hidden shadow-sm">
                <div class="p-5 border-b border-black/5 dark:border-white/10 flex justify-between items-center">
                    <h2 class="text-slate-900 dark:text-white font-semibold">{{ t('recent_payments_title') }}</h2>
                    <Link :href="route('admin.payments.index')" class="text-indigo-600 dark:text-indigo-400 text-sm hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">{{ t('view_all') }} →</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-black/5 dark:border-white/5">
                                <th class="text-left px-5 py-3">{{ t('user') }}</th>
                                <th class="text-left px-5 py-3">{{ t('plan') }}</th>
                                <th class="text-left px-5 py-3">{{ t('amount') }}</th>
                                <th class="text-left px-5 py-3">{{ t('status') }}</th>
                                <th class="text-left px-5 py-3">{{ t('date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/5">
                            <tr v-for="payment in recentPayments" :key="payment.id" class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-5 py-4 text-slate-900 dark:text-white">{{ payment.user?.name ?? 'Unknown' }}</td>
                                <td class="px-5 py-4">
                                    <span class="bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-full text-xs capitalize">{{ payment.plan }}</span>
                                </td>
                                <td class="px-5 py-4 text-emerald-600 dark:text-green-400 font-medium">{{ formatCurrency(payment.amount) }}</td>
                                <td class="px-5 py-4">
                                    <span :class="{
                                        'bg-green-500/20 text-green-300': payment.status === 'paid',
                                        'bg-yellow-500/20 text-yellow-300': payment.status === 'pending',
                                        'bg-red-500/20 text-red-300': payment.status === 'failed',
                                        'bg-slate-500/20 text-slate-300': payment.status === 'refunded',
                                    }" class="px-2 py-0.5 rounded-full text-xs capitalize">
                                        {{ payment.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-400">{{ payment.created_at ? new Date(payment.created_at).toLocaleDateString() : '—' }}</td>
                            </tr>
                            <tr v-if="!recentPayments?.length">
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500">{{ t('no_payments_yet') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-slate-800/50 border border-white/10 rounded-xl overflow-hidden">
                <div class="p-5 border-b border-white/10 flex justify-between items-center">
                    <h2 class="text-white font-semibold">{{ t('recent_users') }}</h2>
                    <Link :href="route('admin.users.index')" class="text-indigo-400 text-sm hover:text-indigo-300 transition-colors">{{ t('view_all') }} →</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                                <th class="text-left px-5 py-3">{{ t('fullname') }}</th>
                                <th class="text-left px-5 py-3">{{ t('account_status') }}</th>
                                <th class="text-left px-5 py-3">{{ t('role') }}</th>
                                <th class="text-left px-5 py-3">{{ t('plan') }}</th>
                                <th class="text-right px-5 py-3">{{ t('actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-white/5 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="text-white font-medium">{{ user.name }}</div>
                                    <div class="text-slate-500 text-[10px]">{{ user.email }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <button
                                        @click="toggleActive(user.id)"
                                        :class="user.is_active ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold transition-all hover:scale-105 active:scale-95"
                                    >
                                        {{ user.is_active ? t('active') : t('disabled') }}
                                    </button>
                                </td>
                                <td class="px-5 py-4">
                                    <span :class="user.role === 'admin' ? 'bg-red-500/20 text-red-300' : 'bg-slate-600/50 text-slate-300'" class="px-2 py-0.5 rounded-full text-xs capitalize">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span :class="user.plan === 'pro' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-600/50 text-slate-400'" class="px-2 py-0.5 rounded-full text-xs capitalize">
                                        {{ user.plan ?? 'free' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right flex items-center justify-end gap-2 flex-wrap max-w-[200px]">
                                    <button 
                                        v-if="user.plan === 'pro'"
                                        @click="extendSubscription(user.id)"
                                        class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 px-2 py-1 rounded-md text-[10px] uppercase font-black tracking-widest hover:bg-indigo-600 hover:text-white transition-all scale-90"
                                    >
                                        {{ t('extend_subscription') }}
                                    </button>
                                    <button 
                                        @click="toggleRole(user.id)"
                                        class="bg-slate-700 text-slate-300 px-2 py-1 rounded-md text-[10px] uppercase font-black tracking-widest hover:bg-slate-600 transition-all text-nowrap scale-90"
                                    >
                                        {{ t('toggle_role') }}
                                    </button>
                                    <button 
                                        @click="deleteUser(user.id)"
                                        class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white px-2 py-1 rounded-md text-[10px] uppercase font-black tracking-widest transition-all scale-90"
                                    >
                                        {{ t('delete') }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
