<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gerenciar Locais
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Cabeçalho da página -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Locais
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Gerencie os locais da empresa
                        </p>
                    </div>
                    <Link
                        :href="route('locais.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Local
                    </Link>
                </div>

                <!-- Filtros -->
                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <form @submit.prevent="aplicarFiltros" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <input
                                v-model="filtros.busca"
                                type="text"
                                placeholder="Buscar locais..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            />
                        </div>
                        <div>
                            <select
                                v-model="filtros.setor"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            >
                                <option value="">Todos os setores</option>
                                <option v-for="setor in setores" :key="setor" :value="setor">
                                    {{ setor }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <select
                                v-model="filtros.ativo"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                            >
                                <option value="">Todos os status</option>
                                <option value="true">Ativos</option>
                                <option value="false">Inativos</option>
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

                <!-- Grid de Locais -->
                <div v-if="locais.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="local in locais.data"
                        :key="local.id"
                        class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ local.nome }}
                                </h3>
                                <p v-if="local.setor" class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ local.setor }}
                                </p>
                            </div>
                            <span
                                :class="local.ativo ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                class="px-2 py-1 text-xs rounded-full"
                            >
                                {{ local.ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        <div v-if="local.descricao" class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                {{ local.descricao }}
                            </p>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div v-if="local.responsavel" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ local.responsavel }}
                            </div>
                            <div v-if="local.telefone" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ local.telefone }}
                            </div>
                            <div v-if="local.equipamentos_count" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                {{ local.equipamentos_count }} equipamento(s)
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Link
                                :href="route('locais.show', local.id)"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                title="Visualizar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </Link>
                            <Link
                                :href="route('locais.edit', local.id)"
                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300"
                                title="Editar"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </Link>
                            <button
                                @click="confirmarExclusao(local)"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum local encontrado</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ Object.values(filtros).some(f => f) ? 'Tente ajustar os filtros ou' : 'Comece' }} criando um novo local.
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="route('locais.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Novo Local
                        </Link>
                    </div>
                </div>

                <!-- Paginação -->
                <div v-if="locais.data.length > 0" class="mt-6">
                    <Pagination :links="locais" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useNotifications } from '@/composables/useNotifications'
import { useConfirm } from '@/composables/useConfirm'

const { success, error } = useNotifications()
const { confirmDelete } = useConfirm()

const props = defineProps({
    locais: Object,
    filtros: Object,
    setores: Array,
})

const filtros = reactive({
    busca: props.filtros.busca || '',
    setor: props.filtros.setor || '',
    ativo: props.filtros.ativo || '',
})

const aplicarFiltros = () => {
    router.get(route('locais.index'), filtros, {
        preserveState: true,
        preserveScroll: true,
    })
}

const limparFiltros = () => {
    filtros.busca = ''
    filtros.setor = ''
    filtros.ativo = ''
    aplicarFiltros()
}

const confirmarExclusao = async (local) => {
    const confirmado = await confirmDelete(
        `Tem certeza que deseja excluir o local "${local.nome}"?`,
        'Esta ação não pode ser desfeita.'
    )
    
    if (confirmado) {
        router.delete(route('locais.destroy', local.id), {
            onSuccess: () => {
                success(`Local "${local.nome}" excluído com sucesso!`)
            },
            onError: () => {
                error('Erro ao excluir local. Verifique se não há equipamentos ou relatórios associados.')
            }
        })
    }
}
</script> 