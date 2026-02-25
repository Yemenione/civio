<template>
  <div class="relative" ref="dropdownRef">
    <!-- Current language button -->
    <button
      @click="isOpen = !isOpen"
      class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-slate-800/80 border border-slate-600 text-xs font-medium text-slate-300 hover:text-white hover:border-slate-500 transition-all"
    >
      <img :src="`https://flagcdn.com/w40/${currentLang.iso}.png`" class="w-5 h-3.5 object-cover rounded-sm shadow-sm" :alt="currentLang.label" />
      <span class="hidden sm:inline uppercase tracking-tight">{{ currentLang.code }}</span>
      <svg class="w-3 h-3 text-slate-500" :class="isOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown -->
    <div
      v-if="isOpen"
      class="absolute right-0 top-full mt-2 w-48 bg-slate-800 border border-slate-700 rounded-xl shadow-xl shadow-black/40 z-50 py-1.5 overflow-hidden"
    >
      <button
        v-for="lang in languages"
        :key="lang.code"
        @click="setLanguage(lang.code); isOpen = false"
        :class="[
          'flex items-center gap-3 w-full px-4 py-2.5 text-sm transition-colors',
          currentLocale === lang.code
            ? 'bg-indigo-600/20 text-indigo-300'
            : 'text-slate-300 hover:bg-slate-700 hover:text-white'
        ]"
      >
        <img :src="`https://flagcdn.com/w40/${lang.iso}.png`" class="w-5 h-3.5 object-cover rounded-sm shadow-sm" :alt="lang.label" />
        <span class="font-medium">{{ lang.label }}</span>
        <span v-if="currentLocale === lang.code" class="ml-auto text-indigo-400 text-xs">✓</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const isOpen = ref(false);
const dropdownRef = ref(null);

const languages = [
  { code: 'en', iso: 'gb', label: 'English' },
  { code: 'ar', iso: 'sa', label: 'العربية' },
  { code: 'fr', iso: 'fr', label: 'Français' },
  { code: 'es', iso: 'es', label: 'Español' },
  { code: 'de', iso: 'de', label: 'Deutsch' },
  { code: 'it', iso: 'it', label: 'Italiano' },
  { code: 'pt', iso: 'pt', label: 'Português' },
  { code: 'nl', iso: 'nl', label: 'Nederlands' },
];

const currentLocale = computed(() => locale.value);
const currentLang = computed(() => languages.find(l => l.code === locale.value) || languages[0]);

const setLanguage = (code) => {
  locale.value = code;
  localStorage.setItem('locale', code);
  document.dir = code === 'ar' ? 'rtl' : 'ltr';
};

// Close dropdown on outside click
const handleClickOutside = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

