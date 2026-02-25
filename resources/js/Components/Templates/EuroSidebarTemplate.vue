<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps({ 
    resume: Object,
    editable: Boolean 
});
const emit = defineEmits(['section-click']);

// European layout typically uses a distinct sidebar
</script>

<template>
    <div class="euro-sidebar bg-white text-gray-900 w-full min-h-[297mm] flex overflow-hidden" 
         :style="{ 
             fontFamily: resume.design_options?.font_family || 'Inter, sans-serif',
             fontSize: resume.design_options?.font_size || '10pt',
             lineHeight: resume.design_options?.line_height || 1.5,
             '--accent': resume.design_options?.accent_color || '#2c3e50',
             '--surface': resume.design_options?.page_texture === 'paper' ? '#f8f9fa' : '#ffffff'
         }">
        
        <!-- Sidebar (Left Column) -->
        <aside class="w-1/3 bg-slate-50 border-r border-gray-200 p-8 flex flex-col gap-6"
              :style="{ backgroundColor: 'var(--surface)' }">
            
            <!-- Photo (optional but common in Europe) -->
            <div v-if="resume.photo" class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4" :style="{ borderColor: 'var(--accent)' }">
                <img :src="resume.photo" alt="Profile" class="w-full h-full object-cover" />
            </div>

            <!-- Contact -->
            <div class="personal border-b border-gray-200 pb-4 transition-all"
                 :class="[editable ? 'editable-section' : '']"
                 @click="editable && emit('section-click', 'personal')">
                <h3 class="text-xs font-bold uppercase tracking-widest mb-3" :style="{ color: 'var(--accent)' }">{{ t('contact') }}</h3>
                <ul class="text-sm space-y-2 text-gray-700">
                    <li 
                        v-if="resume.email || editable" 
                        class="break-all focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                        :contenteditable="editable"
                        @blur="(e) => editable && emit('update-field', 'personal', 'email', null, e.target.innerText)"
                    >{{ resume.email || (editable ? 'Email' : '') }}</li>
                    <li 
                        v-if="resume.phone || editable" 
                        class="focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                        :contenteditable="editable"
                        @blur="(e) => editable && emit('update-field', 'personal', 'phone', null, e.target.innerText)"
                    >{{ resume.phone || (editable ? 'Phone' : '') }}</li>
                    <li v-if="resume.city || editable" class="focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1">
                        <span 
                            :contenteditable="editable" 
                            @blur="(e) => editable && emit('update-field', 'personal', 'city', null, e.target.innerText)"
                        >{{ resume.city || (editable ? 'City' : '') }}</span>
                        <span v-if="resume.country || editable">, </span>
                        <span 
                            :contenteditable="editable" 
                            @blur="(e) => editable && emit('update-field', 'personal', 'country', null, e.target.innerText)"
                        >{{ resume.country || (editable ? 'Country' : '') }}</span>
                    </li>
                    <li v-if="resume.linkedin">{{ t('linkedin') }}</li>
                    <li v-if="resume.website || resume.portfolio">{{ t('website') }}</li>
                </ul>
            </div>

            <!-- Sidebar Sections (Skills, Languages) -->
            <template v-for="sectionKey in ['skills', 'languages']" :key="sectionKey">
                <div v-if="!resume.design_options?.hidden_sections?.includes(sectionKey)">
                    
                    <div v-if="sectionKey === 'skills' && resume.skills?.length" 
                         class="mb-4 transition-all"
                         :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'skills')">
                        <h3 class="text-xs font-bold uppercase tracking-widest mb-3" :style="{ color: 'var(--accent)' }">{{ t('skills') }}</h3>
                        <div class="space-y-3">
                            <div v-for="skill in resume.skills" :key="skill.id">
                                <div class="flex justify-between text-sm mb-1">
                                    <span 
                                        class="font-medium text-gray-800 focus:outline-none focus:bg-indigo-50/50 rounded px-1"
                                        :contenteditable="editable"
                                        @blur="(e) => editable && emit('update-field', 'skills', 'name', skill.id, e.target.innerText)"
                                    >{{ skill.name }}</span>
                                    <span v-if="skill.level" class="text-xs text-gray-500">{{ t(`proficiency_${skill.level.toLowerCase()}`) || skill.level }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5" v-if="['Beginner', 'Intermediate', 'Advanced', 'Expert'].includes(skill.level)">
                                    <div class="h-1.5 rounded-full opacity-80" 
                                         :style="{ 
                                             backgroundColor: 'var(--accent)',
                                             width: skill.level === 'Beginner' ? '25%' : skill.level === 'Intermediate' ? '50%' : skill.level === 'Advanced' ? '75%' : '100%' 
                                         }">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="sectionKey === 'languages' && resume.languages?.length" 
                         class="transition-all"
                         :class="[editable ? 'editable-section' : '']"
                         @click="editable && emit('section-click', 'languages')">
                        <h3 class="text-xs font-bold uppercase tracking-widest mb-3" :style="{ color: 'var(--accent)' }">{{ t('languages') }}</h3>
                        <ul class="space-y-2 text-sm text-gray-800 font-medium">
                            <li v-for="lang in resume.languages" :key="lang.id" class="flex justify-between">
                                <span 
                                    class="focus:outline-none focus:bg-indigo-50/50 rounded px-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'languages', 'name', lang.id, e.target.innerText)"
                                >{{ lang.name }}</span>
                                <span v-if="lang.level" class="text-xs text-gray-500 font-normal">{{ t(`proficiency_${lang.level.toLowerCase()}`) || lang.level }}</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </template>
        </aside>

        <!-- Main Content (Right Column) -->
        <main class="w-2/3 p-8">
            
            <!-- Header -->
            <header class="mb-8 transition-all"
                    :class="[editable ? 'editable-section' : '']"
                    @click="editable && emit('section-click', 'personal')">
                <h1 
                    class="text-4xl font-black uppercase tracking-tight text-gray-900 leading-tight mb-1 focus:outline-none focus:bg-indigo-50/50 rounded px-1" 
                    :style="{ color: 'var(--accent)' }"
                    :contenteditable="editable"
                    @blur="(e) => editable && emit('update-field', 'personal', 'fullname', null, e.target.innerText)"
                >{{ resume.fullname || 'Your Name' }}</h1>
                
                <h2 
                    v-if="resume.jobtitle || editable" 
                    class="text-xl text-gray-500 font-light tracking-wide focus:outline-none focus:bg-indigo-50/50 rounded px-1"
                    :contenteditable="editable"
                    @blur="(e) => editable && emit('update-field', 'personal', 'jobtitle', null, e.target.innerText)"
                >{{ resume.jobtitle || (editable ? 'Job Title' : '') }}</h2>
                
                <p 
                    v-if="resume.summary || editable" 
                    v-show="!resume.design_options?.hidden_sections?.includes('summary')"
                    class="mt-6 text-sm text-gray-700 leading-relaxed font-serif italic border-l-4 pl-4 focus:outline-none focus:bg-indigo-50/50 rounded" 
                    :style="{ borderLeftColor: 'var(--accent)'}"
                    :contenteditable="editable"
                    @blur="(e) => editable && emit('update-field', 'personal', 'summary', null, e.target.innerText)"
                >{{ resume.summary || (editable ? 'Summary...' : '') }}</p>
            </header>

            <!-- Main Sections (Experience, Education, Projects, Certifications) -->
            <template v-for="sectionKey in (resume.design_options?.section_order || ['experience', 'education', 'certifications', 'projects'])" :key="sectionKey">
                <div v-if="!['skills', 'languages', 'summary'].includes(sectionKey) && !resume.design_options?.hidden_sections?.includes(sectionKey)">

                    <!-- Experience -->
                    <section v-if="sectionKey === 'experience' && resume.experiences?.length" 
                             class="mb-8 transition-all group"
                             :class="[editable ? 'editable-section' : '']"
                             @click="editable && emit('section-click', 'experience')">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-px bg-gray-300"></div>
                            <h3 class="text-lg font-bold uppercase tracking-widest text-gray-800" :style="{ color: 'var(--accent)' }">{{ t('experience') }}</h3>
                        </div>
                        
                        <div class="relative border-l border-gray-200 ml-4 space-y-6">
                            <div v-for="exp in resume.experiences" :key="exp.id" class="relative pl-6">
                                <div class="absolute w-3 h-3 rounded-full -left-1.5 top-1.5 bg-white border-2" :style="{ borderColor: 'var(--accent)' }"></div>
                                <h4 
                                    class="font-bold text-gray-900 text-md focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'experiences', 'jobtitle', exp.id, e.target.innerText)"
                                >{{ exp.jobtitle }}</h4>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                                    <span 
                                        class="font-semibold text-gray-700 focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                        :contenteditable="editable"
                                        @blur="(e) => editable && emit('update-field', 'experiences', 'company', exp.id, e.target.innerText)"
                                    >{{ exp.company }}</span>
                                    <span>•</span>
                                    <span>{{ exp.start_date }} – {{ exp.current ? t('current') : exp.end_date }}</span>
                                </div>
                                <p 
                                    v-if="exp.description || editable" 
                                    class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'experiences', 'description', exp.id, e.target.innerText)"
                                >{{ exp.description || (editable ? 'Add description...' : '') }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Education -->
                    <section v-if="sectionKey === 'education' && resume.education?.length" 
                             class="mb-8 transition-all group"
                             :class="[editable ? 'editable-section' : '']"
                             @click="editable && emit('section-click', 'education')">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-px bg-gray-300"></div>
                            <h3 class="text-lg font-bold uppercase tracking-widest text-gray-800" :style="{ color: 'var(--accent)' }">{{ t('education') }}</h3>
                        </div>
                        
                        <div class="relative border-l border-gray-200 ml-4 space-y-5">
                            <div v-for="edu in resume.education" :key="edu.id" class="relative pl-6">
                                <div class="absolute w-3 h-3 rounded-full -left-1.5 top-1.5 bg-white border-2" :style="{ borderColor: 'var(--accent)' }"></div>
                                <h4 
                                    class="font-bold text-gray-900 text-md focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'education', 'degree', edu.id, e.target.innerText)"
                                >{{ edu.degree }}</h4>
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                                    <span 
                                        class="font-semibold text-gray-700 focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                        :contenteditable="editable"
                                        @blur="(e) => editable && emit('update-field', 'education', 'institution', edu.id, e.target.innerText)"
                                    >{{ edu.institution }}</span>
                                    <span>•</span>
                                    <span>{{ edu.start_date }} – {{ edu.current ? t('current') : edu.end_date }}</span>
                                </div>
                                <p 
                                    v-if="edu.description || editable" 
                                    class="text-sm text-gray-700 focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'education', 'description', edu.id, e.target.innerText)"
                                >{{ edu.description || (editable ? 'Add description...' : '') }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- Certifications -->
                    <section v-if="sectionKey === 'certifications' && resume.certifications?.length" 
                             class="mb-8 transition-all"
                             :class="[editable ? 'editable-section' : '']"
                             @click="editable && emit('section-click', 'certifications')">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-px bg-gray-300"></div>
                            <h3 class="text-lg font-bold uppercase tracking-widest text-gray-800" :style="{ color: 'var(--accent)' }">{{ t('certifications') }}</h3>
                        </div>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 pl-4">
                            <div v-for="cert in resume.certifications" :key="cert.id" class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <h4 
                                    class="font-bold text-gray-900 text-sm focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'certifications', 'name', cert.id, e.target.innerText)"
                                >{{ cert.name }}</h4>
                                <div class="text-xs text-gray-500 mt-1 flex justify-between">
                                    <span 
                                        class="focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                        :contenteditable="editable"
                                        @blur="(e) => editable && emit('update-field', 'certifications', 'issuer', cert.id, e.target.innerText)"
                                    >{{ cert.issuer }}</span>
                                    <span>{{ cert.date }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Projects -->
                    <section v-if="sectionKey === 'projects' && resume.projects?.length" 
                             class="mb-6 transition-all"
                             :class="[editable ? 'editable-section' : '']"
                             @click="editable && emit('section-click', 'projects')">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-px bg-gray-300"></div>
                            <h3 class="text-lg font-bold uppercase tracking-widest text-gray-800" :style="{ color: 'var(--accent)' }">{{ t('projects') }}</h3>
                        </div>
                        <div class="space-y-4 pl-4 border-l border-gray-200 ml-4">
                            <div v-for="proj in resume.projects" :key="proj.id" class="relative pl-6">
                                <div class="absolute w-2 h-2 rounded-full -left-1 top-1.5 bg-gray-300" :style="{ backgroundColor: 'var(--accent)' }"></div>
                                <h4 
                                    class="font-bold text-gray-900 text-sm focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'projects', 'name', proj.id, e.target.innerText)"
                                >{{ proj.name }}</h4>
                                <p 
                                    v-if="proj.description || editable" 
                                    class="text-sm text-gray-700 mt-1 whitespace-pre-wrap focus:outline-none focus:bg-indigo-50/50 rounded px-1 -mx-1"
                                    :contenteditable="editable"
                                    @blur="(e) => editable && emit('update-field', 'projects', 'description', proj.id, e.target.innerText)"
                                >{{ proj.description || (editable ? 'Add description...' : '') }}</p>
                            </div>
                        </div>
                    </section>

                </div>
            </template>
        </main>
    </div>
</template>
