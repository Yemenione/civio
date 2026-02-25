<script setup>
const props = defineProps(['resume', 'editable']);
const emit = defineEmits(['section-click', 'update-field']);

const handleEdit = (section, field, id, e) => {
    if (!props.editable) return;
    emit('update-field', section, field, id, e.target.innerText);
};
</script>

<template>
    <div class="resume-preview-wrapper min-h-[297mm] bg-white text-slate-800 font-sans p-10">
        <!-- Tech Header -->
        <header class="mb-10 pb-6 border-b-4 border-[rgb(var(--accent-rgb))]">
            <h1 
                class="text-5xl font-mono font-black tracking-tighter mb-2 text-[rgb(var(--accent-rgb))]"
                :contenteditable="editable"
                @blur="handleEdit('personal', 'first_name', null, $event)"
            >
                {{ resume.first_name }} {{ resume.last_name }}
            </h1>
            <p class="text-xl font-bold uppercase tracking-widest mb-4 text-slate-600">{{ resume.job_title }}</p>
            
            <div class="flex flex-wrap gap-4 text-xs font-mono text-slate-500">
                <span class="px-2 py-1 bg-slate-100 rounded">{{ resume.email }}</span>
                <span class="px-2 py-1 bg-slate-100 rounded">{{ resume.phone }}</span>
                <span v-if="resume.location" class="px-2 py-1 bg-slate-100 rounded">{{ resume.location }}</span>
            </div>
        </header>

        <div class="grid grid-cols-12 gap-8">
            <!-- Main Content -->
            <div class="col-span-8 space-y-10">
                <!-- Summary -->
                <section v-if="resume.summary">
                    <h3 class="text-sm font-mono font-bold uppercase text-[rgb(var(--accent-rgb))] mb-3 flex items-center gap-2">
                        <span class="text-xl">01.</span> Profile
                    </h3>
                    <p class="text-sm leading-relaxed text-slate-600 font-medium">
                        {{ resume.summary }}
                    </p>
                </section>

                <!-- Experience -->
                <section v-if="resume.experiences?.length">
                    <h3 class="text-sm font-mono font-bold uppercase text-[rgb(var(--accent-rgb))] mb-6 flex items-center gap-2">
                        <span class="text-xl">02.</span> Experience
                    </h3>
                    <div class="space-y-8 pl-4 border-l-2 border-slate-100">
                        <div v-for="exp in resume.experiences" :key="exp.id" class="relative">
                            <div class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-white border-2 border-[rgb(var(--accent-rgb))]"></div>
                            <div class="flex justify-between items-baseline mb-1">
                                <h4 class="text-lg font-bold text-slate-900">{{ exp.job_title }}</h4>
                                <span class="text-xs font-mono font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded">{{ exp.start_date }} — {{ exp.current ? 'Now' : exp.end_date }}</span>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-widest text-[rgb(var(--accent-rgb))] mb-2">{{ exp.company }}</p>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ exp.description }}</p>
                        </div>
                    </div>
                </section>

                <!-- Projects -->
                <section v-if="resume.projects?.length">
                    <h3 class="text-sm font-mono font-bold uppercase text-[rgb(var(--accent-rgb))] mb-4 flex items-center gap-2">
                        <span class="text-xl">03.</span> Projects
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div v-for="proj in resume.projects" :key="proj.id" class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                            <div class="flex justify-between items-baseline mb-2">
                                <span class="font-bold text-sm text-slate-900">{{ proj.title }}</span>
                                <span class="text-[10px] font-mono text-slate-400" v-if="proj.link">🔗 Link</span>
                            </div>
                            <p class="text-xs text-slate-500">{{ proj.description }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="col-span-4 space-y-10">
                <!-- Skills (Tag Cloud) -->
                <section v-if="resume.skills?.length">
                    <h3 class="text-sm font-mono font-bold uppercase text-[rgb(var(--accent-rgb))] mb-4">Stack</h3>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="skill in resume.skills" :key="skill.id" class="px-2 py-1 bg-[rgb(var(--accent-rgb))] bg-opacity-10 text-[rgb(var(--accent-rgb))] text-[10px] font-bold rounded-md border border-[rgb(var(--accent-rgb))] border-opacity-20">
                            {{ skill.name }}
                        </span>
                    </div>
                </section>

                <!-- Education -->
                <section v-if="resume.education?.length">
                    <h3 class="text-sm font-mono font-bold uppercase text-[rgb(var(--accent-rgb))] mb-4">Education</h3>
                    <div class="space-y-4">
                        <div v-for="edu in resume.education" :key="edu.id">
                            <p class="text-xs font-bold text-slate-900">{{ edu.institution }}</p>
                            <p class="text-[10px] text-slate-500">{{ edu.degree }}</p>
                            <p class="text-[10px] font-mono text-slate-400 mt-1">{{ edu.start_date }}</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
