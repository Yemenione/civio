import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createI18n } from 'vue-i18n';
import CookieBanner from './Components/CookieBanner.vue';

import en from './locales/en.json';
import ar from './locales/ar.json';
import fr from './locales/fr.json';
import es from './locales/es.json';
import de from './locales/de.json';
import it from './locales/it.json';
import pt from './locales/pt.json';
import nl from './locales/nl.json';

const appName = import.meta.env.VITE_APP_NAME || 'Civio';

const i18n = createI18n({
    locale: localStorage.getItem('locale') || 'en',
    fallbackLocale: 'en',
    messages: { en, ar, fr, es, de, it, pt, nl },
    legacy: false
});

if (i18n.global.locale.value === 'ar') {
    document.dir = 'rtl';
} else {
    document.dir = 'ltr';
}

createInertiaApp({
    title: (title) => `${title} — Civio`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n);

        // Mount global CookieBanner
        const cookieMount = document.createElement('div');
        document.body.appendChild(cookieMount);
        createApp(CookieBanner).use(i18n).mount(cookieMount);

        return vueApp.mount(el);
    },
    progress: {
        color: '#4f46e5',
    },
});
