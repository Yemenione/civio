<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
import { useNotify } from '@/composables/useNotify';

const notify = useNotify();

const props = defineProps({ users: Object });

const search = ref('');

const deleteUser = (id) => {
    notify.confirm(
        t('delete_user') || 'Delete User?',
        t('delete_user_permanently') || 'Are you sure you want to permanently delete this user?',
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

const changePlan = (userId, plan) => {
    router.put(route('admin.users.changePlan', userId), { plan });
};

const impersonate = (user) => {
    router.post(route('admin.users.impersonate', user.id));
};
</script>

<template>
    <Head :title="'Admin — ' + t('users')" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h1 class="text-slate-900 dark:text-white font-bold text-xl">{{ t('user_management') }}</h1>
                <div class="text-slate-500 dark:text-slate-400 text-sm">{{ users.total }} {{ t('total_users') }}</div>
            </div>
        </template>

        <div class="p-8">
            <div class="bg-white dark:bg-slate-800/50 border border-black/5 dark:border-white/10 rounded-xl overflow-hidden shadow-sm">
                <!-- Search / Filter Bar -->
                <div class="p-4 border-b border-black/5 dark:border-white/10 flex gap-4">
                    <input
                        v-model="search"
                        type="text"
                        :placeholder="t('search_placeholder')"
                        class="flex-1 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-slate-400 dark:placeholder-slate-500"
                    />
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider border-b border-black/5 dark:border-white/5">
                                <th class="text-left px-5 py-3">{{ t('user') }}</th>
                                <th class="text-left px-5 py-3">{{ t('role') }}</th>
                                <th class="text-left px-5 py-3">{{ t('account_status') }}</th>
                                <th class="text-left px-5 py-3">{{ t('plan') }}</th>
                                <th class="text-left px-5 py-3">{{ t('expires') }}</th>
                                <th class="text-left px-5 py-3">{{ t('resumes_count') }}</th>
                                <th class="text-right px-5 py-3">{{ t('actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5 dark:divide-white/5">
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                v-show="!search || user.name.toLowerCase().includes(search.toLowerCase()) || user.email.toLowerCase().includes(search.toLowerCase())"
                                class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="text-slate-900 dark:text-white font-medium">{{ user.name }}</div>
                                            <div class="text-slate-500 text-xs">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span :class="user.role === 'admin' ? 'bg-red-500/10 text-red-600 dark:bg-red-500/20 dark:text-red-300' : 'bg-slate-100 dark:bg-slate-600/50 text-slate-600 dark:text-slate-300'" class="px-2 py-0.5 rounded-full text-xs capitalize">
                                        {{ user.role ?? 'user' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <button
                                        @click="toggleActive(user.id)"
                                        :class="user.is_active ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'"
                                        class="px-2 py-0.5 rounded-full text-xs font-bold transition-all hover:scale-105 active:scale-95"
                                    >
                                        {{ user.is_active ? t('active') : t('disabled') }}
                                    </button>
                                </td>
                                <td class="px-5 py-4">
                                    <select
                                        :value="user.plan ?? 'free'"
                                        @change="changePlan(user.id, $event.target.value)"
                                        class="bg-slate-700 border border-slate-600 text-white text-xs rounded-lg px-2 py-1"
                                    >
                                        <option value="free">{{ t('free') }}</option>
                                        <option value="pro">{{ t('pro') }}</option>
                                    </select>
                                </td>
                                <td class="px-5 py-4 text-slate-400 text-xs">
                                    <div v-if="user.plan === 'pro'">
                                        {{ user.subscription_ends_at ? new Date(user.subscription_ends_at).toLocaleDateString() : '—' }}
                                    </div>
                                    <div v-else>—</div>
                                </td>
                                <td class="px-5 py-4 text-slate-300">
                                    {{ user.resumes_count ?? 0 }}
                                </td>
                                <td class="px-5 py-4 text-right flex items-center justify-end gap-2 flex-wrap max-w-[200px]">
                                    <button 
                                        v-if="user.plan === 'pro'"
                                        @click="extendSubscription(user.id)"
                                        class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 px-2 py-1 rounded-md text-[10px] uppercase font-black tracking-widest hover:bg-indigo-600 hover:text-white transition-all"
                                    >
                                        {{ t('extend_subscription') }}
                                    </button>
                                    <button 
                                        @click="toggleRole(user.id)"
                                        class="bg-slate-700 text-slate-300 px-2 py-1 rounded-md text-[10px] uppercase font-black tracking-widest hover:bg-slate-600 transition-all text-nowrap"
                                    >
                                        {{ t('toggle_role') }}
                                    </button>
                                    <button 
                                        @click="deleteUser(user.id)"
                                        class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white px-2 py-1 rounded-md text-[10px] uppercase font-black tracking-widest transition-all"
                                    >
                                        {{ t('delete') }}
                                    </button>
                                    <button 
                                        v-if="user.role !== 'admin'"
                                        @click="impersonate(user)"
                                        class="bg-amber-500/20 text-amber-500 border border-amber-500/30 px-2 py-1 rounded-md text-[10px] uppercase font-black tracking-widest hover:bg-amber-500 hover:text-white transition-all text-nowrap"
                                    >
                                        {{ t('impersonate') || 'Impersonate' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-white/10 flex gap-2 flex-wrap" v-if="users.links">
                    <template v-for="(link, k) in users.links" :key="k">
                        <component
                            :is="link.url ? 'a' : 'span'"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1.5 text-xs rounded-lg border transition-colors"
                            :class="{
                                'bg-indigo-600 border-indigo-500 text-white': link.active,
                                'border-slate-600 text-slate-400 hover:border-slate-400 hover:text-slate-200 cursor-pointer': link.url && !link.active,
                                'border-slate-700 text-slate-600 cursor-not-allowed': !link.url
                            }"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
