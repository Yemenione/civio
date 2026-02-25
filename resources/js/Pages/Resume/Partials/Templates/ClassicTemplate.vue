<script setup>
import { useI18n } from 'vue-i18n';
const props = defineProps(['resume']);
const { t } = useI18n();
</script>
<template>
    <div class="p-12 text-gray-900 bg-white min-h-[297mm] font-serif leading-normal" :dir="resume.language === 'ar' ? 'rtl' : 'ltr'">
        <header class="text-center border-b-2 border-gray-800 pb-6 mb-8">
            <h1 class="text-4xl font-bold uppercase tracking-widest mb-2">{{ resume.first_name }} {{ resume.last_name }}</h1>
            <p class="text-xl text-gray-700 tracking-wide">{{ resume.job_title }}</p>
            <div class="flex justify-center gap-4 text-sm mt-4 text-gray-600 flex-wrap font-sans">
                <span v-if="resume.email">{{ resume.email }}</span>
                <span v-if="resume.phone">| {{ resume.phone }}</span>
                <span v-if="resume.address">| {{ resume.address }}</span>
            </div>
        </header>

        <div class="space-y-8">
            <section v-if="resume.summary">
                <h2 class="text-xl font-bold uppercase border-b border-gray-400 mb-4 pb-1 tracking-wider">{{ t('summary') }}</h2>
                <p class="text-md leading-relaxed text-justify text-gray-800">{{ resume.summary }}</p>
            </section>

            <section v-if="resume.experiences && resume.experiences.length">
                <h2 class="text-xl font-bold uppercase border-b border-gray-400 mb-4 pb-1 tracking-wider">{{ t('experience') }}</h2>
                <div v-for="exp in resume.experiences" :key="exp.id" class="mb-6">
                    <div class="flex justify-between items-baseline mb-1">
                        <h3 class="font-bold text-lg">{{ exp.job_title }}</h3>
                        <span class="text-sm font-sans text-gray-600 font-semibold">{{ exp.start_date }} - {{ exp.current ? t('current') : exp.end_date }}</span>
                    </div>
                    <div class="text-md font-semibold text-gray-700 mb-2 italic">{{ exp.company }}</div>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap leading-relaxed">{{ exp.description }}</p>
                </div>
            </section>

            <section v-if="resume.education && resume.education.length">
                <h2 class="text-xl font-bold uppercase border-b border-gray-400 mb-4 pb-1 tracking-wider">{{ t('education') }}</h2>
                <div v-for="edu in resume.education" :key="edu.id" class="mb-5">
                    <div class="flex justify-between items-baseline mb-1">
                        <h3 class="font-bold text-lg">{{ edu.institution }}</h3>
                        <span class="text-sm font-sans text-gray-600 font-semibold">{{ edu.start_date }} - {{ edu.end_date }}</span>
                    </div>
                    <div class="text-md">{{ edu.degree }}</div>
                    <p class="text-sm text-gray-600 mt-1">{{ edu.description }}</p>
                </div>
            </section>

            <section v-if="resume.skills && resume.skills.length">
                <h2 class="text-xl font-bold uppercase border-b border-gray-400 mb-4 pb-1 tracking-wider">{{ t('skills') }}</h2>
                <div class="flex flex-wrap gap-x-8 gap-y-2">
                    <span v-for="skill in resume.skills" :key="skill.id" class="text-md font-medium">• {{ skill.name }}</span>
                </div>
            </section>

             <section v-if="resume.projects && resume.projects.length">
                <h2 class="text-xl font-bold uppercase border-b border-gray-400 mb-4 pb-1 tracking-wider">{{ t('projects') }}</h2>
                <div v-for="proj in resume.projects" :key="proj.id" class="mb-4">
                    <div class="flex justify-between items-baseline">
                         <h3 class="font-bold text-lg">{{ proj.title }}</h3>
                         <a v-if="proj.link" :href="proj.link" class="text-blue-800 text-sm hover:underline font-sans">{{ proj.link }}</a>
                    </div>
                    <p class="text-sm text-gray-700 mt-1">{{ proj.description }}</p>
                </div>
            </section>
        </div>
    </div>
</template>
