<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ResumePreview from './ResumePreview.vue';
import { useI18n } from 'vue-i18n';
import { useNotify } from '@/composables/useNotify';
import AuraRing from './AuraRing.vue';

const { t } = useI18n();
const notify = useNotify();

const props = defineProps({
    resume: { type: Object, required: true },
});

const emit = defineEmits(['delete', 'duplicate']);

const showShareMenu = ref(false);
const showMoreMenu = ref(false);

const updatedAt = computed(() => {
    if (!props.resume.updated_at) return '';
    return new Date(props.resume.updated_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
});

const templateLabel = computed(() => {
    const map = { 
        classic: t('classic'), 
        modern: t('modern'), 
        minimal: t('minimal'), 
        executive: t('executive'), 
        creative: t('creative'), 
        arabicpro: t('plan_pro') + ' (Arabic)',
        canvalux:  'CanvaLux (Pro)'
    };
    return map[props.resume.template] ?? t('classic');
});

const confirmDelete = () => {
    notify.confirm(
        t('delete_resume_title') || 'Delete Resume?',
        t('confirm_delete_resume') || 'Are you sure you want to delete this resume? This cannot be undone.',
        () => emit('delete', props.resume.id)
    );
    showMoreMenu.value = false;
};

const duplicate = () => {
    emit('duplicate', props.resume.id);
    showMoreMenu.value = false;
};

const copyShareLink = () => {
    navigator.clipboard.writeText(route('resumes.show', props.resume.share_token));
    notify.success(t('cl_copied') || 'Link copied to clipboard!');
    showShareMenu.value = false;
};

const toggleShareMenu = (e) => {
    e.stopPropagation();
    showShareMenu.value = !showShareMenu.value;
    showMoreMenu.value = false;
};

const toggleMoreMenu = (e) => {
    e.stopPropagation();
    showMoreMenu.value = !showMoreMenu.value;
    showShareMenu.value = false;
};

// Close menus when clicking anywhere else
const closeMenus = () => {
    showShareMenu.value = false;
    showMoreMenu.value = false;
};

// Strength Calculation (Static for overview, matching logic)
const strength = computed(() => {
    let score = 0;
    const r = props.resume;
    if (r.first_name) score += 5;
    if (r.last_name)  score += 5;
    if (r.email)      score += 5;
    if (r.phone)      score += 5;
    if (r.job_title)  score += 10;
    if (r.summary)    score += 10;
    if (r.experiences?.length) score += 15;
    if (r.education?.length)   score += 15;
    if (r.skills?.length)      score += 10;
    if (r.projects?.length)       score += 10;
    if (r.languages?.length)      score += 5;
    if (r.certifications?.length) score += 5;
    return Math.min(score, 100);
});

onMounted(() => {
    window.addEventListener('click', closeMenus);
});

onUnmounted(() => {
    window.removeEventListener('click', closeMenus);
});
</script>

<template>
    <div class="group relative bg-white dark:bg-slate-900/40 backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-3xl overflow-hidden hover:border-indigo-500/50 transition-all duration-500 hover:shadow-2xl flex flex-col h-full">
        <!-- Thumbnail Preview -->
        <div class="relative aspect-[4/3] overflow-hidden bg-slate-50 dark:bg-slate-950/50 cursor-pointer" @click="router.visit(route('resumes.edit', resume.id))">
            <!-- Scaling the preview components -->
            <div class="absolute inset-0 origin-top-left p-4 opacity-80 group-hover:opacity-100 transition-opacity duration-500" style="transform: scale(0.3); width: 333%; height: 333%; pointer-events: none;">
                <ResumePreview :resume="resume" />
            </div>
            
            <!-- Floating Status Badge -->
            <div class="absolute top-4 right-4 z-10 flex flex-col items-end gap-2">
                <div v-if="resume.is_public" class="flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 rounded-full text-[10px] font-black backdrop-blur-md">
                    <span class="w-1 h-1 rounded-full bg-emerald-500 dark:bg-emerald-400 animate-pulse"></span>
                    {{ t('public_link_active') }}
                </div>
                <div v-else class="flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 dark:bg-white/5 text-slate-500 border border-black/5 dark:border-white/5 rounded-full text-[10px] font-black backdrop-blur-md uppercase tracking-widest leading-none">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    PRIVATE
                </div>
                
                <!-- Aura Mini -->
                <div class="scale-50 origin-top-right">
                    <AuraRing :score="strength" :size="100" />
                </div>
            </div>

            <!-- Enhanced Hover Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col items-center justify-center gap-3">
                <button class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-2xl shadow-indigo-500/40 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500 hover:scale-110 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white opacity-80 translate-y-4 group-hover:translate-y-0 transition-all duration-500 delay-75">{{ t('edit') }}</p>
            </div>
        </div>

        <!-- Content Section -->
        <div class="p-4 flex flex-col flex-1">
            <div class="mb-3">
                <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                    {{ resume.title || t('untitled_resume') }}
                </h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-white/5 border border-black/5 dark:border-white/5 rounded text-[9px] uppercase font-black tracking-widest text-slate-500">
                        {{ templateLabel }}
                    </span>
                    <span class="text-slate-300 dark:text-slate-600">·</span>
                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">{{ updatedAt }}</span>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-auto grid grid-cols-5 gap-1.5 pt-3 border-t border-black/5 dark:border-white/5">
                <!-- Main Edit -->
                <Link
                    :href="route('resumes.edit', resume.id)"
                    class="col-span-2 flex items-center justify-center gap-2 py-2 text-[9px] font-black uppercase tracking-widest text-indigo-600 dark:text-white bg-indigo-600/5 dark:bg-indigo-600/10 hover:bg-indigo-600 hover:text-white border border-indigo-500/20 dark:border-indigo-500/30 rounded-lg transition-all duration-300"
                >
                    {{ t('edit') }}
                </Link>

                <!-- Download -->
                <Link
                    :href="route('resumes.download', resume.id)"
                    class="flex items-center justify-center p-2 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 border border-black/5 dark:border-white/5 rounded-lg transition-all duration-300"
                    :title="t('download')"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </Link>

                <!-- Share -->
                <div class="relative">
                    <button
                        @click="toggleShareMenu"
                        class="w-full flex items-center justify-center p-2 transition-all duration-300 border rounded-lg"
                        :class="resume.is_public ? 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 border-emerald-500/20' : 'bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 border-black/5 dark:border-white/5 hover:bg-slate-200 dark:hover:bg-white/10'"
                        :title="t('share_resume')"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    </button>
                    <!-- Floating Share Menu -->
                    <div v-show="showShareMenu && resume.is_public" class="absolute bottom-full ltr:right-0 rtl:left-0 mb-3 w-52 bg-white dark:bg-slate-900/95 backdrop-blur-2xl border border-black/10 dark:border-white/10 rounded-2xl p-2 shadow-2xl z-20">
                        <button @click="copyShareLink" class="w-full text-left p-3 hover:bg-slate-50 dark:hover:bg-white/5 rounded-xl text-[11px] font-black text-slate-900 dark:text-white flex items-center gap-3 transition-colors">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            {{ t('copy_link') }}
                        </button>
                        <button 
                            @click="router.patch(route('resumes.share.toggle', resume.id), {}, { preserveScroll: true })"
                            class="w-full text-left p-3 hover:bg-white/5 rounded-xl text-[11px] font-bold text-red-300 flex items-center gap-3 transition-colors mt-1"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            DISABLE PUBLIC
                        </button>
                    </div>
                </div>

                <!-- More Options -->
                <div class="relative">
                    <button 
                        @click="toggleMoreMenu"
                        class="w-full flex items-center justify-center p-2 text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 border border-black/5 dark:border-white/5 rounded-lg transition-all duration-300"
                        :class="showMoreMenu ? 'bg-indigo-50 dark:bg-white/10 text-indigo-600 dark:text-white border-indigo-200 dark:border-white/20' : ''"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                    </button>
                    <div v-show="showMoreMenu" class="absolute bottom-full ltr:right-0 rtl:left-0 mb-3 w-48 bg-white dark:bg-slate-900/95 backdrop-blur-2xl border border-black/10 dark:border-white/10 rounded-2xl p-2 shadow-2xl z-20">
                        <button @click="duplicate" class="w-full text-left p-3 hover:bg-slate-50 dark:hover:bg-white/5 rounded-xl text-[11px] font-black text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-white flex items-center gap-3 transition-colors">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                            {{ t('duplicate_resume') }}
                        </button>
                        <button @click="confirmDelete" class="w-full text-left p-3 hover:bg-red-500/10 rounded-xl text-[11px] font-bold text-red-400 flex items-center gap-3 transition-colors mt-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            {{ t('delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
