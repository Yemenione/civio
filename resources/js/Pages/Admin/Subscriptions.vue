<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
import { useNotify } from '@/composables/useNotify';

const notify = useNotify();

const props = defineProps({ subscriptions: Object });

const extend = (userId) => {
    notify.confirm(
        t('extend_30_days') || 'Extend Subscription?',
        t('extend_30_days_confirm') || 'Are you sure you want to extend this user\'s subscription by 30 days?',
        () => router.patch(route('admin.subscriptions.extend', userId))
    );
};

const cancel = (userId) => {
    notify.confirm(
        t('cancel_subscription') || 'Cancel Subscription?',
        t('cancel_sub_confirm') || 'Are you sure you want to cancel this user\'s subscription?',
        () => router.delete(route('admin.subscriptions.cancel', userId))
    );
};
</script>

<template>
    <Head :title="'Admin — ' + t('subscriptions')" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h1 class="text-white font-bold text-xl">{{ t('subscriptions') }}</h1>
                <div class="text-slate-400 text-sm">{{ subscriptions.total }} {{ t('total') }}</div>
            </div>
        </template>

        <div class="p-8">
            <div class="bg-slate-800/50 border border-white/10 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-400 text-xs uppercase tracking-wider border-b border-white/5">
                                <th class="text-left px-5 py-3">{{ t('user') }}</th>
                                <th class="text-left px-5 py-3">{{ t('plan') }}</th>
                                <th class="text-left px-5 py-3">{{ t('status') }}</th>
                                <th class="text-left px-5 py-3">{{ t('expires') }}</th>
                                <th class="text-right px-5 py-3">{{ t('actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <tr v-for="user in subscriptions.data" :key="user.id" class="hover:bg-white/5 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="text-white font-medium">{{ user.name }}</div>
                                            <div class="text-slate-500 text-xs">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="bg-indigo-500/20 text-indigo-300 px-2 py-0.5 rounded-full text-xs capitalize">{{ user.plan }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span :class="user.subscription_ends_at && new Date(user.subscription_ends_at) > new Date()
                                        ? 'bg-green-500/20 text-green-300'
                                        : 'bg-red-500/20 text-red-300'"
                                        class="px-2 py-0.5 rounded-full text-xs">
                                        {{ user.subscription_ends_at && new Date(user.subscription_ends_at) > new Date() ? t('active') : t('expired') }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-400 text-xs">
                                    {{ user.subscription_ends_at ? new Date(user.subscription_ends_at).toLocaleDateString() : '—' }}
                                </td>
                                <td class="px-5 py-4 text-right flex items-center justify-end gap-3">
                                    <SecondaryButton @click="extend(user.id)" :is-laser="true" class="scale-90 transform origin-right text-green-400 border-green-500/30">{{ t('add_30_days') }}</SecondaryButton>
                                    <DangerButton @click="cancel(user.id)" :is-laser="true" class="scale-90 transform origin-right">{{ t('cancel') }}</DangerButton>
                                </td>
                            </tr>
                            <tr v-if="!subscriptions.data?.length">
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500">{{ t('no_subscriptions') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-white/10 flex gap-2 flex-wrap" v-if="subscriptions.links">
                    <template v-for="(link, k) in subscriptions.links" :key="k">
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
