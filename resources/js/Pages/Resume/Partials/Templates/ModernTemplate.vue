<script setup>
import { useI18n } from 'vue-i18n';
const props = defineProps(['resume']);
const { t } = useI18n();
</script>
<template>
    <div class="flex min-h-[297mm] bg-white font-sans text-gray-800" :dir="resume.language === 'ar' ? 'rtl' : 'ltr'">
        <!-- Left Column -->
        <div class="w-1/3 bg-slate-900 text-white p-8 flex flex-col gap-8">
            <div class="text-center" v-if="resume.photo">
                 <!-- Photo placeholder -->
                 <div class="w-32 h-32 bg-gray-700 rounded-full mx-auto mb-4 border-4 border-teal-500"></div>
            </div>

            <div>
                <h3 class="text-sm font-bold border-b border-slate-700 pb-2 mb-4 text-teal-400 uppercase tracking-widest">{{ t('personal_details') }}</h3>
                <div class="space-y-4 text-sm font-light">
                     <div v-if="resume.email">
                        <span class="block text-slate-400 text-xs uppercase font-bold mb-1">{{ t('email') }}</span>
                        {{ resume.email }}
                    </div>
                    <div v-if="resume.phone">
                        <span class="block text-slate-400 text-xs uppercase font-bold mb-1">{{ t('phone') }}</span>
                        {{ resume.phone }}
                    </div>
                     <div v-if="resume.address">
                        <span class="block text-slate-400 text-xs uppercase font-bold mb-1">{{ t('address') }}</span>
                        {{ resume.address }}
                    </div>
                </div>
            </div>

            <div v-if="resume.skills && resume.skills.length">
                <h3 class="text-sm font-bold border-b border-slate-700 pb-2 mb-4 text-teal-400 uppercase tracking-widest">{{ t('skills') }}</h3>
                <div class="space-y-2">
                    <div v-for="skill in resume.skills" :key="skill.id" class="text-sm">
                        {{ skill.name }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="w-2/3 p-10 bg-white">
            <header class="mb-10">
                <h1 class="text-5xl font-extrabold text-slate-900 uppercase tracking-tight leading-none mb-2 text-teal-700">{{ resume.first_name }} <br/><span class="text-slate-900">{{ resume.last_name }}</span></h1>
                <p class="text-2xl text-slate-500 font-light tracking-wide mt-4">{{ resume.job_title }}</p>
            </header>

            <div class="space-y-10">
                <section v-if="resume.summary">
                    <h2 class="text-lg font-bold text-slate-800 uppercase tracking-widest mb-4 flex items-center">
                        <span class="w-10 h-1.5 bg-teal-500 mr-4 rounded-full"></span>{{ t('summary') }}
                    </h2>
                    <p class="text-sm leading-7 text-slate-600 font-medium">{{ resume.summary }}</p>
                </section>

                <section v-if="resume.experiences && resume.experiences.length">
                    <h2 class="text-lg font-bold text-slate-800 uppercase tracking-widest mb-6 flex items-center">
                        <span class="w-10 h-1.5 bg-teal-500 mr-4 rounded-full"></span>{{ t('experience') }}
                    </h2>
                    <div v-for="exp in resume.experiences" :key="exp.id" class="mb-8 border-l-2 border-slate-200 pl-6 relative ml-2">
                        <div class="absolute w-3 h-3 bg-teal-500 rounded-full -left-[7px] top-1.5 ring-4 ring-white"></div>
                        <div class="flex justify-between items-baseline mb-1">
                            <h3 class="font-bold text-xl text-slate-800">{{ exp.job_title }}</h3>
                            <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2 py-1 rounded">{{ exp.start_date }} - {{ exp.current ? t('current') : exp.end_date }}</span>
                        </div>
                        <div class="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wide">{{ exp.company }}</div>
                        <p class="text-sm text-slate-600 whitespace-pre-wrap leading-relaxed">{{ exp.description }}</p>
                    </div>
                </section>

                 <section v-if="resume.education && resume.education.length">
                    <h2 class="text-lg font-bold text-slate-800 uppercase tracking-widest mb-6 flex items-center">
                        <span class="w-10 h-1.5 bg-teal-500 mr-4 rounded-full"></span>{{ t('education') }}
                    </h2>
                    <div v-for="edu in resume.education" :key="edu.id" class="mb-6">
                        <h3 class="font-bold text-lg text-slate-800">{{ edu.institution }}</h3>
                        <p class="text-sm text-teal-600 font-bold uppercase mt-1">{{ edu.degree }}</p>
                        <p class="text-xs text-slate-400 mt-1 mb-2 font-mono">{{ edu.start_date }} - {{ edu.end_date }}</p>
                         <p class="text-sm text-slate-600">{{ edu.description }}</p>
                    </div>
                </section>
                
                 <section v-if="resume.projects && resume.projects.length">
                    <h2 class="text-lg font-bold text-slate-800 uppercase tracking-widest mb-6 flex items-center">
                        <span class="w-10 h-1.5 bg-teal-500 mr-4 rounded-full"></span>{{ t('projects') }}
                    </h2>
                    <div v-for="proj in resume.projects" :key="proj.id" class="mb-4">
                        <h3 class="font-bold text-slate-800">{{ proj.title }}</h3>
                        <p class="text-sm text-slate-600 mt-1">{{ proj.description }}</p>
                        <a v-if="proj.link" :href="proj.link" class="text-xs text-teal-600 hover:underline block mt-1 font-mono">{{ proj.link }}</a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
