<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    stats: Object,
    relatoriosRecentes: Array,
})

const getStatusClass = (status) => {
    switch (status) {
        case 'Concluída':
            return 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400'
        case 'Em Andamento':
            return 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400'
        case 'Aberta':
            return 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-400'
        case 'Cancelada':
            return 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400'
        default:
            return 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
    }
}

const getProgressClass = (progresso) => {
    if (progresso >= 100) return 'bg-green-500'
    if (progresso >= 75) return 'bg-blue-500'
    if (progresso >= 50) return 'bg-yellow-500'
    if (progresso >= 25) return 'bg-orange-500'
    return 'bg-red-500'
}
</script>

<template>
    <AppLayout title="Dashboard">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-700 dark:to-blue-900 rounded-lg shadow-lg p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">
                        Bem-vindo ao Sistema de Relatórios! 👋
                    </h1>
                    <p class="text-blue-100 dark:text-blue-200 text-sm md:text-base">
                        Gerencie seus relatórios de forma eficiente e organizada
                    </p>
                </div>
                <div class="hidden md:block">
                    <svg class="w-16 h-16 text-blue-200 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            <!-- Total de Relatórios -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900/20 p-4 md:p-6 hover:shadow-lg dark:hover:shadow-gray-900/30 transition-shadow duration-200 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.totalRelatorios }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Relatórios cadastrados</p>
            </div>

            <!-- Este Mês -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900/20 p-4 md:p-6 hover:shadow-lg dark:hover:shadow-gray-900/30 transition-shadow duration-200 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Este Mês</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.esteMes }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Criados recentemente</p>
            </div>

            <!-- Em Andamento -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900/20 p-4 md:p-6 hover:shadow-lg dark:hover:shadow-gray-900/30 transition-shadow duration-200 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Em Andamento</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.emAndamento }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Sendo trabalhados</p>
            </div>

            <!-- Concluídos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md dark:shadow-gray-900/20 p-4 md:p-6 hover:shadow-lg dark:hover:shadow-gray-900/30 transition-shadow duration-200 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Concluídos</p>
                        <p class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ stats.concluidos }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Finalizados</p>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Criar Novo Relatório -->
            <Link href="/relatorios/create" 
                  class="group bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 hover:from-blue-600 hover:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 rounded-lg shadow-lg dark:shadow-gray-900/30 p-6 text-white transition-all duration-200 hover:shadow-xl dark:hover:shadow-gray-900/40 transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Novo Relatório</h3>
                        <p class="text-blue-100 dark:text-blue-200 text-sm">Criar um novo relatório</p>
                    </div>
                    <svg class="w-8 h-8 text-blue-200 dark:text-blue-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
            </Link>

            <!-- Ver Todos os Relatórios -->
            <Link href="/relatorios" 
                  class="group bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 hover:from-green-600 hover:to-green-700 dark:hover:from-green-700 dark:hover:to-green-800 rounded-lg shadow-lg dark:shadow-gray-900/30 p-6 text-white transition-all duration-200 hover:shadow-xl dark:hover:shadow-gray-900/40 transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Ver Relatórios</h3>
                        <p class="text-green-100 dark:text-green-200 text-sm">Gerenciar todos os relatórios</p>
                    </div>
                    <svg class="w-8 h-8 text-green-200 dark:text-green-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </Link>

            <!-- Estatísticas -->
            <div class="group bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 hover:from-purple-600 hover:to-purple-700 dark:hover:from-purple-700 dark:hover:to-purple-800 rounded-lg shadow-lg dark:shadow-gray-900/30 p-6 text-white transition-all duration-200 hover:shadow-xl dark:hover:shadow-gray-900/40 transform hover:scale-105 cursor-pointer">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Relatórios Abertos</h3>
                        <p class="text-purple-100 dark:text-purple-200 text-sm">{{ stats.abertos }} aguardando atenção</p>
                    </div>
                    <svg class="w-8 h-8 text-purple-200 dark:text-purple-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/20 p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Relatórios Recentes</h2>
                <Link href="/relatorios" 
                      class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                    Ver todos
                </Link>
            </div>

            <div v-if="relatoriosRecentes.length > 0" class="space-y-4">
                <div v-for="relatorio in relatoriosRecentes" 
                     :key="relatorio.id"
                     class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-600">
                    <div class="flex-1">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-1">
                            {{ relatorio.titulo }}
                        </h3>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex flex-wrap items-center space-x-2 sm:space-x-4">
                                <span>{{ relatorio.autor }}</span>
                                <span>•</span>
                                <span>{{ relatorio.setor }}</span>
                                <span>•</span>
                                <span>{{ relatorio.tag }}</span>
                            </div>
                            <div class="flex items-center space-x-2 mt-1 sm:mt-0">
                                <span class="hidden sm:inline">•</span>
                                <span>{{ relatorio.data }}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  :class="getStatusClass(relatorio.status)">
                                {{ relatorio.status }}
                            </span>
                            <div class="flex items-center space-x-2">
                                <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300"
                                         :class="getProgressClass(relatorio.progresso)"
                                         :style="`width: ${relatorio.progresso}%`">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ relatorio.progresso }}%</span>
                            </div>
                        </div>
                    </div>
                    <Link :href="`/relatorios/${relatorio.id}`" 
                          class="ml-4 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>
            </div>

            <div v-else class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum relatório</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comece criando seu primeiro relatório.</p>
                <div class="mt-6">
                    <Link href="/relatorios/create" 
                          class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-800 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 dark:focus:ring-blue-600 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Criar Primeiro Relatório
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
