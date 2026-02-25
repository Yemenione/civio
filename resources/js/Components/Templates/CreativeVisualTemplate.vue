<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps({ 
    resume: Object,
    editable: Boolean 
});
const emit = defineEmits(['section-click', 'update-field', 'reorder-sections']);

const handleEdit = (section, field, id, e) => {
    if (!props.editable) return;
    emit('update-field', section, field, id, e.target.innerText);
};

const handleDragStart = (e, index) => {
    if (!props.editable) return;
    e.dataTransfer.setData('index', index);
};

const handleDrop = (e, targetIndex) => {
    if (!props.editable) return;
    const sourceIndex = parseInt(e.dataTransfer.getData('index'));
    const newOrder = [...(props.resume.design_options?.section_order || [])];
    const [item] = newOrder.splice(sourceIndex, 1);
    newOrder.splice(targetIndex, 0, item);
    emit('reorder-sections', newOrder);
};
</script>

<template>
    <div class="creative-visual bg-white text-slate-900 w-full min-h-[297mm] font-sans">
        <!-- Visual Header -->
        <header class="relative h-64 bg-slate-900 overflow-hidden flex items-center px-12 gap-8">
            <!-- Decorative Accent -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-[rgb(var(--accent-rgb))] opacity-20 blur-[100px]"></div>
            
            <div v-if="resume.photo" class="relative z-10 w-40 h-40 rounded-3xl overflow-hidden border-4 border-white/10 shadow-2xl rotate-3">
                <img :src="resume.photo" alt="Profile" class="w-full h-full object-cover" />
            </div>

            <div class="relative z-10 flex-1 text-white">
                <h1 
                    class="text-5xl font-black tracking-tighter uppercase leading-none mb-2 focus:outline-none"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'fullname', null, $event)"
                >{{ resume.fullname }}</h1>
                <p 
                    class="text-xl font-bold text-[rgb(var(--accent-rgb))] tracking-widest uppercase focus:outline-none"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'jobtitle', null, $event)"
                >{{ resume.jobtitle }}</p>
                
                <div class="flex flex-wrap gap-4 mt-6 text-[10px] font-black uppercase tracking-widest opacity-60">
                    <span :contenteditable="editable" @blur="handleEdit('personal', 'email', null, $event)">{{ resume.email }}</span>
                    <span>•</span>
                    <span :contenteditable="editable" @blur="handleEdit('personal', 'phone', null, $event)">{{ resume.phone }}</span>
                    <span v-if="resume.location">|</span>
                    <span v-if="resume.location" :contenteditable="editable" @blur="handleEdit('personal', 'location', null, $event)">{{ resume.location }}</span>
                </div>
            </div>
        </header>

        <div class="p-12 grid grid-cols-12 gap-12">
            <!-- Left: Main Narrative -->
            <div class="col-span-8 space-y-12">
                <template v-for="(sectionKey, index) in (resume.design_options?.section_order || ['summary', 'experience', 'projects'])" :key="sectionKey">
                    <div v-if="['summary', 'experience', 'projects'].includes(sectionKey) && !resume.design_options?.hidden_sections?.includes(sectionKey)"
                         :draggable="editable" @dragstart="handleDragStart($event, index)" @dragover.prevent @drop="handleDrop($event, index)">
                        
                        <!-- Summary -->
                        <section v-if="sectionKey === 'summary'" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'personal')">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-4">{{ t('summary') }}</h2>
                            <p class="text-lg leading-relaxed text-slate-700 font-medium italic" :contenteditable="editable" @blur="handleEdit('personal', 'summary', null, $event)">"{{ resume.summary }}"</p>
                        </section>

                        <!-- Experience -->
                        <section v-if="sectionKey === 'experience'" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'experience')">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-8">{{ t('experience') }}</h2>
                            <div class="space-y-10">
                                <div v-for="exp in resume.experiences" :key="exp.id" class="relative pl-8 border-l-2 border-slate-100 group-hover:border-[rgb(var(--accent-rgb))] transition-colors">
                                    <div class="absolute -left-[9px] top-0 w-4 h-4 bg-white border-2 border-slate-200 rounded-full group-hover:border-[rgb(var(--accent-rgb))] transition-colors"></div>
                                    <div class="flex justify-between items-baseline mb-2">
                                        <h4 class="text-xl font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('experiences', 'jobtitle', exp.id, $event)">{{ exp.jobtitle }}</h4>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ exp.start_date }} — {{ exp.current ? t('current') : exp.end_date }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-[rgb(var(--accent-rgb))] uppercase tracking-widest mb-4" :contenteditable="editable" @blur="handleEdit('experiences', 'company', exp.id, $event)">{{ exp.company }}</p>
                                    <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap" :contenteditable="editable" @blur="handleEdit('experiences', 'description', exp.id, $event)">{{ exp.description }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Projects -->
                        <section v-if="sectionKey === 'projects'" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'projects')">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">{{ t('projects') }}</h2>
                            <div class="grid grid-cols-1 gap-6">
                                <div v-for="proj in resume.projects" :key="proj.id" class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                                    <h4 class="text-lg font-black text-slate-900 mb-2" :contenteditable="editable" @blur="handleEdit('projects', 'name', proj.id, $event)">{{ proj.name }}</h4>
                                    <p class="text-sm text-slate-600 leading-relaxed" :contenteditable="editable" @blur="handleEdit('projects', 'description', proj.id, $event)">{{ proj.description }}</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </template>
            </div>

            <!-- Right: Details & Stats -->
            <div class="col-span-4 space-y-12">
                <template v-for="(sectionKey, index) in (resume.design_options?.section_order || ['skills', 'education', 'certifications', 'languages'])" :key="sectionKey">
                    <div v-if="!['summary', 'experience', 'projects'].includes(sectionKey) && !resume.design_options?.hidden_sections?.includes(sectionKey)"
                         :draggable="editable" @dragstart="handleDragStart($event, index)" @dragover.prevent @drop="handleDrop($event, index)">
                        
                        <!-- Skills -->
                        <section v-if="sectionKey === 'skills'" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'skills')">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">{{ t('skills') }}</h2>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="skill in resume.skills" :key="skill.id" class="px-3 py-1 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg" :contenteditable="editable" @blur="handleEdit('skills', 'name', skill.id, $event)">
                                    {{ skill.name }}
                                </span>
                            </div>
                        </section>

                        <!-- Education -->
                        <section v-if="sectionKey === 'education'" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'education')">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">{{ t('education') }}</h2>
                            <div class="space-y-6">
                                <div v-for="edu in resume.education" :key="edu.id">
                                    <h4 class="text-sm font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('education', 'degree', edu.id, $event)">{{ edu.degree }}</h4>
                                    <p class="text-xs font-bold text-[rgb(var(--accent-rgb))] uppercase tracking-widest mt-1" :contenteditable="editable" @blur="handleEdit('education', 'institution', edu.id, $event)">{{ edu.institution }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1">{{ edu.start_date }} — {{ edu.end_date }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Certifications -->
                        <section v-if="sectionKey === 'certifications'" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'certifications')">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">{{ t('certifications') }}</h2>
                            <div class="space-y-4">
                                <div v-for="cert in resume.certifications" :key="cert.id">
                                    <h4 class="text-sm font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('certifications', 'name', cert.id, $event)">{{ cert.name }}</h4>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1" :contenteditable="editable" @blur="handleEdit('certifications', 'issued_by', cert.id, $event)">{{ cert.issued_by }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Languages -->
                        <section v-if="sectionKey === 'languages'" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'languages')">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">{{ t('languages') }}</h2>
                            <div class="space-y-2">
                                <div v-for="lang in resume.languages" :key="lang.id" class="flex justify-between items-center text-sm font-bold">
                                    <span :contenteditable="editable" @blur="handleEdit('languages', 'name', lang.id, $event)">{{ lang.name }}</span>
                                    <span class="text-[10px] text-[rgb(var(--accent-rgb))] uppercase tracking-widest">{{ lang.proficiency }}</span>
                                </div>
                            </div>
                        </section>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
