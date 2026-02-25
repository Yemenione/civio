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
    <div class="mena-royal bg-white text-slate-900 w-full min-h-[297mm] font-sans" dir="rtl">
        <!-- Royal Header -->
        <header class="bg-slate-900 text-white p-12 text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/islamic-art.png')]"></div>
            <div class="relative z-10">
                <h1 
                    class="text-5xl font-black mb-4 tracking-tight focus:outline-none"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'fullname', null, $event)"
                >{{ resume.fullname }}</h1>
                <p 
                    class="text-2xl font-bold text-[rgb(var(--accent-rgb))] mb-8 focus:outline-none"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'jobtitle', null, $event)"
                >{{ resume.jobtitle }}</p>
                
                <div class="flex flex-wrap justify-center gap-8 text-sm opacity-80">
                    <span :contenteditable="editable" @blur="handleEdit('personal', 'email', null, $event)">{{ resume.email }}</span>
                    <span :contenteditable="editable" @blur="handleEdit('personal', 'phone', null, $event)">{{ resume.phone }}</span>
                    <span v-if="resume.location" :contenteditable="editable" @blur="handleEdit('personal', 'location', null, $event)">{{ resume.location }}</span>
                </div>
            </div>
        </header>

        <div class="p-12 space-y-12">
            <template v-for="(sectionKey, index) in (resume.design_options?.section_order || ['summary', 'experience', 'education', 'skills', 'projects', 'certifications', 'languages'])" :key="sectionKey">
                <div v-if="!resume.design_options?.hidden_sections?.includes(sectionKey)"
                     :draggable="editable" @dragstart="handleDragStart($event, index)" @dragover.prevent @drop="handleDrop($event, index)">
                    
                    <!-- Summary -->
                    <section v-if="sectionKey === 'summary' && (resume.summary || editable)" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'personal')">
                        <div class="flex items-center gap-4 mb-4">
                            <h2 class="text-xl font-black text-slate-900">{{ t('summary') }}</h2>
                            <div class="flex-1 h-1 bg-[rgb(var(--accent-rgb))] opacity-20"></div>
                        </div>
                        <p class="text-lg leading-relaxed text-slate-700 whitespace-pre-wrap" :contenteditable="editable" @blur="handleEdit('personal', 'summary', null, $event)">{{ resume.summary }}</p>
                    </section>

                    <!-- Experience -->
                    <section v-if="sectionKey === 'experience' && (resume.experiences?.length || editable)" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'experience')">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-xl font-black text-slate-900">{{ t('experience') }}</h2>
                            <div class="flex-1 h-1 bg-[rgb(var(--accent-rgb))] opacity-20"></div>
                        </div>
                        <div class="space-y-8">
                            <div v-for="exp in resume.experiences" :key="exp.id" class="border-r-4 border-slate-100 pr-6 group-hover:border-[rgb(var(--accent-rgb))] transition-colors">
                                <div class="flex justify-between items-baseline mb-2">
                                    <h4 class="text-xl font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('experiences', 'jobtitle', exp.id, $event)">{{ exp.jobtitle }}</h4>
                                    <span class="text-sm font-bold text-slate-400">{{ exp.start_date }} — {{ exp.current ? t('current') : exp.end_date }}</span>
                                </div>
                                <p class="text-md font-bold text-[rgb(var(--accent-rgb))] mb-3" :contenteditable="editable" @blur="handleEdit('experiences', 'company', exp.id, $event)">{{ exp.company }}</p>
                                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap" :contenteditable="editable" @blur="handleEdit('experiences', 'description', exp.id, $event)">{{ exp.description }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Education -->
                    <section v-if="sectionKey === 'education' && (resume.education?.length || editable)" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'education')">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-xl font-black text-slate-900">{{ t('education') }}</h2>
                            <div class="flex-1 h-1 bg-[rgb(var(--accent-rgb))] opacity-20"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-8">
                            <div v-for="edu in resume.education" :key="edu.id">
                                <h4 class="text-lg font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('education', 'degree', edu.id, $event)">{{ edu.degree }}</h4>
                                <p class="text-md font-bold text-[rgb(var(--accent-rgb))]" :contenteditable="editable" @blur="handleEdit('education', 'institution', edu.id, $event)">{{ edu.institution }}</p>
                                <p class="text-sm text-slate-400 font-bold mt-1">{{ edu.start_date }} — {{ edu.end_date }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Skills -->
                    <section v-if="sectionKey === 'skills' && (resume.skills?.length || editable)" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'skills')">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-xl font-black text-slate-900">{{ t('skills') }}</h2>
                            <div class="flex-1 h-1 bg-[rgb(var(--accent-rgb))] opacity-20"></div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <span v-for="skill in resume.skills" :key="skill.id" class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700" :contenteditable="editable" @blur="handleEdit('skills', 'name', skill.id, $event)">
                                {{ skill.name }}
                            </span>
                        </div>
                    </section>

                    <!-- Projects -->
                    <section v-if="sectionKey === 'projects' && (resume.projects?.length || editable)" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'projects')">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-xl font-black text-slate-900">{{ t('projects') }}</h2>
                            <div class="flex-1 h-1 bg-[rgb(var(--accent-rgb))] opacity-20"></div>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            <div v-for="proj in resume.projects" :key="proj.id" class="p-6 bg-slate-50 rounded-3xl">
                                <h4 class="text-lg font-black text-slate-900 mb-2" :contenteditable="editable" @blur="handleEdit('projects', 'name', proj.id, $event)">{{ proj.name }}</h4>
                                <p class="text-sm text-slate-600 leading-relaxed" :contenteditable="editable" @blur="handleEdit('projects', 'description', proj.id, $event)">{{ proj.description }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Certifications -->
                    <section v-if="sectionKey === 'certifications' && (resume.certifications?.length || editable)" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'certifications')">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-xl font-black text-slate-900">{{ t('certifications') }}</h2>
                            <div class="flex-1 h-1 bg-[rgb(var(--accent-rgb))] opacity-20"></div>
                        </div>
                        <div class="space-y-4">
                            <div v-for="cert in resume.certifications" :key="cert.id" class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                <div>
                                    <h4 class="font-bold text-slate-900" :contenteditable="editable" @blur="handleEdit('certifications', 'name', cert.id, $event)">{{ cert.name }}</h4>
                                    <p class="text-sm text-slate-500" :contenteditable="editable" @blur="handleEdit('certifications', 'issued_by', cert.id, $event)">{{ cert.issued_by }}</p>
                                </div>
                                <span class="text-sm font-bold text-slate-400">{{ cert.issue_date }}</span>
                            </div>
                        </div>
                    </section>

                    <!-- Languages -->
                    <section v-if="sectionKey === 'languages' && (resume.languages?.length || editable)" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'languages')">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-xl font-black text-slate-900">{{ t('languages') }}</h2>
                            <div class="flex-1 h-1 bg-[rgb(var(--accent-rgb))] opacity-20"></div>
                        </div>
                        <div class="flex flex-wrap gap-12">
                            <div v-for="lang in resume.languages" :key="lang.id" class="text-center">
                                <p class="text-lg font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('languages', 'name', lang.id, $event)">{{ lang.name }}</p>
                                <p class="text-sm font-bold text-[rgb(var(--accent-rgb))] uppercase">{{ lang.proficiency }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </template>
        </div>
    </div>
</template>
