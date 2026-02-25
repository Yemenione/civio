<script setup>
const props = defineProps(['resume', 'editable']);
const emit = defineEmits(['section-click', 'update-field']);

const handleEdit = (section, field, id, e) => {
    if (!props.editable) return;
    emit('update-field', section, field, id, e.target.innerText);
};
</script>

<template>
    <div class="resume-preview-wrapper min-h-[297mm] bg-white text-black font-serif p-12 leading-snug">
        <!-- Ivy League Header: Centered, Caps, Serif -->
        <header class="text-center mb-6 border-b border-black pb-4">
            <h1 
                class="text-3xl font-bold uppercase tracking-wide mb-2"
                :contenteditable="editable"
                @blur="handleEdit('personal', 'first_name', null, $event)"
            >
                {{ resume.first_name }} {{ resume.last_name }}
            </h1>
            
            <div class="flex justify-center flex-wrap gap-x-4 text-[10pt]">
                <span v-if="resume.location">{{ resume.location }}</span>
                <span v-if="resume.location && resume.phone">•</span>
                <span v-if="resume.phone">{{ resume.phone }}</span>
                <span v-if="resume.phone && resume.email">•</span>
                <span v-if="resume.email">{{ resume.email }}</span>
                <span v-if="resume.email && resume.linkedin_url">•</span>
                <span v-if="resume.linkedin_url">LinkedIn</span>
            </div>
        </header>

        <!-- Education (Ivy League puts Education first usually, but we follow resume order) -->
        <section v-if="resume.education?.length" class="mb-4">
            <h3 class="text-[11pt] font-bold uppercase border-b border-black mb-3">Education</h3>
            <div v-for="edu in resume.education" :key="edu.id" class="mb-3">
                <div class="flex justify-between items-baseline">
                    <span class="font-bold text-[11pt]">{{ edu.institution }}</span>
                    <span class="text-[10pt] font-bold">{{ edu.start_date }} – {{ edu.end_date }}</span>
                </div>
                <div class="flex justify-between items-baseline">
                    <span class="text-[10pt] italic">{{ edu.degree }}</span>
                    <span v-if="edu.location" class="text-[10pt]">{{ edu.location }}</span>
                </div>
                <p v-if="edu.description" class="text-[10pt] mt-1">{{ edu.description }}</p>
            </div>
        </section>

        <!-- Experience -->
        <section v-if="resume.experiences?.length" class="mb-4">
            <h3 class="text-[11pt] font-bold uppercase border-b border-black mb-3">Professional Experience</h3>
            <div v-for="exp in resume.experiences" :key="exp.id" class="mb-4">
                <div class="flex justify-between items-baseline">
                    <span class="font-bold text-[11pt]">{{ exp.company }}</span>
                    <span class="text-[10pt] font-bold">{{ exp.start_date }} – {{ exp.current ? 'Present' : exp.end_date }}</span>
                </div>
                <div class="flex justify-between items-baseline mb-1">
                    <span class="text-[10pt] italic font-semibold">{{ exp.job_title }}</span>
                    <span v-if="exp.location" class="text-[10pt]">{{ exp.location }}</span>
                </div>
                <div class="text-[10pt] text-justify pl-0">
                    {{ exp.description }}
                </div>
            </div>
        </section>

        <!-- Skills & Languages (Combined for Density) -->
        <section v-if="resume.skills?.length || resume.languages?.length" class="mb-4">
            <h3 class="text-[11pt] font-bold uppercase border-b border-black mb-3">Skills & Languages</h3>
            <div class="text-[10pt]">
                <p v-if="resume.languages?.length">
                    <span class="font-bold">Languages:</span> 
                    {{ resume.languages.map(l => l.name + (l.proficiency ? ` (${l.proficiency})` : '')).join(', ') }}
                </p>
                <p v-if="resume.skills?.length">
                    <span class="font-bold">Technical Skills:</span> 
                    {{ resume.skills.map(s => s.name).join(', ') }}
                </p>
            </div>
        </section>

        <!-- Projects -->
        <section v-if="resume.projects?.length" class="mb-4">
            <h3 class="text-[11pt] font-bold uppercase border-b border-black mb-3">Leadership & Projects</h3>
            <div v-for="proj in resume.projects" :key="proj.id" class="mb-2">
                <div class="flex justify-between items-baseline">
                    <span class="font-bold text-[11pt]">{{ proj.title }}</span>
                </div>
                <p class="text-[10pt]">{{ proj.description }}</p>
            </div>
        </section>
    </div>
</template>
