<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar Desktop -->
        <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white dark:bg-gray-800 shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0" 
               :class="{ 'translate-x-0': sidebarOpen }">
            <div class="flex flex-col h-full">
                <!-- Logo/Brand -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 h-16">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Relatórios</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sistema</p>
                        </div>
                    </div>
                    <button @click="toggleSidebar" class="p-2 rounded-md text-gray-500 hover:text-gray-700 md:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <Link href="/dashboard" 
                          class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group"
                          :class="isActive('/dashboard') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                        <svg class="w-5 h-5 mr-3 transition-colors" 
                             :class="isActive('/dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        </svg>
                        Dashboard
                    </Link>
                    
                    <Link href="/relatorios" 
                          class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group"
                          :class="isActive('/relatorios') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                        <svg class="w-5 h-5 mr-3 transition-colors"
                             :class="isActive('/relatorios') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Relatórios
                    </Link>

                    <!-- Motores -->
                    <Link href="/motores" 
                          class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group"
                          :class="isActive('/motores') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                        <svg class="w-5 h-5 mr-3 transition-colors"
                             :class="isActive('/motores') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Motores
                    </Link>

                    <!-- Equipamentos de Teste (apenas para admins) -->
                    <Link v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'" 
                          href="/equipamento-tests" 
                          class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group"
                          :class="isActive('/equipamento-tests') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                        <svg class="w-5 h-5 mr-3 transition-colors"
                             :class="isActive('/equipamento-tests') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h3m4 0V7a2 2 0 00-2-2h-7a2 2 0 00-2 2v10a2 2 0 002 2h7a2 2 0 002-2v-5z" />
                        </svg>
                        Equipamentos
                    </Link>

                    <!-- Seção Administrativa (apenas para admins) -->
                    <div v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'" class="pt-4">
                        <div class="px-4 mb-2">
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Administração
                            </h3>
                        </div>
                        
                        <Link href="/users" 
                              class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors group"
                              :class="isActive('/users') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'">
                            <svg class="w-5 h-5 mr-3 transition-colors"
                                 :class="isActive('/users') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            Usuários
                        </Link>
                    </div>
                </nav>
                
                <!-- Footer da Sidebar -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 mt-auto">
                    <!-- Informações do Usuário -->
                    <div class="flex items-center mb-3">
                        <a href="/profile" class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 hover:opacity-80 transition">
                            <span class="text-white text-sm font-medium">
                                {{ getInitials($page.props.auth.user?.name) }}
                            </span>
                        </a>
                        <div class="ml-3 min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $page.props.auth.user?.name || 'Usuário' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ getUserRole($page.props.auth.user?.role) }}
                            </p>
                            <p v-if="$page.props.auth.user?.setor" class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $page.props.auth.user.setor }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Ações do Footer -->
                    <div class="flex items-center justify-between gap-2">
                        <ThemeToggle variant="dropdown" />
                        <button 
                            @click="handleLogout"
                            :disabled="loggingOut"
                            class="flex items-center justify-center px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="loggingOut" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden sm:inline">{{ loggingOut ? 'Saindo...' : 'Sair' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Overlay para mobile -->
        <div v-if="sidebarOpen" 
             @click="toggleSidebar" 
             class="fixed inset-0 z-30 bg-black bg-opacity-50 md:hidden"></div>

        <!-- Layout Principal -->
        <div class="md:ml-64 min-h-screen flex flex-col">
            <!-- Header Mobile/Desktop -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20 h-16">
                <div class="px-4 h-full flex items-center justify-between">
                    <div class="flex items-center min-w-0 flex-1">
                        <button @click="toggleSidebar" class="p-2 -ml-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 md:hidden mr-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100 truncate">
                                {{ pageTitle }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block truncate">
                                {{ pageSubtitle }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-2 ml-4">
                        <!-- User Avatar e Info - Mobile -->
                        <div class="flex items-center md:hidden mr-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-medium">
                                    {{ getInitials($page.props.auth.user?.name) }}
                                </span>
                            </div>
                            <div class="ml-2 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate max-w-20">
                                    {{ ($page.props.auth.user?.name || 'Usuário').split(' ')[0] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ getUserRole($page.props.auth.user?.role) }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Theme Toggle -->
                        <ThemeToggle />
                        
                        <!-- Logout Button -->
                        <button 
                            @click="handleLogout"
                            :disabled="loggingOut"
                            class="p-2 rounded-lg text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            title="Logout"
                        >
                            <svg v-if="loggingOut" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Conteúdo Principal -->
            <main class="flex-1 p-4 pb-20 md:pb-6">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>

        <!-- Bottom Navigation Mobile -->
        <nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 md:hidden z-50 h-16">
            <div class="flex justify-around h-full">
                <Link href="/dashboard" 
                      class="flex flex-col items-center justify-center py-2 px-3 rounded-lg transition-colors min-w-0 flex-1"
                      :class="isActive('/dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    </svg>
                    <span class="text-xs mt-1 truncate">Dashboard</span>
                </Link>
                
                <Link href="/relatorios" 
                      class="flex flex-col items-center justify-center py-2 px-3 rounded-lg transition-colors min-w-0 flex-1"
                      :class="isActive('/relatorios') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-xs mt-1 truncate">Relatórios</span>
                </Link>
                
                <Link href="/relatorios/create" 
                      class="flex flex-col items-center justify-center py-2 px-3 rounded-lg transition-colors min-w-0 flex-1"
                      :class="isActive('/relatorios/create') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400'">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-xs mt-1 truncate">Novo</span>
                </Link>
            </div>
        </nav>
        
        <!-- Sistema de Notificações -->
        <NotificationToast />
        
        <!-- Modal de Confirmação -->
        <ConfirmDialog />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import NotificationToast from '@/Components/NotificationToast.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import { useNotifications } from '@/composables/useNotifications'

const props = defineProps({
    title: {
        type: String,
        default: 'Sistema de Relatórios'
    },
    subtitle: {
        type: String,
        default: ''
    }
})

const sidebarOpen = ref(false)
const loggingOut = ref(false)
const page = usePage()
const { success, error } = useNotifications()

const pageTitle = computed(() => {
    return props.title || page.props.title || 'Sistema de Relatórios'
})

const pageSubtitle = computed(() => {
    if (props.subtitle) return props.subtitle
    
    const url = page.url
    if (url === '/dashboard') return 'Visão geral do sistema'
    if (url.startsWith('/relatorios/create')) return 'Criar novo relatório'
    if (url.startsWith('/relatorios') && url.includes('/edit')) return 'Editar relatório'
    if (url.startsWith('/relatorios') && url.match(/\/\d+$/)) return 'Visualizar relatório'
    if (url.startsWith('/relatorios')) return 'Gerenciar relatórios'
    
    return ''
})

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

const isActive = (route) => {
    if (route === '/dashboard') {
        return page.url === '/dashboard' || page.url === '/'
    }
    return page.url.startsWith(route)
}

const getInitials = (name) => {
    if (!name) return ''
    const names = name.split(' ')
    return names.map(n => n.charAt(0)).join('')
}

const getUserRole = (role) => {
    if (!role) return 'Usuário'
    return role.charAt(0).toUpperCase() + role.slice(1)
}

// Função de logout com animação
const handleLogout = async () => {
    if (loggingOut.value) return
    
    try {
        loggingOut.value = true
        
        // Fechar sidebar se estiver aberta
        sidebarOpen.value = false
        
        // Mostrar notificação de despedida
        success('Até logo! Redirecionando para a página de login...')
        
        // Animação de fade out na aplicação
        document.body.style.transition = 'opacity 0.3s ease-out'
        document.body.style.opacity = '0.7'
        
        // Aguardar um pouco para a animação
        await new Promise(resolve => setTimeout(resolve, 500))
        
        // Fazer o logout via Inertia
        router.post('/logout', {}, {
            onSuccess: () => {
                // Animação adicional antes do redirect
                document.body.style.opacity = '0.3'
                setTimeout(() => {
                    // Reset do estilo depois da navegação
                    document.body.style.transition = ''
                    document.body.style.opacity = ''
                }, 300)
            },
            onError: (errors) => {
                // Reset em caso de erro
                document.body.style.transition = ''
                document.body.style.opacity = ''
                loggingOut.value = false
                error('Erro ao fazer logout. Tente novamente.')
                console.error('Logout error:', errors)
            }
        })
        
    } catch (err) {
        // Reset em caso de exceção
        document.body.style.transition = ''
        document.body.style.opacity = ''
        loggingOut.value = false
        error('Erro inesperado durante o logout')
        console.error('Logout exception:', err)
    }
}
</script> 