<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detalhes do Usuário
            </h2>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Cabeçalho da Página -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ user.name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ user.email }}
                            </p>
                        </div>
                        
                        <!-- Botões Desktop -->
                        <div class="hidden sm:flex items-center gap-2">
                            <Link 
                                :href="route('users.edit', user.id)"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </Link>
                            <button 
                                @click="confirmarExclusao"
                                class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Excluir
                            </button>
                            <Link 
                                :href="route('users.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                Voltar
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Botões Mobile -->
                    <div class="sm:hidden mt-4 flex flex-col gap-2">
                        <div class="flex gap-2">
                            <Link 
                                :href="route('users.edit', user.id)"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </Link>
                            <button 
                                @click="confirmarExclusao"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Excluir
                            </button>
                        </div>
                        <Link 
                            :href="route('users.index')"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            Voltar
                        </Link>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <!-- Informações Pessoais -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                            <!-- Nome -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Nome Completo
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ user.name }}
                                </p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Email
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ user.email }}
                                </p>
                                <div class="flex items-center mt-1">
                                    <svg v-if="user.email_verified_at" class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <svg v-else class="w-4 h-4 text-orange-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L4.268 16.5C3.498 18.333 4.46 20 6 20z"/>
                                    </svg>
                                    <span class="text-xs" :class="user.email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400'">
                                        {{ user.email_verified_at ? 'Email verificado' : 'Email não verificado' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Setor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Setor
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ user.setor || 'Não informado' }}
                                </p>
                            </div>

                            <!-- Cargo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Cargo
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ user.cargo || 'Não informado' }}
                                </p>
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Telefone
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ user.telefone || 'Não informado' }}
                                </p>
                            </div>

                            <!-- Tipo de Usuário -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tipo de Usuário
                                </label>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                          :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'">
                                        {{ user.role === 'admin' ? 'Administrador' : 'Usuário' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status da Conta
                            </label>
                            <div class="flex items-center space-x-2">
                                <div 
                                    class="w-3 h-3 rounded-full"
                                    :class="user.ativo ? 'bg-green-500' : 'bg-red-500'"
                                ></div>
                                <span 
                                    class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium"
                                    :class="user.ativo ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'"
                                >
                                    {{ user.ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ user.ativo ? 'Usuário pode acessar o sistema' : 'Usuário não pode acessar o sistema' }}
                            </p>
                        </div>

                        <!-- Informações do Sistema -->
                        <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">
                                Informações do Sistema
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                        Data de Cadastro
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ formatarData(user.created_at) }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                        Última Atualização
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ formatarData(user.updated_at) }}
                                    </p>
                                </div>
                                <div v-if="user.email_verified_at">
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                        Email Verificado em
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ formatarData(user.email_verified_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Últimos Relatórios (se existirem) -->
                        <div v-if="user.relatorios && user.relatorios.length > 0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">
                                Últimos Relatórios Criados
                            </h4>
                            <div class="space-y-2">
                                <Link
                                    v-for="relatorio in user.relatorios"
                                    :key="relatorio.id"
                                    :href="route('relatorios.show', relatorio.id)"
                                    class="block p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
                                >
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ relatorio.titulo }}
                                            </h5>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                {{ relatorio.sector }} • {{ relatorio.activity }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatarData(relatorio.created_at) }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              :class="getStatusClass(relatorio.status)">
                                            {{ relatorio.status }}
                                        </span>
                                    </div>
                                </Link>
                            </div>
                            <div class="mt-3">
                                <Link :href="route('relatorios.index', { author: user.id })" 
                                      class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    Ver todos os relatórios deste usuário →
                                </Link>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum relatório criado</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Este usuário ainda não criou nenhum relatório no sistema.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmação de exclusão -->
        <ConfirmDialog 
            :show="showDeleteDialog"
            title="Confirmar Exclusão"
            :message="`Tem certeza que deseja excluir o usuário ${user.name}? Esta ação não pode ser desfeita.`"
            confirmText="Excluir"
            cancelText="Cancelar"
            type="danger"
            @confirm="excluirUsuario"
            @cancel="cancelarExclusao"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'

// Props
const props = defineProps({
    user: Object
})

// Estado reativo
const showDeleteDialog = ref(false)

// Métodos
const formatarData = (data) => {
    if (!data) return 'Não informado'
    return new Date(data).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getStatusClass = (status) => {
    const classes = {
        'Aberta': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'Em Andamento': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'Concluída': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'Cancelada': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
    }
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
}

const confirmarExclusao = () => {
    showDeleteDialog.value = true
}

const excluirUsuario = () => {
    router.delete(route('users.destroy', props.user.id), {
        preserveScroll: true,
        onFinish: () => {
            showDeleteDialog.value = false
        }
    })
}

const cancelarExclusao = () => {
    showDeleteDialog.value = false
}
</script> 