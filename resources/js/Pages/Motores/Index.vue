<template>
    <Head title="Motores" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Motores') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Cabeçalho -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Gerenciamento de Motores
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Cadastre e gerencie os motores da empresa
                                </p>
                            </div>
                            <Link :href="route('motores.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Novo Motor
                            </Link>
                        </div>

                        <!-- Mensagem de sucesso -->
                        <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                        </div>

                        <!-- Filtros -->
                        <div class="mb-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <form @submit.prevent="applyFilters" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- Busca -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Buscar
                                        </label>
                                        <input 
                                            v-model="filters.busca" 
                                            type="text" 
                                            placeholder="TAG, equipamento, fabricante..."
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        >
                                    </div>

                                    <!-- Local -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Local
                                        </label>
                                        <select 
                                            v-model="filters.local_id" 
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        >
                                            <option value="">Todos os locais</option>
                                            <option v-for="local in locais" :key="local.id" :value="local.id">
                                                {{ local.nome }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Armazenamento -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Armazenamento
                                        </label>
                                        <select 
                                            v-model="filters.armazenamento" 
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        >
                                            <option value="">Todos</option>
                                            <option value="Instalado">Instalado</option>
                                            <option value="Almoxarifado">Almoxarifado</option>
                                            <option value="Manutenção">Manutenção</option>
                                            <option value="Descartado">Descartado</option>
                                        </select>
                                    </div>

                                    <!-- Reserva Almox -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Reserva Almox
                                        </label>
                                        <select 
                                            v-model="filters.reserva_almox" 
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        >
                                            <option value="">Todos</option>
                                            <option value="true">Sim</option>
                                            <option value="false">Não</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Filtros avançados -->
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <button 
                                        type="button" 
                                        @click="showAdvancedFilters = !showAdvancedFilters"
                                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300"
                                    >
                                        {{ showAdvancedFilters ? 'Ocultar' : 'Mostrar' }} filtros avançados
                                    </button>
                                    
                                    <div v-if="showAdvancedFilters" class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <!-- Fabricante -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Fabricante
                                            </label>
                                            <select 
                                                v-model="filters.fabricante" 
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            >
                                                <option value="">Todos</option>
                                                <option v-for="fabricante in fabricantes" :key="fabricante" :value="fabricante">
                                                    {{ fabricante }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Tipo de Equipamento -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Tipo de Equipamento
                                            </label>
                                            <select 
                                                v-model="filters.tipo_equipamento" 
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            >
                                                <option value="">Todos</option>
                                                <option v-for="tipo in tiposEquipamento" :key="tipo" :value="tipo">
                                                    {{ tipo }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Status Ativo -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Status
                                            </label>
                                            <select 
                                                v-model="filters.ativo" 
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            >
                                                <option value="">Todos</option>
                                                <option value="true">Ativo</option>
                                                <option value="false">Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões de filtro -->
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ motores.total }} motor(es) encontrado(s)
                                    </div>
                                    <div class="flex space-x-2">
                                        <button 
                                            type="button" 
                                            @click="clearFilters"
                                            class="inline-flex items-center px-3 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:bg-gray-400 dark:focus:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                        >
                                            Limpar
                                        </button>
                                        <button 
                                            type="submit"
                                            class="inline-flex items-center px-3 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:bg-indigo-700 dark:focus:bg-indigo-600 active:bg-indigo-900 dark:active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                        >
                                            Filtrar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Lista de motores -->
                        <div v-if="motores.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div 
                                v-for="motor in motores.data" 
                                :key="motor.id"
                                class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200"
                            >
                                <!-- Cabeçalho do card -->
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ motor.tag }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ motor.equipamento }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col space-y-1">
                                        <span :class="motor.armazenamento_class" class="px-2 py-1 text-xs rounded-full">
                                            {{ motor.armazenamento }}
                                        </span>
                                        <span v-if="motor.reserva_almox" :class="motor.reserva_almox_class" class="px-2 py-1 text-xs rounded-full">
                                            Reserva
                                        </span>
                                    </div>
                                </div>

                                <!-- Informações do motor -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Fabricante:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.fabricante || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Potência:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.potencia_formatada }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Rotação:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.rotacao ? `${motor.rotacao} RPM` : 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Local:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.local?.nome || 'N/A' }}</span>
                                    </div>
                                </div>

                                <!-- Foto do motor -->
                                <div v-if="motor.foto" class="mb-4">
                                    <img 
                                        :src="motor.foto_url" 
                                        :alt="motor.equipamento"
                                        class="w-full h-32 object-cover rounded-md"
                                    >
                                </div>

                                <!-- Ações -->
                                <div class="flex justify-end space-x-2">
                                    <Link 
                                        :href="route('motores.show', motor.id)"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                        title="Visualizar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                    <Link 
                                        :href="route('motores.edit', motor.id)"
                                        class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300"
                                        title="Editar"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                    <button 
                                        @click="deleteMotor(motor)"
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                        title="Excluir"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Estado vazio -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum motor encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ Object.keys(filters).some(key => filters[key] !== '' && filters[key] !== null) 
                                    ? 'Tente ajustar os filtros de busca.' 
                                    : 'Comece criando um novo motor.' }}
                            </p>
                            <div class="mt-6">
                                <Link 
                                    :href="route('motores.create')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Novo Motor
                                </Link>
                            </div>
                        </div>

                        <!-- Paginação -->
                        <div v-if="motores.data.length > 0" class="mt-6">
                            <Pagination :links="motores.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useConfirm } from '@/composables/useConfirm'

const props = defineProps({
    motores: Object,
    filtros: Object,
    fabricantes: Array,
    tiposEquipamento: Array,
    locais: Array,
})

const { confirm } = useConfirm()

const showAdvancedFilters = ref(false)

const filters = reactive({
    busca: props.filtros.busca || '',
    local_id: props.filtros.local_id || '',
    armazenamento: props.filtros.armazenamento || '',
    reserva_almox: props.filtros.reserva_almox || '',
    fabricante: props.filtros.fabricante || '',
    tipo_equipamento: props.filtros.tipo_equipamento || '',
    ativo: props.filtros.ativo || '',
})

const applyFilters = () => {
    router.get(route('motores.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    Object.keys(filters).forEach(key => {
        filters[key] = ''
    })
    applyFilters()
}

const deleteMotor = async (motor) => {
    const confirmed = await confirm(
        'Excluir Motor',
        `Tem certeza que deseja excluir o motor "${motor.tag}"? Esta ação não pode ser desfeita.`
    )

    if (confirmed) {
        router.delete(route('motores.destroy', motor.id))
    }
}

// Debounce para busca
let searchTimeout
watch(() => filters.busca, (newValue) => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 500)
})
</script> 