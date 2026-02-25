<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps(['resume', 'editable']);
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
    <div class="resume-preview-wrapper min-h-[297mm] bg-white relative font-serif">
        <!-- Luxury Header -->
        <header class="mb-12 pt-8 border-b-2 border-slate-100 pb-8 text-center" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'personal')">
            <h1 
                class="text-5xl font-black tracking-tight text-slate-900 mb-2 uppercase focus:outline-none"
                :contenteditable="editable"
                @blur="handleEdit('personal', 'fullname', null, $event)"
            >{{ resume.fullname }}</h1>
            <p 
                class="text-xl font-medium text-[rgb(var(--accent-rgb))] uppercase tracking-[0.3em] mb-6 focus:outline-none"
                :contenteditable="editable"
                @blur="handleEdit('personal', 'jobtitle', null, $event)"
            >{{ resume.jobtitle }}</p>
            
            <div class="flex flex-wrap justify-center gap-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                <span :contenteditable="editable" @blur="handleEdit('personal', 'email', null, $event)">{{ resume.email }}</span>
                <span :contenteditable="editable" @blur="handleEdit('personal', 'phone', null, $event)">{{ resume.phone }}</span>
                <span v-if="resume.location" :contenteditable="editable" @blur="handleEdit('personal', 'location', null, $event)">{{ resume.location }}</span>
            </div>
        </header>

        <div class="grid grid-cols-12 gap-12">
            <!-- Left Column: Narrative -->
            <div class="col-span-8 space-y-12">
                <template v-for="(sectionKey, index) in (resume.design_options?.section_order || ['summary', 'experience', 'projects'])" :key="sectionKey">
                    <div v-if="['summary', 'experience', 'projects'].includes(sectionKey) && !resume.design_options?.hidden_sections?.includes(sectionKey)"
                         :draggable="editable" @dragstart="handleDragStart($event, index)" @dragover.prevent @drop="handleDrop($event, index)">
                        
                        <!-- Summary -->
                        <section v-if="sectionKey === 'summary'" class="transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'personal')">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-4 flex items-center gap-4">
                                {{ t('summary') }}
                                <div class="flex-1 h-px bg-slate-100"></div>
                            </h3>
                            <p class="text-sm text-slate-600 leading-relaxed italic" :contenteditable="editable" @blur="handleEdit('personal', 'summary', null, $event)">"{{ resume.summary }}"</p>
                        </section>

                        <!-- Experience -->
                        <section v-if="sectionKey === 'experience'" class="transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'experience')">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-6 flex items-center gap-4">
                                {{ t('experience') }}
                                <div class="flex-1 h-px bg-slate-100"></div>
                            </h3>
                            <div class="space-y-8">
                                <div v-for="exp in resume.experiences" :key="exp.id">
                                    <div class="flex justify-between items-baseline mb-2">
                                        <h4 class="text-lg font-bold text-slate-900" :contenteditable="editable" @blur="handleEdit('experiences', 'jobtitle', exp.id, $event)">{{ exp.jobtitle }}</h4>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ exp.start_date }} — {{ exp.end_date }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-[rgb(var(--accent-rgb))] uppercase tracking-widest mb-3" :contenteditable="editable" @blur="handleEdit('experiences', 'company', exp.id, $event)">{{ exp.company }}</p>
                                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-wrap" :contenteditable="editable" @blur="handleEdit('experiences', 'description', exp.id, $event)">{{ exp.description }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Projects -->
                        <section v-if="sectionKey === 'projects'" class="transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'projects')">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-6 flex items-center gap-4">
                                {{ t('projects') }}
                                <div class="flex-1 h-px bg-slate-100"></div>
                            </h3>
                            <div class="space-y-6">
                                <div v-for="proj in resume.projects" :key="proj.id">
                                    <h4 class="text-md font-black text-slate-900 mb-1" :contenteditable="editable" @blur="handleEdit('projects', 'name', proj.id, $event)">{{ proj.name }}</h4>
                                    <p class="text-xs text-slate-600 leading-relaxed" :contenteditable="editable" @blur="handleEdit('projects', 'description', proj.id, $event)">{{ proj.description }}</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </template>
            </div>

            <!-- Right Column: Stats -->
            <div class="col-span-4 space-y-12 border-l border-slate-100 pl-8">
                <template v-for="(sectionKey, index) in (resume.design_options?.section_order || ['skills', 'education', 'certifications', 'languages'])" :key="sectionKey">
                    <div v-if="!['summary', 'experience', 'projects'].includes(sectionKey) && !resume.design_options?.hidden_sections?.includes(sectionKey)"
                         :draggable="editable" @dragstart="handleDragStart($event, index)" @dragover.prevent @drop="handleDrop($event, index)">
                        
                        <!-- Skills -->
                        <section v-if="sectionKey === 'skills'" class="transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'skills')">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-6">{{ t('skills') }}</h3>
                            <div class="space-y-4">
                                <div v-for="skill in resume.skills" :key="skill.id">
                                    <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mb-1" :contenteditable="editable" @blur="handleEdit('skills', 'name', skill.id, $event)">{{ skill.name }}</p>
                                    <div class="w-full h-1 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-[rgb(var(--accent-rgb))] opacity-60" :style="{ width: '70%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Education -->
                        <section v-if="sectionKey === 'education'" class="transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'education')">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-6">{{ t('education') }}</h3>
                            <div class="space-y-6">
                                <div v-for="edu in resume.education" :key="edu.id">
                                    <p class="text-xs font-black text-slate-900 uppercase leading-tight mb-1" :contenteditable="editable" @blur="handleEdit('education', 'degree', edu.id, $event)">{{ edu.degree }}</p>
                                    <p class="text-[10px] font-bold text-[rgb(var(--accent-rgb))] uppercase tracking-widest" :contenteditable="editable" @blur="handleEdit('education', 'institution', edu.id, $event)">{{ edu.institution }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Certifications -->
                        <section v-if="sectionKey === 'certifications'" class="transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'certifications')">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-6">{{ t('certifications') }}</h3>
                            <div class="space-y-4">
                                <div v-for="cert in resume.certifications" :key="cert.id">
                                    <p class="text-xs font-bold text-slate-800" :contenteditable="editable" @blur="handleEdit('certifications', 'name', cert.id, $event)">{{ cert.name }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1" :contenteditable="editable" @blur="handleEdit('certifications', 'issued_by', cert.id, $event)">{{ cert.issued_by }}</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
