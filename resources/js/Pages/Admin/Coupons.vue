<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { useNotify } from '@/composables/useNotify';

const { t } = useI18n();
const notify = useNotify();

const props = defineProps({
    coupons: Object,
});

const form = useForm({
    code: '',
    discount_percent: '',
    max_uses: 100,
    expires_at: '',
});

const submit = () => {
    form.post(route('admin.coupons.store'), {
        onSuccess: () => form.reset(),
    });
};

const deleteCoupon = (id) => {
    notify.confirm(
        t('delete_coupon') || 'Delete Coupon?',
        t('are_you_sure') || 'Are you sure you want to delete this coupon?',
        () => form.delete(route('admin.coupons.destroy', id))
    );
};
</script>

<template>
    <Head :title="t('coupon_management')" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ t('coupons_discounts') }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Create Coupon -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-slate-200">
                    <div class="p-8">
                        <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                            <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                            {{ t('generate_new_promo') }}
                        </h3>
                        
                        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ t('coupon_code') }}</label>
                                <input v-model="form.code" type="text" placeholder="SUMMER50" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm uppercase font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ t('discount_percent') }}</label>
                                <input v-model="form.discount_percent" type="number" placeholder="50" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ t('max_uses') }}</label>
                                <input v-model="form.max_uses" type="number" placeholder="100" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2.5 text-sm" />
                            </div>
                            <div>
                                <button type="submit" :disabled="form.processing" class="w-full bg-slate-900 text-white rounded-xl py-3 text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all active:scale-95 disabled:opacity-50">
                                    {{ t('create_coupon') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Coupons List -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-slate-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">{{ t('code') }}</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">{{ t('discount') }}</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">{{ t('usage') }}</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">{{ t('status') }}</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 text-right">{{ t('actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 italic">
                            <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-1 rounded text-sm uppercase leading-none">{{ coupon.code }}</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="text-indigo-600 font-black">{{ coupon.discount_percent }}% {{ t('off') }}</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="text-xs font-bold text-slate-600">{{ coupon.used_count }} / {{ coupon.max_uses }}</span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span v-if="coupon.is_active" class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase">{{ t('active') }}</span>
                                    <span v-else class="px-2 py-1 bg-rose-100 text-rose-700 rounded-full text-[10px] font-black uppercase">{{ t('expired') }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <button @click="deleteCoupon(coupon.id)" class="text-xs font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition-colors">{{ t('delete') }}</button>
                                </td>
                            </tr>
                            <tr v-if="coupons.data.length === 0">
                                <td colspan="5" class="px-8 py-10 text-center text-slate-400 italic">{{ t('no_coupons') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
