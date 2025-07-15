<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gerenciar Equipamentos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Cabeçalho da página -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Equipamentos
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Gerencie os equipamentos da empresa
                        </p>
                    </div>
                    <Link
                        :href="route('equipamentos.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Equipamento
                    </Link>
                </div>

                <!-- Filtros -->
                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <form @submit.prevent="aplicarFiltros" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <input
                                v-model="filtros.busca"
                                type="text"
                                placeholder="Buscar equipamentos..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            />
                        </div>
                        <div>
                            <select
                                v-model="filtros.local_id"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            >
                                <option value="">Todos os locais</option>
                                <option v-for="local in locais" :key="local.id" :value="local.id">
                                    {{ local.nome }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <select
                                v-model="filtros.status"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            >
                                <option value="">Todos os status</option>
                                <option value="Operacional">Operacional</option>
                                <option value="Manutenção">Manutenção</option>
                                <option value="Inativo">Inativo</option>
                                <option value="Defeito">Defeito</option>
                            </select>
                        </div>
                        <div>
                            <select
                                v-model="filtros.tipo"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            >
                                <option value="">Todos os tipos</option>
                                <option v-for="tipo in tipos" :key="tipo" :value="tipo">
                                    {{ tipo }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <select
                                v-model="filtros.setor_id"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            >
                                <option value="">Todos os setores</option>
                                <option v-for="setor in setores" :key="setor.id" :value="setor.id">
                                    {{ setor.nome }}
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="submit"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Filtrar
                            </button>
                            <button
                                type="button"
                                @click="limparFiltros"
                                class="px-3 py-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Grid de Equipamentos -->
                <div v-if="equipamentos.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="equipamento in equipamentos.data"
                        :key="equipamento.id"
                        class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ equipamento.equipment_tag }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ equipamento.nome }}
                                </p>
                            </div>
                            <span
                                :class="getStatusClass(equipamento.status)"
                                class="px-2 py-1 text-xs rounded-full"
                            >
                                {{ equipamento.status }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div v-if="equipamento.local" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ equipamento.local.nome }}
                            </div>
                            <div v-if="equipamento.tipo" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                {{ equipamento.tipo }}
                            </div>
                            <div v-if="equipamento.marca" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ equipamento.marca }} {{ equipamento.modelo }}
                            </div>
                            <div v-if="equipamento.proxima_manutencao" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Próx. manutenção: {{ formatDate(equipamento.proxima_manutencao) }}
                            </div>
                            <div v-if="equipamento.setor" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                                </svg>
                                {{ equipamento.setor.nome }}
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Link
                                :href="route('equipamentos.show', equipamento.id)"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                title="Visualizar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </Link>
                            <Link
                                :href="route('equipamentos.edit', equipamento.id)"
                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300"
                                title="Editar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </Link>
                            <button
                                @click="confirmarExclusao(equipamento)"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum equipamento encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ Object.values(filtros).some(f => f) ? 'Tente ajustar os filtros ou' : 'Comece' }} criando um novo equipamento.
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="route('equipamentos.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Novo Equipamento
                        </Link>
                    </div>
                </div>

                <!-- Paginação -->
                <div v-if="equipamentos.data.length > 0" class="mt-6">
                    <Pagination :links="equipamentos" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useNotifications } from '@/composables/useNotifications'
import { useConfirm } from '@/composables/useConfirm'

const { success, error } = useNotifications()
const { confirmDelete } = useConfirm()

const props = defineProps({
    equipamentos: Object,
    filtros: Object,
    locais: Array,
    tipos: Array,
})

const setores = ref([])

const filtros = ref({
    busca: props.filtros.busca || '',
    local_id: props.filtros.local_id || '',
    status: props.filtros.status || '',
    tipo: props.filtros.tipo || '',
    setor_id: props.filtros.setor_id || '',
})

onMounted(async () => {
    // Buscar setores ativos para o filtro
    const res = await fetch(route('api.setores.ativos'))
    setores.value = await res.json()
})

function aplicarFiltros() {
    router.get(route('equipamentos.index'), filtros.value, { preserveState: true, replace: true })
}

function limparFiltros() {
    filtros.value = { busca: '', local_id: '', status: '', tipo: '', setor_id: '' }
    aplicarFiltros()
}

function getStatusClass(status) {
    const classes = {
        'Operacional': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        'Manutenção': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        'Inativo': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
        'Defeito': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    }
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

function formatDate(date) {
    if (!date) return ''
    return new Date(date).toLocaleDateString('pt-BR')
}

function confirmarExclusao(equipamento) {
    confirmDelete(
        `Tem certeza que deseja excluir o equipamento "${equipamento.equipment_tag}"?`,
        'Esta ação não pode ser desfeita.'
    )
    .then(confirmado => {
        if (confirmado) {
            router.delete(route('equipamentos.destroy', equipamento.id), {
                onSuccess: () => {
                    success(`Equipamento "${equipamento.equipment_tag}" excluído com sucesso!`)
                },
                onError: () => {
                    error('Erro ao excluir equipamento. Verifique se não há relatórios associados.')
                }
            })
        }
    })
}
</script> 