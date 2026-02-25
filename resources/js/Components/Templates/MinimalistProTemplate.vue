<script setup>
import { computed } from 'vue';
const props = defineProps(['resume', 'editable']);
const emit = defineEmits(['section-click', 'update-field']);

const handleEdit = (section, field, id, e) => {
    if (!props.editable) return;
    emit('update-field', section, field, id, e.target.innerText);
};
</script>

<template>
    <div class="resume-preview-wrapper min-h-[297mm] bg-white text-slate-900 font-sans p-16">
        <!-- Header: Ultra Minimal Side-by-Side -->
        <header class="flex justify-between items-end mb-24">
            <div>
                <h1 
                    class="text-6xl font-black tracking-tighter leading-none mb-4"
                    :contenteditable="editable"
                    @blur="handleEdit('personal', 'first_name', null, $event)"
                >
                    {{ resume.first_name }}<br/>{{ resume.last_name }}
                </h1>
                <div class="w-16 h-2 bg-indigo-600"></div>
            </div>
            <div class="text-right space-y-1">
                <p class="text-sm font-black uppercase tracking-widest text-indigo-600 mb-4">{{ resume.job_title }}</p>
                <p class="text-[11px] font-medium text-slate-400">{{ resume.email }}</p>
                <p class="text-[11px] font-medium text-slate-400">{{ resume.phone }}</p>
                <p class="text-[11px] font-medium text-slate-400">{{ resume.location }}</p>
            </div>
        </header>

        <div class="grid grid-cols-12 gap-16">
            <!-- Summary Column -->
            <div class="col-span-4">
                <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-6">About</h3>
                <p class="text-xs leading-relaxed text-slate-500 pr-4">
                    {{ resume.summary }}
                </p>
                
                <!-- Skills Injected Here -->
                <div v-if="resume.skills?.length" class="mt-16">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-6">Expertise</h3>
                    <div class="flex flex-col gap-2">
                        <span v-for="skill in resume.skills" :key="skill.id" class="text-[11px] font-bold text-slate-800">
                            + {{ skill.name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Experience Column -->
            <div class="col-span-8">
                <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-8">Selected Experience</h3>
                <div class="space-y-16">
                    <div v-for="exp in resume.experiences" :key="exp.id" class="group relative">
                        <div class="absolute -left-8 top-0 bottom-0 w-px bg-slate-100 group-hover:bg-indigo-500 transition-colors"></div>
                        <div class="mb-4">
                            <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest block mb-2">{{ exp.start_date }} — {{ exp.current ? 'Now' : exp.end_date }}</span>
                            <h4 class="text-xl font-black text-slate-900 leading-tight mb-1">{{ exp.job_title }}</h4>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ exp.company }}</p>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed whitespace-pre-wrap">
                            {{ exp.description }}
                        </p>
                    </div>
                </div>

                <!-- Education -->
                <div v-if="resume.education?.length" class="mt-24">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300 mb-8">Education</h3>
                    <div class="grid grid-cols-2 gap-8">
                        <div v-for="edu in resume.education" :key="edu.id">
                            <p class="text-xs font-black text-slate-900 mb-1">{{ edu.degree }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">{{ edu.institution }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
