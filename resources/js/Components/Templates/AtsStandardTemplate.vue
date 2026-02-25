<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps({ 
    resume: Object,
    editable: Boolean 
});
const emit = defineEmits(['section-click', 'update-field', 'reorder-sections']);

const handleDragStart = (e, index) => {
    if (!props.editable) return;
    e.dataTransfer.setData('index', index);
    e.target.classList.add('opacity-40');
};

const handleDrop = (e, targetIndex) => {
    if (!props.editable) return;
    const sourceIndex = parseInt(e.dataTransfer.getData('index'));
    const newOrder = [...(props.resume.design_options?.section_order || ['summary', 'experience', 'education', 'skills', 'certifications', 'languages', 'projects'])];
    const [item] = newOrder.splice(sourceIndex, 1);
    newOrder.splice(targetIndex, 0, item);
    emit('reorder-sections', newOrder);
};

const handleEdit = (section, field, id, e) => {
    if (!props.editable) return;
    emit('update-field', section, field, id, e.target.innerText);
};
</script>

<template>
    <div class="resume-preview-wrapper min-h-[297mm]">
        <!-- Header -->
        <header 
            class="text-center mb-8 border-b-2 pb-6 transition-all"
            :style="{ borderColor: 'var(--accent)' }"
            :class="[editable ? 'editable-section' : '']"
            @click="editable && emit('section-click', 'personal')"
        >
            <h1 
                class="text-4xl font-black tracking-tight uppercase focus:outline-none focus:bg-indigo-50/50 rounded px-2" 
                :style="{ color: 'var(--accent)' }"
                :contenteditable="editable"
                @blur="handleEdit('personal', 'fullname', null, $event)"
            >{{ resume.fullname || (resume.first_name + ' ' + resume.last_name) }}</h1>
            
            <p 
                class="text-xl mt-2 font-bold text-gray-700 uppercase tracking-widest focus:outline-none focus:bg-indigo-50/50 rounded px-2"
                :contenteditable="editable"
                @blur="handleEdit('personal', 'jobtitle', null, $event)"
            >{{ resume.jobtitle || resume.job_title }}</p>
            
            <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mt-4 text-sm font-semibold text-gray-600">
                <span :contenteditable="editable" @blur="handleEdit('personal', 'email', null, $event)">{{ resume.email }}</span>
                <span class="text-gray-300">|</span>
                <span :contenteditable="editable" @blur="handleEdit('personal', 'phone', null, $event)">{{ resume.phone }}</span>
                <span v-if="resume.city || resume.country" class="text-gray-300">|</span>
                <span v-if="resume.city || resume.country" :contenteditable="editable" @blur="handleEdit('personal', 'location', null, $event)">
                    {{ resume.city }} {{ resume.country }}
                </span>
                <span v-if="resume.website" class="text-gray-300">|</span>
                <span v-if="resume.website" :contenteditable="editable" @blur="handleEdit('personal', 'website', null, $event)">{{ resume.website }}</span>
            </div>
        </header>

        <!-- Dynamic Sections Loop -->
        <template v-for="(sectionKey, index) in (resume.design_options?.section_order || ['summary', 'experience', 'education', 'skills', 'certifications', 'languages', 'projects'])" :key="sectionKey">
            <div 
                v-if="!resume.design_options?.hidden_sections?.includes(sectionKey)"
                :draggable="editable"
                @dragstart="handleDragStart($event, index)"
                @dragover.prevent
                @drop="handleDrop($event, index)"
                @dragend="(e) => e.target.classList.remove('opacity-40')"
                class="mb-6 group/section"
            >
                <!-- Summary -->
                <section v-if="sectionKey === 'summary' && (resume.summary || editable)" 
                         class="transition-all" :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'personal')">
                    <h2 class="text-sm font-black uppercase border-b border-gray-200 pb-1 mb-3 tracking-[0.2em]" :style="{ color: 'var(--accent)' }">{{ t('summary') }}</h2>
                    <p class="text-sm leading-relaxed text-gray-800 whitespace-pre-wrap" :contenteditable="editable" @blur="handleEdit('personal', 'summary', null, $event)">{{ resume.summary }}</p>
                </section>

                <!-- Experience -->
                <section v-if="sectionKey === 'experience' && (resume.experiences?.length || editable)" 
                         class="transition-all" :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'experience')">
                    <h2 class="text-sm font-black uppercase border-b border-gray-200 pb-1 mb-4 tracking-[0.2em]" :style="{ color: 'var(--accent)' }">{{ t('experience') }}</h2>
                    <div v-for="exp in resume.experiences" :key="exp.id" class="mb-6">
                        <div class="flex justify-between items-baseline mb-1">
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-gray-900 text-base" :contenteditable="editable" @blur="handleEdit('experiences', 'job_title', exp.id, $event)">{{ exp.job_title || exp.jobtitle }}</span>
                                <span class="text-gray-300">•</span>
                                <span class="font-bold text-[rgb(var(--accent-rgb))] uppercase tracking-wider text-sm" :contenteditable="editable" @blur="handleEdit('experiences', 'company', exp.id, $event)">{{ exp.company }}</span>
                            </div>
                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ exp.start_date }} — {{ exp.current ? t('current') : exp.end_date }}</span>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-600 whitespace-pre-wrap mt-2" :contenteditable="editable" @blur="handleEdit('experiences', 'description', exp.id, $event)">{{ exp.description }}</p>
                    </div>
                </section>

                <!-- Education -->
                <section v-if="sectionKey === 'education' && (resume.education?.length || editable)" 
                         class="transition-all" :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'education')">
                    <h2 class="text-sm font-black uppercase border-b border-gray-200 pb-1 mb-4 tracking-[0.2em]" :style="{ color: 'var(--accent)' }">{{ t('education') }}</h2>
                    <div v-for="edu in resume.education" :key="edu.id" class="mb-4">
                        <div class="flex justify-between items-baseline">
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-gray-900" :contenteditable="editable" @blur="handleEdit('education', 'degree', edu.id, $event)">{{ edu.degree }}</span>
                                <span class="text-gray-300">|</span>
                                <span class="text-gray-700 font-medium" :contenteditable="editable" @blur="handleEdit('education', 'institution', edu.id, $event)">{{ edu.institution }}</span>
                            </div>
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ edu.start_date }} — {{ edu.end_date }}</span>
                        </div>
                    </div>
                </section>

                <!-- Skills -->
                <section v-if="sectionKey === 'skills' && (resume.skills?.length || editable)" 
                         class="transition-all" :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'skills')">
                    <h2 class="text-sm font-black uppercase border-b border-gray-200 pb-1 mb-3 tracking-[0.2em]" :style="{ color: 'var(--accent)' }">{{ t('skills') }}</h2>
                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                        <div v-for="skill in resume.skills" :key="skill.id" class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-800" :contenteditable="editable" @blur="handleEdit('skills', 'name', skill.id, $event)">{{ skill.name }}</span>
                            <span v-if="skill.level" class="text-[10px] text-gray-400 italic">({{ skill.level }})</span>
                        </div>
                    </div>
                </section>

                <!-- Projects -->
                <section v-if="sectionKey === 'projects' && (resume.projects?.length || editable)" 
                         class="transition-all" :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'projects')">
                    <h2 class="text-sm font-black uppercase border-b border-gray-200 pb-1 mb-3 tracking-[0.2em]" :style="{ color: 'var(--accent)' }">{{ t('projects') }}</h2>
                    <div v-for="proj in resume.projects" :key="proj.id" class="mb-4">
                        <div class="flex justify-between items-baseline">
                            <span class="font-bold text-gray-900" :contenteditable="editable" @blur="handleEdit('projects', 'title', proj.id, $event)">{{ proj.title }}</span>
                            <span v-if="proj.link" class="text-xs text-indigo-500 font-bold" :contenteditable="editable" @blur="handleEdit('projects', 'link', proj.id, $event)">{{ proj.link }}</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1" :contenteditable="editable" @blur="handleEdit('projects', 'description', proj.id, $event)">{{ proj.description }}</p>
                    </div>
                </section>

                <!-- Certifications -->
                <section v-if="sectionKey === 'certifications' && (resume.certifications?.length || editable)" 
                         class="transition-all" :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'certifications')">
                    <h2 class="text-sm font-black uppercase border-b border-gray-200 pb-1 mb-3 tracking-[0.2em]" :style="{ color: 'var(--accent)' }">{{ t('certifications') }}</h2>
                    <div v-for="cert in resume.certifications" :key="cert.id" class="mb-3 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-gray-900" :contenteditable="editable" @blur="handleEdit('certifications', 'name', cert.id, $event)">{{ cert.name }}</span>
                            <span class="text-gray-400 mx-2">—</span>
                            <span class="text-gray-600 italic" :contenteditable="editable" @blur="handleEdit('certifications', 'issued_by', cert.id, $event)">{{ cert.issued_by }}</span>
                        </div>
                        <span class="text-xs font-bold text-gray-400">{{ cert.issue_date }}</span>
                    </div>
                </section>

                <!-- Languages -->
                <section v-if="sectionKey === 'languages' && (resume.languages?.length || editable)" 
                         class="transition-all" :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'languages')">
                    <h2 class="text-sm font-black uppercase border-b border-gray-200 pb-1 mb-3 tracking-[0.2em]" :style="{ color: 'var(--accent)' }">{{ t('languages') }}</h2>
                    <div class="flex flex-wrap gap-6">
                        <div v-for="lang in resume.languages" :key="lang.id">
                            <span class="text-xs font-bold text-gray-800" :contenteditable="editable" @blur="handleEdit('languages', 'name', lang.id, $event)">{{ lang.name }}</span>
                            <span class="text-xs text-gray-400 ml-2">— {{ lang.proficiency }}</span>
                        </div>
                    </div>
                </section>
            </div>
        </template>
    </div>
</template>
