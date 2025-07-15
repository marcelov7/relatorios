<template>
    <div class="relative">
        <!-- Botão Toggle -->
        <button 
            @click="handleToggle"
            class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            :title="getToggleTitle()"
        >
            <!-- Ícone Sol (Light Mode) -->
            <svg v-if="theme === 'light'" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            
            <!-- Ícone Lua (Dark Mode) -->
            <svg v-else-if="theme === 'dark'" class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            
            <!-- Ícone Sistema (System Mode) -->
            <svg v-else class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </button>
        
        <!-- Dropdown Menu (Opcional) -->
        <div v-if="showDropdown" 
             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
             @click.stop>
            <button 
                @click="handleThemeSelect('light')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                :class="{ 'bg-blue-50 text-blue-700': theme === 'light' }"
            >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Tema Claro
            </button>
            
            <button 
                @click="handleThemeSelect('dark')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                :class="{ 'bg-blue-50 text-blue-700': theme === 'dark' }"
            >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                Tema Escuro
            </button>
            
            <button 
                @click="handleThemeSelect('system')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                :class="{ 'bg-blue-50 text-blue-700': theme === 'system' }"
            >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Sistema
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useTheme } from '@/composables/useTheme'

const props = defineProps({
    variant: {
        type: String,
        default: 'simple', // 'simple' ou 'dropdown'
        validator: (value) => ['simple', 'dropdown'].includes(value)
    }
})

const { isDark, theme, toggleTheme, setTheme } = useTheme()
const showDropdown = ref(false)

// Função para lidar com o clique no botão
const handleToggle = () => {
    if (props.variant === 'dropdown') {
        showDropdown.value = !showDropdown.value
    } else {
        // Ciclo: light -> dark -> system -> light
        if (theme.value === 'light') {
            setTheme('dark')
        } else if (theme.value === 'dark') {
            setTheme('system')
        } else {
            setTheme('light')
        }
    }
}

// Função para obter o título do botão
const getToggleTitle = () => {
    if (props.variant === 'dropdown') {
        return 'Configurações de tema'
    }
    
    if (theme.value === 'light') {
        return 'Alternar para tema escuro'
    } else if (theme.value === 'dark') {
        return 'Alternar para tema do sistema'
    } else {
        return 'Alternar para tema claro'
    }
}

// Fechar dropdown quando clicar fora
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showDropdown.value = false
    }
}

// Fechar dropdown após selecionar tema
const handleThemeSelect = (newTheme) => {
    setTheme(newTheme)
    showDropdown.value = false
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.theme-toggle {
    position: relative;
}
</style> 