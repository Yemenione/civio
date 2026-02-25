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
    <div class="prism-bold bg-white text-slate-900 w-full min-h-[297mm] flex font-sans overflow-hidden">
        <!-- Colorful Sidebar Accent -->
        <aside class="w-24 bg-slate-900 flex flex-col items-center py-12 gap-12 border-r-8 border-[rgb(var(--accent-rgb))]">
            <div class="rotate-[-90deg] whitespace-nowrap text-white/20 text-6xl font-black uppercase tracking-[0.2em] mt-32 origin-center">
                STUDIO PRO
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 p-16">
            <header class="mb-20">
                <h1 
                    class="text-8xl font-black tracking-tighter leading-none mb-4 focus:outline-none"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'fullname', null, $event)"
                >{{ resume.fullname }}</h1>
                <div class="flex items-center gap-6">
                    <p 
                        class="text-2xl font-bold text-[rgb(var(--accent-rgb))] uppercase tracking-widest focus:outline-none"
                        :contenteditable="editable"
                        @blur="handleEdit('personal', 'jobtitle', null, $event)"
                    >{{ resume.jobtitle }}</p>
                    <div class="h-px flex-1 bg-slate-100"></div>
                </div>
                
                <div class="flex gap-8 mt-8 text-[11px] font-black uppercase tracking-[0.3em] text-slate-400">
                    <span :contenteditable="editable" @blur="handleEdit('personal', 'email', null, $event)">{{ resume.email }}</span>
                    <span :contenteditable="editable" @blur="handleEdit('personal', 'phone', null, $event)">{{ resume.phone }}</span>
                    <span :contenteditable="editable" @blur="handleEdit('personal', 'location', null, $event)">{{ resume.location }}</span>
                </div>
            </header>

            <div class="grid grid-cols-12 gap-16">
                <!-- Primary Narrative -->
                <div class="col-span-8 space-y-16">
                    <section v-if="resume.experiences?.length">
                        <h3 class="text-xs font-black uppercase tracking-[0.5em] text-[rgb(var(--accent-rgb))] mb-10">Experience</h3>
                        <div class="space-y-12">
                            <div v-for="exp in resume.experiences" :key="exp.id">
                                <div class="flex justify-between items-baseline mb-4">
                                    <h4 class="text-2xl font-black text-slate-900" :contenteditable="editable" @blur="handleEdit('experiences', 'jobtitle', exp.id, $event)">{{ exp.jobtitle }}</h4>
                                    <span class="text-xs font-black text-slate-300 uppercase tracking-widest">{{ exp.start_date }} — {{ exp.end_date }}</span>
                                </div>
                                <p class="text-lg font-bold text-slate-400 mb-4" :contenteditable="editable" @blur="handleEdit('experiences', 'company', exp.id, $event)">{{ exp.company }}</p>
                                <p class="text-sm text-slate-600 leading-relaxed max-w-2xl" :contenteditable="editable" @blur="handleEdit('experiences', 'description', exp.id, $event)">{{ exp.description }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Secondary Details -->
                <div class="col-span-4 space-y-16">
                    <section v-if="resume.skills?.length">
                        <h3 class="text-xs font-black uppercase tracking-[0.5em] text-[rgb(var(--accent-rgb))] mb-8">Expertise</h3>
                        <div class="flex flex-col gap-4">
                            <div v-for="skill in resume.skills" :key="skill.id" class="flex items-center gap-4">
                                <div class="w-2 h-2 rounded-full bg-[rgb(var(--accent-rgb))]"></div>
                                <span class="text-sm font-black uppercase tracking-widest text-slate-900" :contenteditable="editable" @blur="handleEdit('skills', 'name', skill.id, $event)">{{ skill.name }}</span>
                            </div>
                        </div>
                    </section>

                    <section v-if="resume.education?.length">
                        <h3 class="text-xs font-black uppercase tracking-[0.5em] text-[rgb(var(--accent-rgb))] mb-8">Academic</h3>
                        <div class="space-y-8">
                            <div v-for="edu in resume.education" :key="edu.id">
                                <h4 class="text-sm font-black text-slate-900 leading-tight mb-1" :contenteditable="editable" @blur="handleEdit('education', 'degree', edu.id, $event)">{{ edu.degree }}</h4>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest" :contenteditable="editable" @blur="handleEdit('education', 'institution', edu.id, $event)">{{ edu.institution }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
