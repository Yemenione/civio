<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useNotify } from '@/composables/useNotify';
import PremiumToast from '@/Components/PremiumToast.vue';
import PremiumAlert from '@/Components/PremiumAlert.vue';
import PremiumConfirm from '@/Components/PremiumConfirm.vue';

const { t } = useI18n();
const notify = useNotify();

const props = defineProps({
    templates: Array,
});

const showModal   = ref(false);
const editingId   = ref(null);
const flashMsg    = ref('');

const form = useForm({
    name:                '',
    slug:                '',
    component:           '',
    description:         '',
    category:            'standard',
    is_premium:          false,
    sort_order:          0,
    supported_languages: [],
});

const categories = ['standard', 'rtl', 'creative'];
const langs = ['en','ar','fr','es','de','it','pt','nl'];

const openCreate = () => {
    editingId.value = null;
    form.reset();
    showModal.value = true;
};

const openEdit = (tpl) => {
    editingId.value = tpl.id;
    form.name                = tpl.name;
    form.slug                = tpl.slug;
    form.component           = tpl.component;
    form.description         = tpl.description ?? '';
    form.category            = tpl.category;
    form.is_premium          = tpl.is_premium;
    form.sort_order          = tpl.sort_order;
    form.supported_languages = tpl.supported_languages ?? [];
    showModal.value = true;
};

const toggleLang = (code) => {
    const idx = form.supported_languages.indexOf(code);
    if (idx === -1) form.supported_languages.push(code);
    else form.supported_languages.splice(idx, 1);
};

const submit = () => {
    if (editingId.value) {
        form.put(route('admin.templates.update', editingId.value), {
            onSuccess: () => { showModal.value = false; flashMsg.value = t('template_updated'); setTimeout(() => flashMsg.value = '', 3000); },
        });
    } else {
        form.post(route('admin.templates.store'), {
            onSuccess: () => { showModal.value = false; flashMsg.value = t('template_created'); setTimeout(() => flashMsg.value = '', 3000); },
        });
    }
};

const toggleActive = (tpl) => {
    router.patch(route('admin.templates.toggle', tpl.id));
};

const destroy = (tpl) => {
    notify.confirm(
        t('delete_template') || 'Delete Template?',
        t('delete_tpl_confirm', { name: tpl.name }),
        () => router.delete(route('admin.templates.destroy', tpl.id))
    );
};
</script>

<template>
    <Head :title="t('template_management') + '' + ' — Admin'" />

    <div class="min-h-screen bg-slate-900 text-white">
        <!-- Header -->
        <div class="bg-slate-800/60 border-b border-white/10 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.dashboard')" class="text-slate-400 hover:text-white text-sm transition-colors">
                    ← {{ t('admin') }}
                </Link>
                <h1 class="text-xl font-bold text-white">{{ t('template_management') }}</h1>
            </div>
            <div class="laser-btn-wrapper shadow-lg shadow-indigo-500/20 active:scale-95 transition-transform hover:scale-105">
                <button @click="openCreate" class="laser-btn-content px-4 py-2 text-white">
                    <span class="text-xs font-black uppercase tracking-[0.1em]">{{ t('new_template') }}</span>
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
                            <th class="px-5 py-4 font-medium">{{ t('name') }}</th>
                            <th class="px-5 py-4 font-medium">{{ t('slug_component') }}</th>
                            <th class="px-5 py-4 font-medium">{{ t('category') }}</th>
                            <th class="px-5 py-4 font-medium">{{ t('plan') }}</th>
                            <th class="px-5 py-4 font-medium">{{ t('languages') }}</th>
                            <th class="px-5 py-4 font-medium">{{ t('status') }}</th>
                            <th class="px-5 py-4 font-medium text-right">{{ t('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="tpl in templates" :key="tpl.id"
                            class="border-b border-white/5 hover:bg-white/5 transition-colors">
                            <td class="px-5 py-4 text-slate-500">{{ tpl.sort_order }}</td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ tpl.name }}</div>
                                <div class="text-slate-500 text-xs mt-0.5 truncate max-w-[180px]">{{ tpl.description }}</div>
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-400">
                                <div>{{ tpl.slug }}</div>
                                <div class="text-slate-600 mt-0.5">{{ tpl.component }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span :class="{
                                    'bg-blue-500/20 text-blue-300': tpl.category === 'standard',
                                    'bg-teal-500/20 text-teal-300': tpl.category === 'rtl',
                                    'bg-purple-500/20 text-purple-300': tpl.category === 'creative',
                                }" class="px-2 py-0.5 rounded-full text-xs font-medium uppercase">
                                    {{ t(tpl.category) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span :class="tpl.is_premium ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-700 text-slate-400'"
                                    class="px-2 py-0.5 rounded-full text-xs font-medium uppercase">
                                    {{ tpl.is_premium ? t('pro') : t('free') }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="lang in (tpl.supported_languages ?? [])" :key="lang"
                                        class="px-1.5 py-0.5 bg-slate-700 text-slate-400 rounded text-xs font-mono">
                                        {{ lang }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="laser-btn-wrapper scale-90" :class="tpl.is_active ? 'shadow-green-500/20' : 'laser-silver opacity-50'">
                                    <button @click="toggleActive(tpl)"
                                        class="laser-btn-content px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.1em] text-white">
                                        {{ tpl.is_active ? t('active') : t('hidden') }}
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <SecondaryButton @click="openEdit(tpl)" :is-laser="true" class="scale-90 transform origin-right">{{ t('edit') }}</SecondaryButton>
                                    <DangerButton @click="destroy(tpl)" :is-laser="true" class="scale-90 transform origin-right">{{ t('delete') }}</DangerButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="templates.length === 0">
                            <td colspan="8" class="px-5 py-10 text-center text-slate-600 italic">{{ t('no_templates_yet') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="w-full max-w-lg bg-slate-800 border border-white/10 rounded-2xl shadow-2xl overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
                    <h2 class="text-lg font-bold text-white">{{ editingId ? t('edit_template') : t('add_template') }}</h2>
                    <button @click="showModal = false" class="text-slate-500 hover:text-white text-xl leading-none">✕</button>
                </div>

                <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Name *</label>
                            <input v-model="form.name" required type="text" placeholder="e.g. Classic"
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Slug *</label>
                            <input v-model="form.slug" required type="text" placeholder="e.g. classic"
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            <div v-if="form.errors.slug" class="text-red-400 text-xs mt-1">{{ form.errors.slug }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-medium mb-1 block">Vue Component Name *</label>
                        <input v-model="form.component" required type="text" placeholder="e.g. ClassicTemplate"
                            class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-medium mb-1 block">Description</label>
                        <textarea v-model="form.description" rows="2" placeholder="Short description..."
                            class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Category *</label>
                            <select v-model="form.category"
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-slate-400 font-medium mb-1 block">Sort Order</label>
                            <input v-model.number="form.sort_order" type="number" min="0"
                                class="w-full bg-slate-700 border border-white/10 text-white text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <input v-model="form.is_premium" type="checkbox" id="is_premium"
                            class="w-4 h-4 rounded border-slate-600 bg-slate-700 text-indigo-600 focus:ring-indigo-500" />
                        <label for="is_premium" class="text-sm text-slate-300">Pro only (premium template)</label>
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-medium mb-2 block">Supported Languages</label>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="lang in langs" :key="lang" type="button" @click="toggleLang(lang)"
                                :class="form.supported_languages.includes(lang)
                                    ? 'bg-indigo-600 text-white border-indigo-500'
                                    : 'bg-slate-700 text-slate-400 border-slate-600'"
                                class="px-3 py-1 rounded-full text-xs font-mono font-medium border transition-colors">
                                {{ lang }}
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2 border-t border-white/10">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm text-slate-400 hover:text-white transition-colors">
                            {{ t('cancel') }}
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                            {{ form.processing ? t('saving') : (editingId ? t('edit_template') : t('add_template')) }}
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
