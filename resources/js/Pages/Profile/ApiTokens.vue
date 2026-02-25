<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    tokens: Array
});

const form = useForm({
    token_name: ''
});

const createToken = () => {
    form.post(route('api-tokens.store'), {
        onSuccess: () => form.reset(),
    });
};

const revokeToken = (id) => {
    if (confirm('Are you sure you want to revoke this token?')) {
        form.delete(route('api-tokens.destroy', id));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-black text-white uppercase tracking-widest">
                Developer <span class="premium-gradient-text">API Tokens</span>
            </h2>
            <p class="text-slate-400 text-sm mt-1 uppercase tracking-tighter">Generate tokens to access Civio programmatically.</p>
        </template>

        <div class="space-y-8 max-w-4xl">
            <!-- Create Token -->
            <div class="glass-card p-8 rounded-3xl">
                <h3 class="text-lg font-bold mb-6 text-white uppercase">Create New Token</h3>
                <form @submit.prevent="createToken" class="flex flex-col sm:flex-row gap-4">
                    <input 
                        v-model="form.token_name" 
                        type="text" 
                        placeholder="e.g. My Website Integrator"
                        class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                        required
                    />
                    <button 
                        :disabled="form.processing"
                        type="submit" 
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/20 uppercase text-xs"
                    >
                        {{ form.processing ? 'Creating...' : 'Create' }}
                    </button>
                </form>
            </div>

            <!-- Existing Tokens -->
            <div class="glass-card p-8 rounded-3xl">
                <h3 class="text-lg font-bold mb-6 text-white uppercase">Your Tokens</h3>
                <div v-if="tokens.length" class="space-y-4">
                    <div v-for="token in tokens" :key="token.id" class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 group">
                        <div>
                            <p class="text-white font-bold uppercase text-sm tracking-wide">{{ token.name }}</p>
                            <p class="text-slate-500 text-[10px] mt-1 italic">Last used: {{ token.last_used }}</p>
                        </div>
                        <button 
                            @click="revokeToken(token.id)"
                            class="p-2 text-slate-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                <div v-else class="text-center py-12 text-slate-500">
                    <p class="text-sm italic">You haven't generated any API tokens yet.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
