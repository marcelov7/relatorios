<template>
    <AppLayout title="Relatórios">
        <!-- Pull to Refresh Indicator -->
        <div v-if="isRefreshing" class="fixed top-16 left-1/2 transform -translate-x-1/2 z-50 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Atualizando...
            </div>
        </div>
        <!-- Botão de PDF Técnico -->
        <div class="mb-4 flex items-center gap-4">
            <button
                :disabled="selectedIds.length === 0 || selectedIds.length > 20"
                @click="gerarPdf"
                class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-800 focus:bg-green-700 dark:focus:bg-green-800 active:bg-green-900 dark:active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Gerar PDF Técnico
            </button>
            <span v-if="selectedIds.length > 20" class="text-red-500 text-sm">
                Selecione no máximo 20 relatórios.
            </span>
        </div>
        <!-- Cabeçalho com filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/20 p-4 mb-6 border border-gray-200 dark:border-gray-700">
            <!-- Busca e Novo Relatório -->
            <div class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="flex-1">
                    <div class="relative">
                        <input 
                            v-model="form.busca" 
                            @input="buscar"
                            type="text" 
                            placeholder="Buscar relatórios..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:placeholder-gray-400 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600"
                        >
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <Link href="/relatorios/create" 
                      class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-800 focus:bg-blue-700 dark:focus:bg-blue-800 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Relatório
                </Link>
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select v-model="form.status" @change="aplicarFiltros" 
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600">
                        <option value="">Todos os Status</option>
                        <option value="Aberta">Aberta</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Concluída">Concluída</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Setor</label>
                    <select v-model="form.setor_id" @change="aplicarFiltros" 
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600">
                        <option value="">Todos os Setores</option>
                        <option v-for="setor in setores" :key="setor.id" :value="setor.id">
                            {{ setor.nome }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Autor</label>
                    <select v-model="form.autor_id" @change="aplicarFiltros" 
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600">
                        <option value="">Todos os Autores</option>
                        <option v-for="autor in autores" :key="autor.id" :value="autor.id">
                            {{ autor.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Início</label>
                    <input v-model="form.data_inicio" @change="aplicarFiltros" 
                           type="date" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data Fim</label>
                    <input v-model="form.data_fim" @change="aplicarFiltros" 
                           type="date" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span v-if="temFiltros" class="inline-flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filtros ativos</span>
                    </span>
                </div>
                <button @click="limparFiltros" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Limpar Filtros
                </button>
            </div>
        </div>

        <!-- Contador de resultados -->
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Exibindo {{ relatorios.data.length }} de {{ relatorios.total }} relatórios
        </div>

        <!-- Grid de Relatórios -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <SwipeableCard 
                v-for="relatorio in relatorios.data" 
                :key="relatorio.id"
                @click="router.visit(`/relatorios/${relatorio.id}`)"
                @edit="relatorio.podeEditar ? router.visit(`/relatorios/${relatorio.id}/edit`) : null"
                @delete="relatorio.podeExcluir ? confirmarExclusao(relatorio) : null"
                :can-edit="relatorio.podeEditar"
                :can-delete="relatorio.podeExcluir"
            >
                <div class="p-6">
                    <div class="flex items-center mb-2">
                        <input type="checkbox" :value="relatorio.id" v-model="selectedIds" class="mr-2 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" @click.stop />
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1 flex-1">
                            {{ relatorio.titulo }}
                        </h3>
                    </div>
                    <!-- Header do Card -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ relatorio.autor?.name || 'Autor não identificado' }}
                            </p>
                            <div class="flex flex-col gap-1 mt-1">
                                <div v-if="relatorio.equipamentosTesteArr && relatorio.equipamentosTesteArr.length > 0">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Equipamentos:</span>
                                    <ul class="ml-2 mt-1 space-y-0.5">
                                        <li v-for="equip in relatorio.equipamentosTesteArr" :key="equip.id" class="flex flex-wrap items-center gap-x-1 gap-y-0.5 min-w-0">
                                            <span class="font-semibold text-blue-700 dark:text-blue-300 text-[10px] truncate max-w-[80px]">{{ equip.tag }}</span>
                                            <span class="text-[10px] text-gray-700 dark:text-gray-200 truncate max-w-[120px]">{{ equip.nome }}</span>
                                            <span class="text-[10px] px-1 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 truncate max-w-[60px]">{{ equip.setor || 'Sem setor' }}</span>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap"
                                                  :class="equip.status === 'Operacional' ? 'bg-green-100 border-green-400 text-green-800 dark:bg-green-900/40 dark:text-green-300 dark:border-green-600' : 'bg-yellow-100 border-yellow-400 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-600'">
                                                {{ equip.status }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                                <div v-else class="text-xs text-gray-400 italic">Nenhum equipamento de teste vinculado</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2"
                              :class="getStatusClass(relatorio.status)">
                            {{ relatorio.status }}
                        </span>
                    </div>

                    <!-- Progresso -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500 dark:text-gray-400">Progresso:</span>
                            <span class="text-gray-900 dark:text-gray-100 font-medium">{{ relatorio.progresso }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300"
                                 :class="getProgressClass(relatorio.progresso)"
                                 :style="`width: ${relatorio.progresso}%`">
                            </div>
                        </div>
                    </div>

                    <!-- Informações Adicionais -->
                    <div class="space-y-2 mb-4">
                        <div v-if="relatorio.local?.nome" class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Local:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ relatorio.local.nome }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Criado em:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ formatarData(relatorio.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                        <Link :href="`/relatorios/${relatorio.id}`" 
                              class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 font-medium text-sm">
                            Ver Detalhes
                        </Link>
                        <div class="flex space-x-2">
                            <!-- Botão Editar -->
                            <Link v-if="relatorio.podeEditar" 
                                  :href="`/relatorios/${relatorio.id}/edit`" 
                                  class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300"
                                  title="Editar relatório">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </Link>
                            <!-- Botão Excluir -->
                            <button v-if="relatorio.podeExcluir"
                                    @click="confirmarExclusao(relatorio)" 
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                    title="Excluir relatório">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            <!-- Ícone de bloqueio para indicar permissão restrita -->
                            <div v-if="!relatorio.podeEditar && !relatorio.podeExcluir"
                                 class="text-gray-400 dark:text-gray-500"
                                 title="Sem permissão para editar ou excluir">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <!-- Indicador de tempo restante para exclusão -->
                            <div v-if="relatorio.ehAutor && !$page.props.auth.user.role === 'admin' && relatorio.tempoRestanteExclusao !== null && relatorio.tempoRestanteExclusao > 0"
                                 class="text-orange-500 dark:text-orange-400"
                                 :title="`${relatorio.tempoRestanteExclusao}h restantes para exclusão`">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <!-- Indicadores de fotos e histórico abaixo dos botões -->
                    <div class="flex items-center justify-center gap-6 mt-2">
                        <!-- Ícone de fotos (Heroicons Camera) -->
                        <span v-if="relatorio.totalFotos > 0" class="flex items-center text-gray-600 dark:text-gray-300" :title="`Este relatório possui ${relatorio.totalFotos} foto${relatorio.totalFotos > 1 ? 's' : ''}`">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75V7.5A2.25 2.25 0 014.5 5.25h2.086a2.25 2.25 0 001.591-.659l.828-.828A2.25 2.25 0 0110.5 3.75h3a2.25 2.25 0 011.495.563l.828.828a2.25 2.25 0 001.591.659H19.5a2.25 2.25 0 012.25 2.25v11.25a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25z" />
                                <circle cx="12" cy="13" r="3.25" stroke="currentColor" stroke-width="1.5" fill="none" />
                            </svg>
                            <span class="text-xs">{{ relatorio.totalFotos }}</span>
                        </span>
                        <!-- Ícone de histórico de atualização (Heroicons Clock) -->
                        <span v-if="relatorio.totalHistoricos > 0" class="flex items-center text-blue-600 dark:text-blue-400" title="Este relatório possui atualizações">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                </div>
            </SwipeableCard>
        </div>

        <!-- Estado Vazio -->
        <div v-if="relatorios.data.length === 0" 
             class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/20 border border-gray-200 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum relatório encontrado</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ temFiltros ? 'Tente ajustar os filtros de busca.' : 'Comece criando um novo relatório.' }}
            </p>
            <div class="mt-6" v-if="!temFiltros">
                <Link href="/relatorios/create" 
                      class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-800 active:bg-blue-900 dark:active:bg-blue-900 focus:outline-none focus:border-blue-900 dark:focus:border-blue-800 focus:ring ring-blue-300 dark:focus:ring-blue-600 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Criar Primeiro Relatório
                </Link>
            </div>
        </div>

        <!-- Paginação -->
        <div v-if="relatorios.data.length > 0" class="mt-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Seletor de itens por página -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-700 dark:text-gray-300">Itens por página:</label>
                    <select v-model="form.per_page" @change="aplicarFiltros" 
                            class="border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-600 text-sm">
                        <option v-for="option in perPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                </div>
                
                <!-- Componente de paginação -->
                <Pagination :links="relatorios" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import SwipeableCard from '@/Components/SwipeableCard.vue'
import { useConfirm } from '@/composables/useConfirm'
import { useNotifications } from '@/composables/useNotifications'

const props = defineProps({
    relatorios: Object,
    filtros: Object,
    locais: Array,
    setores: Array,
    autores: Array,
    perPageOptions: Array,
})

const { confirmDelete } = useConfirm()
const { success, error } = useNotifications()

const form = ref({
    busca: props.filtros.busca || '',
    status: props.filtros.status || '',
    setor_id: props.filtros.setor_id || '',
    autor_id: props.filtros.autor_id || '',
    data_inicio: props.filtros.data_inicio || '',
    data_fim: props.filtros.data_fim || '',
    per_page: props.filtros.per_page || 12,
})

const isRefreshing = ref(false)
let timeoutBusca = null
let startY = 0
let currentY = 0
let isPulling = false

const temFiltros = computed(() => {
    return form.value.busca || form.value.status || form.value.setor_id || form.value.autor_id || form.value.data_inicio || form.value.data_fim
})

const buscar = () => {
    clearTimeout(timeoutBusca)
    timeoutBusca = setTimeout(() => {
        aplicarFiltros()
    }, 500)
}

const aplicarFiltros = () => {
    router.get('/relatorios', form.value, {
        preserveState: true,
        replace: true,
    })
}

const limparFiltros = () => {
    form.value = {
        busca: '',
        status: '',
        setor_id: '',
        autor_id: '',
        data_inicio: '',
        data_fim: '',
        per_page: 12,
    }
    aplicarFiltros()
}

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

const formatarData = (data) => {
    return new Date(data).toLocaleDateString('pt-BR')
}

const confirmarExclusao = async (relatorio) => {
    const confirmado = await confirmDelete(`"${relatorio.titulo}"`)
    
    if (confirmado) {
        router.delete(`/relatorios/${relatorio.id}`, {
            onSuccess: () => {
                success('Relatório excluído com sucesso!', {
                    title: 'Exclusão Confirmada',
                    message: `O relatório "${relatorio.titulo}" foi removido do sistema.`
                })
            },
            onError: () => {
                error('Erro ao excluir relatório', {
                    title: 'Falha na Exclusão',
                    message: 'Não foi possível remover o relatório. Tente novamente.'
                })
            }
        })
    }
}

const selectedIds = ref([])

const gerarPdf = () => {
    if (selectedIds.value.length === 0 || selectedIds.value.length > 20) return
    const form = document.createElement('form')
    form.method = 'POST'
    form.action = '/relatorios/pdf-lote'
    form.target = '_blank'
    form.style.display = 'none'
    // CSRF token
    const csrf = document.querySelector('meta[name=csrf-token]').content
    const csrfInput = document.createElement('input')
    csrfInput.type = 'hidden'
    csrfInput.name = '_token'
    csrfInput.value = csrf
    form.appendChild(csrfInput)
    // IDs
    selectedIds.value.forEach(id => {
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = 'ids[]'
        input.value = id
        form.appendChild(input)
    })
    document.body.appendChild(form)
    form.submit()
    document.body.removeChild(form)
}

// Pull to Refresh
const handleTouchStart = (e) => {
    if (window.scrollY === 0) {
        startY = e.touches[0].clientY
        isPulling = true
    }
}

const handleTouchMove = (e) => {
    if (!isPulling) return
    
    currentY = e.touches[0].clientY
    const pullDistance = currentY - startY
    
    if (pullDistance > 80 && !isRefreshing.value) {
        e.preventDefault()
    }
}

const handleTouchEnd = () => {
    if (!isPulling) return
    
    const pullDistance = currentY - startY
    
    if (pullDistance > 80 && !isRefreshing.value) {
        isRefreshing.value = true
        router.reload({
            onFinish: () => {
                setTimeout(() => {
                    isRefreshing.value = false
                }, 1000)
            }
        })
    }
    
    isPulling = false
    startY = 0
    currentY = 0
}

// Mount event listeners
onMounted(() => {
    document.addEventListener('touchstart', handleTouchStart, { passive: false })
    document.addEventListener('touchmove', handleTouchMove, { passive: false })
    document.addEventListener('touchend', handleTouchEnd)
})

onUnmounted(() => {
    document.removeEventListener('touchstart', handleTouchStart)
    document.removeEventListener('touchmove', handleTouchMove)
    document.removeEventListener('touchend', handleTouchEnd)
})
</script> 