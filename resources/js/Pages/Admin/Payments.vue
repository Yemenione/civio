<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
import { useNotify } from '@/composables/useNotify';

const notify = useNotify();

const props = defineProps({ payments: Object });

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const refund = (id) => {
    notify.confirm(
        t('refund_payment') || 'Refund Payment?',
        t('refund_confirm') || 'Are you sure you want to refund this payment?',
        () => router.patch(route('admin.payments.refund', id))
    );
};

const exportCsv = () => {
    window.location.href = route('admin.payments.export');
};
</script>

<template>
    <Head :title="'Admin — ' + t('payment_transactions')" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h1 class="text-white font-bold text-xl">{{ t('payment_transactions') }}</h1>
                <div class="laser-btn-wrapper laser-silver shadow-lg shadow-white/5 active:scale-95 transition-transform hover:scale-105">
                    <button @click="exportCsv" class="laser-btn-content px-4 py-2 text-white flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span class="text-xs font-black uppercase tracking-[0.1em]">{{ t('export_csv') }}</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="p-8">
            <div class="bg-slate-800/50 border border-white/10 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                                <th class="text-left px-5 py-3">{{ t('transaction_id') }}</th>
                                <th class="text-left px-5 py-3">{{ t('user') }}</th>
                                <th class="text-left px-5 py-3">{{ t('plan') }}</th>
                                <th class="text-left px-5 py-3">{{ t('amount') }}</th>
                                <th class="text-left px-5 py-3">{{ t('method') }}</th>
                                <th class="text-left px-5 py-3">{{ t('status') }}</th>
                                <th class="text-left px-5 py-3">{{ t('date') }}</th>
                                <th class="text-right px-5 py-3">{{ t('actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-white/5 transition-colors">
                                <td class="px-5 py-4 text-slate-400 font-mono text-xs">
                                    {{ payment.transaction_id ?? '#' + payment.id }}
                                </td>
                                <td class="px-5 py-4 text-white">{{ payment.user?.name ?? t('unknown') }}</td>
                                <td class="px-5 py-4">
                                    <span class="bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-full text-xs capitalize">{{ payment.plan }}</span>
                                </td>
                                <td class="px-5 py-4 text-green-400 font-medium">{{ formatCurrency(payment.amount) }}</td>
                                <td class="px-5 py-4 text-slate-400 capitalize">{{ payment.payment_method ?? t('manual') }}</td>
                                <td class="px-5 py-4">
                                    <span :class="{
                                        'bg-green-500/20 text-green-300': payment.status === 'paid',
                                        'bg-yellow-500/20 text-yellow-300': payment.status === 'pending',
                                        'bg-red-500/20 text-red-300': payment.status === 'failed',
                                        'bg-slate-500/20 text-slate-400': payment.status === 'refunded',
                                    }" class="px-2 py-0.5 rounded-full text-xs capitalize">
                                        {{ payment.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-400 text-xs">
                                    {{ new Date(payment.created_at).toLocaleDateString() }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <SecondaryButton 
                                        v-if="payment.status === 'paid'"
                                        @click="refund(payment.id)"
                                        :is-laser="true"
                                        class="scale-90 transform origin-right text-yellow-400 border-yellow-500/30 font-black uppercase tracking-[0.1em]"
                                    >
                                        {{ t('refund') }}
                                    </SecondaryButton>
                                    <span v-else class="text-slate-600 text-xs">—</span>
                                </td>
                            </tr>
                            <tr v-if="!payments.data?.length">
                                <td colspan="8" class="px-5 py-12 text-center text-slate-500">{{ t('no_payments_records') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-white/10 flex gap-2 flex-wrap" v-if="payments.links">
                    <template v-for="(link, k) in payments.links" :key="k">
                        <component
                            :is="link.url ? 'a' : 'span'"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1.5 text-xs rounded-lg border transition-colors"
                            :class="{
                                'bg-indigo-600 border-indigo-500 text-white': link.active,
                                'border-slate-600 text-slate-400 hover:border-slate-400 cursor-pointer': link.url && !link.active,
                                'border-slate-700 text-slate-600 cursor-not-allowed': !link.url
                            }"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
