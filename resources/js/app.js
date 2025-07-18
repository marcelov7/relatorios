import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import 'trix'
import 'trix/dist/trix.css'

// Inicializar tema antes da aplicação
const initTheme = () => {
    const savedTheme = localStorage.getItem('theme')
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    
    let theme = 'light'
    let isDark = false
    
    if (savedTheme) {
        theme = savedTheme
        if (savedTheme === 'system') {
            isDark = prefersDark
        } else {
            isDark = savedTheme === 'dark'
        }
    } else {
        theme = 'system'
        isDark = prefersDark
    }
    
    const html = document.documentElement
    const body = document.body
    
    if (isDark) {
        html.classList.add('dark')
        body.classList.add('dark')
        html.style.colorScheme = 'dark'
        html.setAttribute('data-theme', 'dark')
    } else {
        html.classList.remove('dark')
        body.classList.remove('dark')
        html.style.colorScheme = 'light'
        html.setAttribute('data-theme', 'light')
    }
}

// Inicializar tema imediatamente
initTheme();



const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
