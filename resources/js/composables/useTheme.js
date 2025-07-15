import { ref, onMounted, watch } from 'vue'

const isDark = ref(false)
const theme = ref('light')

export function useTheme() {
    // Inicializar tema baseado na preferência salva ou preferência do sistema
    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme')
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        
        if (savedTheme) {
            theme.value = savedTheme
            if (savedTheme === 'system') {
                isDark.value = prefersDark
            } else {
                isDark.value = savedTheme === 'dark'
            }
        } else {
            theme.value = 'system'
            isDark.value = prefersDark
        }
        
        applyTheme()
    }
    
    // Aplicar tema ao documento
    const applyTheme = () => {
        const html = document.documentElement
        const body = document.body
        
        if (isDark.value) {
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
        
        // Disparar evento personalizado para notificar mudança de tema
        window.dispatchEvent(new CustomEvent('theme-changed', { 
            detail: { theme: theme.value, isDark: isDark.value } 
        }))
    }
    
    // Alternar tema
    const toggleTheme = () => {
        isDark.value = !isDark.value
        theme.value = isDark.value ? 'dark' : 'light'
        localStorage.setItem('theme', theme.value)
        applyTheme()
    }
    
    // Definir tema específico
    const setTheme = (newTheme) => {
        theme.value = newTheme
        
        if (newTheme === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
            isDark.value = prefersDark
        } else {
            isDark.value = newTheme === 'dark'
        }
        
        localStorage.setItem('theme', newTheme)
        applyTheme()
    }
    
    // Observar mudanças na preferência do sistema
    const watchSystemPreference = () => {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
        
        const handleChange = (e) => {
            // Só atualizar se o tema estiver definido como 'system'
            if (theme.value === 'system') {
                isDark.value = e.matches
                applyTheme()
            }
        }
        
        mediaQuery.addEventListener('change', handleChange)
        
        return () => mediaQuery.removeEventListener('change', handleChange)
    }
    
    // Inicializar quando o composable for usado
    onMounted(() => {
        initTheme()
        watchSystemPreference()
    })
    
    // Observar mudanças no tema para aplicar imediatamente
    watch(theme, applyTheme, { immediate: true })
    
    return {
        isDark,
        theme,
        toggleTheme,
        setTheme,
        initTheme
    }
} 