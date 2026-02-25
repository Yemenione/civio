<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useNotify } from '@/composables/useNotify';
import PremiumToast from '@/Components/PremiumToast.vue';
import PremiumAlert from '@/Components/PremiumAlert.vue';
import PremiumConfirm from '@/Components/PremiumConfirm.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const { t } = useI18n();
const notify = useNotify();

const props = defineProps({
    plans: Array,
});

const showModal   = ref(false);
const editingId   = ref(null);
const flashMsg    = ref('');

const form = useForm({
    name:                    '',
    slug:                    '',
    description:             '',
    price_monthly:           0,
    price_yearly:            0,
    stripe_price_id_monthly: '',
    stripe_price_id_yearly:  '',
    features:                {},
    is_active:               true,
    is_popular:              false,
    sort_order:              0,
});

const openCreate = () => {
    editingId.value = null;
    form.reset();
    showModal.value = true;
};

const openEdit = (plan) => {
    editingId.value = plan.id;
    form.name                    = plan.name;
    form.slug                    = plan.slug;
    form.description             = plan.description ?? '';
    form.price_monthly           = plan.price_monthly;
    form.price_yearly            = plan.price_yearly;
    form.stripe_price_id_monthly = plan.stripe_price_id_monthly ?? '';
    form.stripe_price_id_yearly  = plan.stripe_price_id_yearly ?? '';
    form.features                = plan.features ?? {};
    form.is_active               = plan.is_active;
    form.is_popular              = plan.is_popular;
    form.sort_order              = plan.sort_order;
    showModal.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.put(route('admin.plans.update', editingId.value), {
            onSuccess: () => { showModal.value = false; flashMsg.value = 'Plan updated'; setTimeout(() => flashMsg.value = '', 3000); },
        });
    } else {
        form.post(route('admin.plans.store'), {
            onSuccess: () => { showModal.value = false; flashMsg.value = 'Plan created'; setTimeout(() => flashMsg.value = '', 3000); },
        });
    }
};

const toggleActive = (plan) => {
    router.patch(route('admin.plans.toggle', plan.id));
};

const destroy = (plan) => {
    notify.confirm(
        'Delete Plan?',
        `Are you sure you want to delete the plan "${plan.name}"?`,
        () => router.delete(route('admin.plans.destroy', plan.id))
    );
};
</script>

<template>
    <Head title="Plan Management — Admin" />

    <div class="min-h-screen bg-slate-900 text-white">
        <!-- Header -->
        <div class="bg-slate-800/60 border-b border-white/10 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.dashboard')" class="text-slate-400 hover:text-white text-sm transition-colors">
                    ← {{ t('admin') }}
                </Link>
                <h1 class="text-xl font-bold text-white">Plan Management</h1>
            </div>
            <div class="laser-btn-wrapper shadow-lg shadow-indigo-500/20 active:scale-95 transition-transform hover:scale-105">
                <button @click="openCreate" class="laser-btn-content px-4 py-2 text-white">
                    <span class="text-xs font-black uppercase tracking-[0.1em]">New Plan</span>
                </button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Flash -->
            <div v-if="flashMsg" class="mb-6 px-4 py-3 bg-green-500/15 border border-green-500/30 rounded-xl text-green-400 text-sm">
                ✓ {{ flashMsg }}
            </div>

            <!-- Table -->
            <div class="bg-slate-800/50 backdrop-blur border border-white/10 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-400 border-b border-white/10 text-xs uppercase tracking-wider">
                            <th class="px-5 py-4 font-medium">#</th>
                            <th class="px-5 py-4 font-medium">Name</th>
                            <th class="px-5 py-4 font-medium">Pricing (M/Y)</th>
                            <th class="px-5 py-4 font-medium">Stripe IDs</th>
                            <th class="px-5 py-4 font-medium">Popular</th>
                            <th class="px-5 py-4 font-medium">Status</th>
                            <th class="px-5 py-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="plan in plans" :key="plan.id"
                            class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4 text-slate-500">{{ plan.sort_order }}</td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ plan.name }}</div>
                                <div class="text-slate-500 text-xs mt-0.5">{{ plan.slug }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-white font-mono">${{ plan.price_monthly }} / ${{ plan.price_yearly }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-slate-500 text-[10px] font-mono truncate max-w-[150px]">{{ plan.stripe_price_id_monthly || 'N/A' }}</div>
                                <div class="text-slate-500 text-[10px] font-mono truncate max-w-[150px]">{{ plan.stripe_price_id_yearly || 'N/A' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span v-if="plan.is_popular" class="px-2 py-0.5 bg-indigo-500/20 text-indigo-400 rounded-full text-[10px] font-bold uppercase">
                                    Popular
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="laser-btn-wrapper scale-90" :class="plan.is_active ? 'shadow-green-500/20' : 'laser-silver opacity-50'">
                                    <button @click="toggleActive(plan)"
                                        class="laser-btn-content px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.1em] text-white">
                                        {{ plan.is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <SecondaryButton @click="openEdit(plan)" :is-laser="true" class="scale-90 transform origin-right">{{ t('edit') }}</SecondaryButton>
                                    <DangerButton @click="destroy(plan)" :is-laser="true" class="scale-90 transform origin-right">{{ t('delete') }}</DangerButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="plans.length === 0">
                            <td colspan="7" class="px-5 py-10 text-center text-slate-600 italic">No plans defined yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="w-full max-w-lg bg-slate-800 border border-white/10 rounded-2xl shadow-2xl overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <h2 class="text-lg font-bold text-white">{{ editingId ? 'Edit Plan' : 'Add Plan' }}</h2>
                    <button @click="showModal = false" class="text-slate-500 hover:text-white text-xl leading-none">✕</button>
                </div>

                <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Name *</label>
                            <input v-model="form.name" required type="text" placeholder="e.g. Professional"
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Slug *</label>
                            <input v-model="form.slug" required type="text" placeholder="e.g. pro"
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-medium mb-1 block">Monthly Price ($) *</label>
                        <input v-model.number="form.price_monthly" required type="number" step="0.01"
                            class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-medium mb-1 block">Yearly Price ($) *</label>
                        <input v-model.number="form.price_yearly" required type="number" step="0.01"
                            class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Stripe Monthly Price ID</label>
                            <input v-model="form.stripe_price_id_monthly" type="text" placeholder="price_..."
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Stripe Yearly Price ID</label>
                            <input v-model="form.stripe_price_id_yearly" type="text" placeholder="price_..."
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono" />
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <input v-model="form.is_popular" type="checkbox" id="is_popular"
                                class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-indigo-600 focus:ring-indigo-500" />
                            <label for="is_popular" class="text-sm text-slate-300">Popular</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input v-model="form.is_active" type="checkbox" id="is_active"
                                class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-indigo-600 focus:ring-indigo-500" />
                            <label for="is_active" class="text-sm text-slate-300">Active</label>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-medium mb-1 block">Sort Order</label>
                        <input v-model.number="form.sort_order" type="number" min="0"
                            class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-white/10">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                            {{ t('cancel') }}
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                            {{ form.processing ? t('saving') : (editingId ? 'Update Plan' : 'Create Plan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Global Notifications -->
        <PremiumToast />
        <PremiumAlert />
        <PremiumConfirm />
    </div>
</template>
