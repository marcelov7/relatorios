<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gerenciar Usuários
            </h2>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <!-- Cabeçalho com botão de novo usuário -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Usuários do Sistema
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Gerencie todos os usuários do sistema
                                </p>
                            </div>
                            <Link :href="route('users.create')" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Novo Usuário
                            </Link>
                        </div>

                        <!-- Mensagens de feedback -->
                        <div v-if="$page.props.flash?.success" 
                            class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                        </div>

                        <div v-if="$page.props.flash?.error" 
                            class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.error }}</span>
                        </div>

                        <!-- Filtros -->
                        <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <form @submit.prevent="filtrar" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Busca -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Buscar
                                    </label>
                                    <input 
                                        v-model="form.busca"
                                        type="text" 
                                        placeholder="Nome, email, setor, cargo..." 
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                </div>

                                <!-- Setor -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Setor
                                    </label>
                                    <select 
                                        v-model="form.setor"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                        <option value="">Todos</option>
                                        <option v-for="setor in setores" :key="setor" :value="setor">
                                            {{ setor }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Role -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tipo
                                    </label>
                                    <select 
                                        v-model="form.role"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                        <option value="">Todos</option>
                                        <option value="user">Usuário</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Status
                                    </label>
                                    <select 
                                        v-model="form.ativo"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                        <option value="">Todos</option>
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </form>

                            <!-- Botões de ação -->
                            <div class="mt-4 flex flex-col sm:flex-row gap-2">
                                <button 
                                    @click="filtrar"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    Filtrar
                                </button>
                                <button 
                                    @click="limparFiltros"
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Limpar
                                </button>
                            </div>
                        </div>

                        <!-- Lista de usuários -->
                        <div v-if="users.data.length > 0">
                            <!-- Contador de resultados -->
                            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                Mostrando {{ users.from }} a {{ users.to }} de {{ users.total }} usuários
                            </div>

                            <!-- Grid de cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                <div v-for="user in users.data" :key="user.id" 
                                    class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-200">
                                    
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                {{ user.name }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                {{ user.email }}
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <span :class="getRoleClass(user.role)" 
                                                class="px-2 py-1 text-xs rounded-full font-medium">
                                                {{ user.role === 'admin' ? 'Admin' : 'Usuário' }}
                                            </span>
                                            <span :class="getStatusClass(user.ativo)" 
                                                class="px-2 py-1 text-xs rounded-full font-medium">
                                                {{ user.ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-2 mb-4">
                                        <div v-if="user.setor" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ user.setor }}
                                        </div>
                                        <div v-if="user.cargo" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6"/>
                                            </svg>
                                            {{ user.cargo }}
                                        </div>
                                        <div v-if="user.telefone" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            {{ user.telefone }}
                                        </div>
                                    </div>

                                    <!-- Botões de ação -->
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="route('users.show', user.id)" 
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-200"
                                            title="Visualizar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </Link>
                                        <Link :href="route('users.edit', user.id)" 
                                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 transition-colors duration-200"
                                            title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </Link>
                                        <button @click="confirmarExclusao(user)" 
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-200"
                                            title="Excluir">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Paginação -->
                            <div class="mt-6">
                                <Pagination :links="users" />
                            </div>
                        </div>

                        <!-- Estado vazio -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum usuário encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ Object.keys(filters).some(key => filters[key]) ? 'Tente ajustar os filtros.' : 'Comece criando um novo usuário.' }}
                            </p>
                            <div class="mt-6">
                                <Link :href="route('users.create')" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Criar Usuário
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmação de exclusão -->
        <ConfirmDialog 
            :show="showDeleteDialog"
            title="Confirmar Exclusão"
            :message="`Tem certeza que deseja excluir o usuário ${userToDelete?.name}? Esta ação não pode ser desfeita.`"
            confirmText="Excluir"
            cancelText="Cancelar"
            type="danger"
            @confirm="excluirUsuario"
            @cancel="cancelarExclusao"
        />
    </AppLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'

// Props
const props = defineProps({
    users: Object,
    setores: Array,
    filters: Object
})

// Estado reativo
const form = reactive({
    busca: props.filters.busca || '',
    setor: props.filters.setor || '',
    role: props.filters.role || '',
    ativo: props.filters.ativo || ''
})

const showDeleteDialog = ref(false)
const userToDelete = ref(null)

// Watcher para aplicar filtros automaticamente nos selects
watch([() => form.setor, () => form.role, () => form.ativo], () => {
    filtrar()
}, { deep: true })

// Métodos
const filtrar = () => {
    router.get(route('users.index'), form, {
        preserveState: true,
        preserveScroll: true
    })
}

const limparFiltros = () => {
    Object.keys(form).forEach(key => {
        form[key] = ''
    })
    filtrar()
}

const getRoleClass = (role) => {
    return role === 'admin' 
        ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
        : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
}

const getStatusClass = (ativo) => {
    return ativo 
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
        : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
}

const confirmarExclusao = (user) => {
    userToDelete.value = user
    showDeleteDialog.value = true
}

const excluirUsuario = () => {
    if (userToDelete.value) {
        router.delete(route('users.destroy', userToDelete.value.id), {
            preserveScroll: true,
            onFinish: () => {
                showDeleteDialog.value = false
                userToDelete.value = null
            }
        })
    }
}

const cancelarExclusao = () => {
    showDeleteDialog.value = false
    userToDelete.value = null
}
</script> 