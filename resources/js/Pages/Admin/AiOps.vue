<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PremiumToast from '@/Components/PremiumToast.vue';

const { t } = useI18n();

const props = defineProps({
    usageStats: Object,
    aiConfig:   Object,
    prompts:    Object,
});

const configForm = useForm({
    provider: props.aiConfig.provider,
    model:    props.aiConfig.model,
});

const promptsForm = useForm({
    prompts: { ...props.prompts },
});

const updateConfig = () => {
    configForm.post(route('admin.ai-ops.config.update'), {
        preserveScroll: true,
    });
};

const updatePrompts = () => {
    promptsForm.post(route('admin.ai-ops.prompts.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="AI Operations — Admin" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Header -->
        <div class="bg-slate-900 border-b border-white/10 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white uppercase tracking-tighter">AI Operations & Cost Command</h1>
                <p class="text-slate-500 text-xs mt-0.5">Control models, track token consumption, and manage system prompts.</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] text-emerald-400 font-black uppercase tracking-widest">Mistral Optimized</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
            
            <!-- Usage Analytics Header -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-slate-900 border border-white/5 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Total Tokens Consumed</p>
                    <p class="text-4xl font-black text-white mt-4">{{ Number(usageStats.total_tokens || 0).toLocaleString() }}</p>
                    <div class="mt-4 h-1 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-1000" style="width: 65%"></div>
                    </div>
                </div>

                <div class="bg-slate-900 border border-white/5 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Accumulated AI Cost</p>
                    <p class="text-4xl font-black text-emerald-400 mt-4">${{ Number(usageStats.total_cost || 0).toFixed(4) }}</p>
                    <p class="text-[10px] text-slate-500 mt-4">Estimated based on current provider pricing.</p>
                </div>

                <div class="bg-slate-900 border border-white/5 rounded-2xl p-6 shadow-xl relative overflow-hidden group">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Average Latency</p>
                    <p class="text-4xl font-black text-amber-400 mt-4">{{ Math.round(usageStats.avg_response || 0) }}ms</p>
                    <div class="mt-4 flex gap-1">
                         <div v-for="i in 10" :key="i" class="flex-1 h-3 bg-white/5 rounded-[1px]" :class="{'bg-amber-400/20': i < 7}"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-sm">
                
                <!-- Left: Orchestrator -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-slate-900 border border-white/10 rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 bg-white/5 border-b border-white/10">
                            <h2 class="text-xs font-black uppercase tracking-widest">AI Orchestrator</h2>
                        </div>
                        <form @submit.prevent="updateConfig" class="p-6 space-y-4">
                            <div>
                                <label class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-2 block">Default Provider</label>
                                <select v-model="configForm.provider" class="w-full bg-slate-800 border-white/10 rounded-lg text-white text-xs focus:ring-indigo-500">
                                    <option>Mistral AI</option>
                                    <option>OpenAI</option>
                                    <option>Anthropic</option>
                                    <option>Google Gemini</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-2 block">Active Model</label>
                                <select v-model="configForm.model" class="w-full bg-slate-800 border-white/10 rounded-lg text-white text-xs focus:ring-indigo-500">
                                    <option>mistral-small-latest</option>
                                    <option>mistral-medium-latest</option>
                                    <option>gpt-4o-mini</option>
                                    <option>gemini-1.5-flash</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 rounded-lg transition-all text-xs">
                                Update AI Orchestration
                            </button>
                        </form>
                    </div>

                    <div class="bg-indigo-600/10 border border-indigo-500/20 rounded-2xl p-6">
                        <h3 class="text-xs font-bold text-indigo-300 uppercase tracking-widest mb-2">Cost Optimization Tip</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Switching to <b>mistral-small</b> for draft generation can reduce costs by 60% with minimal impact on quality for basic resume summaries.
                        </p>
                    </div>
                </div>

                <!-- Right: Prompt Governance -->
                <div class="lg:col-span-2 bg-slate-900 border border-white/10 rounded-2xl overflow-hidden">
                    <div class="px-6 py-4 bg-white/5 border-b border-white/10 flex justify-between items-center">
                        <h2 class="text-xs font-black uppercase tracking-widest">Prompt Governance</h2>
                        <span class="text-[10px] bg-red-500/20 text-red-400 px-2 py-0.5 rounded-full font-bold">CRITICAL CONFIG</span>
                    </div>
                    <form @submit.prevent="updatePrompts" class="p-6 space-y-6">
                        <div v-for="(content, key) in promptsForm.prompts" :key="key" class="space-y-2">
                             <label class="text-[10px] text-slate-500 font-bold uppercase tracking-widest block">{{ key.replace('_', ' ') }}</label>
                             <textarea 
                                v-model="promptsForm.prompts[key]" 
                                rows="6" 
                                class="w-full bg-slate-850 border-white/5 rounded-xl text-white text-xs font-mono focus:ring-indigo-500 focus:border-indigo-500 leading-relaxed"
                             ></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-white text-black hover:bg-slate-200 font-bold px-8 py-3 rounded-xl transition-all text-xs uppercase tracking-widest">
                                Deploy System Prompts
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <PremiumToast />
    </div>
</template>

<style scoped>
.bg-slate-850 { background-color: #0f172a; }
</style>
