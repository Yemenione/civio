<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
const props = defineProps(['resume', 'editable']);
const emit = defineEmits(['section-click', 'update-field', 'reorder-sections']);

const handleEdit = (section, field, id, e) => {
    if (!props.editable) return;
    emit('update-field', section, field, id, e.target.innerText);
};
</script>

<template>
    <div class="modern-grid bg-[#fcfcfc] text-slate-900 w-full min-h-[297mm] p-12 font-sans">
        <!-- Modern Grid Header -->
        <header class="grid grid-cols-12 gap-8 mb-16 border-b-8 border-slate-900 pb-12">
            <div class="col-span-8">
                <h1 
                    class="text-7xl font-black tracking-tighter uppercase leading-[0.8] mb-4 focus:outline-none"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'fullname', null, $event)"
                >{{ resume.fullname }}</h1>
                <p 
                    class="text-2xl font-bold text-indigo-600 uppercase tracking-widest focus:outline-none"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'jobtitle', null, $event)"
                >{{ resume.jobtitle }}</p>
            </div>
            <div class="col-span-4 text-right flex flex-col justify-end text-[11px] font-bold uppercase tracking-widest text-slate-400 space-y-1">
                <p :contenteditable="editable" @blur="handleEdit('personal', 'email', null, $event)">{{ resume.email }}</p>
                <p :contenteditable="editable" @blur="handleEdit('personal', 'phone', null, $event)">{{ resume.phone }}</p>
                <p :contenteditable="editable" @blur="handleEdit('personal', 'location', null, $event)">{{ resume.location }}</p>
            </div>
        </header>

        <div class="grid grid-cols-12 gap-12">
            <!-- Left Side: Narrative -->
            <div class="col-span-7 space-y-12">
                <!-- Summary -->
                <section v-if="resume.summary" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'personal')">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-4">Statement</h2>
                    <p class="text-lg leading-relaxed text-slate-700 font-medium" :contenteditable="editable" @blur="handleEdit('personal', 'summary', null, $event)">{{ resume.summary }}</p>
                </section>

                <!-- Experience -->
                <section v-if="resume.experiences?.length" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'experience')">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-8">Career Path</h2>
                    <div class="space-y-10">
                        <div v-for="exp in resume.experiences" :key="exp.id" class="relative pl-8">
                            <div class="absolute left-0 top-2 w-2 h-2 bg-indigo-600 rounded-full"></div>
                            <div class="mb-2">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">{{ exp.start_date }} — {{ exp.end_date }}</span>
                                <h4 class="text-xl font-black text-slate-900 leading-tight" :contenteditable="editable" @blur="handleEdit('experiences', 'jobtitle', exp.id, $event)">{{ exp.jobtitle }}</h4>
                                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mt-1" :contenteditable="editable" @blur="handleEdit('experiences', 'company', exp.id, $event)">{{ exp.company }}</p>
                            </div>
                            <p class="text-sm text-slate-500 leading-relaxed whitespace-pre-wrap mt-4" :contenteditable="editable" @blur="handleEdit('experiences', 'description', exp.id, $event)">{{ exp.description }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Side: Grid Elements -->
            <div class="col-span-5 space-y-12">
                <!-- Skills Grid -->
                <section v-if="resume.skills?.length" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'skills')">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-6">Expertise</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div v-for="skill in resume.skills" :key="skill.id" class="bg-slate-100 p-3 rounded-xl border border-slate-200 group/item">
                            <p class="text-[10px] font-black uppercase tracking-tight text-slate-900" :contenteditable="editable" @blur="handleEdit('skills', 'name', skill.id, $event)">{{ skill.name }}</p>
                            <p v-if="skill.level" class="text-[8px] font-bold text-indigo-500 uppercase mt-1">{{ skill.level }}</p>
                        </div>
                    </div>
                </section>

                <!-- Education -->
                <section v-if="resume.education?.length" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'education')">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-6">Academic</h2>
                    <div class="space-y-6">
                        <div v-for="edu in resume.education" :key="edu.id" class="border-l-2 border-slate-200 pl-4">
                            <h4 class="text-sm font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('education', 'degree', edu.id, $event)">{{ edu.degree }}</h4>
                            <p class="text-[10px] font-bold text-slate-500 uppercase mt-1" :contenteditable="editable" @blur="handleEdit('education', 'institution', edu.id, $event)">{{ edu.institution }}</p>
                        </div>
                    </div>
                </section>

                <!-- Projects -->
                <section v-if="resume.projects?.length" class="group relative transition-all" :class="[editable ? 'editable-section' : '']" @click="editable && emit('section-click', 'projects')">
                    <h2 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-6">Selected Works</h2>
                    <div class="space-y-4">
                        <div v-for="proj in resume.projects" :key="proj.id" class="bg-slate-900 text-white p-4 rounded-2xl shadow-xl">
                            <h4 class="text-xs font-black uppercase tracking-widest mb-1" :contenteditable="editable" @blur="handleEdit('projects', 'name', proj.id, $event)">{{ proj.name }}</h4>
                            <p class="text-[10px] text-slate-400 leading-relaxed" :contenteditable="editable" @blur="handleEdit('projects', 'description', proj.id, $event)">{{ proj.description }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
