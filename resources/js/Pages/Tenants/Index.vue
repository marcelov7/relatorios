<template>
    <AppLayout title="Empresas (Tenants)" subtitle="Gerenciamento de empresas no sistema SaaS">
        <div class="space-y-6">
            <!-- Top Action Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Empresas Cadastradas</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gerencie todos os clientes e tenants ativos na plataforma.</p>
                </div>
                <button 
                    @click="openModal()" 
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg shadow-sm transition-colors duration-200"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Empresa
                </button>
            </div>

            <!-- List of Tenants -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug / CNPJ</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estatísticas</th>
                                <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            <tr v-for="tenant in tenants.data" :key="tenant.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ tenant.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ tenant.nome }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div><span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded text-xs">{{ tenant.slug }}</span></div>
                                    <div v-if="tenant.documento" class="text-xs text-gray-400 mt-1">{{ tenant.documento }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center gap-3">
                                        <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded">👥 {{ tenant.users_count }} Usuários</span>
                                        <span class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded">📄 {{ tenant.relatorios_count }} Relatórios</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        :class="tenant.ativo 
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' 
                                            : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ tenant.ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button 
                                        @click="openModal(tenant)" 
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
                                    >
                                        Editar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!tenants.data || tenants.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Nenhuma empresa cadastrada.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Formulário Tenant -->
            <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ editingTenant ? 'Editar Empresa' : 'Nova Empresa' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            ✕
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome da Empresa</label>
                            <input 
                                v-model="form.nome" 
                                type="text" 
                                required 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug (identificador URL)</label>
                            <input 
                                v-model="form.slug" 
                                type="text" 
                                placeholder="opcional (gerado automaticamente)"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">CNPJ / Documento</label>
                            <input 
                                v-model="form.documento" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                            />
                        </div>

                        <div v-if="!editingTenant" class="border-t border-gray-200 dark:border-gray-700 pt-3 space-y-3">
                            <h4 class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Criar Usuário Administrador (Opcional)</h4>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Nome do Admin</label>
                                <input v-model="form.admin_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Email do Admin</label>
                                <input v-model="form.admin_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Senha do Admin</label>
                                <input v-model="form.admin_password" type="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-xs" />
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input 
                                id="ativo" 
                                v-model="form.ativo" 
                                type="checkbox" 
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                            />
                            <label for="ativo" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Empresa Ativa</label>
                        </div>

                        <div class="flex justify-end gap-2 border-t border-gray-200 dark:border-gray-700 pt-3">
                            <button 
                                type="button" 
                                @click="closeModal" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm disabled:opacity-50"
                            >
                                {{ editingTenant ? 'Salvar Alterações' : 'Criar Empresa' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    tenants: Object
})

const showModal = ref(false)
const editingTenant = ref(null)

const form = useForm({
    nome: '',
    slug: '',
    documento: '',
    ativo: true,
    admin_name: '',
    admin_email: '',
    admin_password: '',
})

const openModal = (tenant = null) => {
    editingTenant.value = tenant
    if (tenant) {
        form.nome = tenant.nome
        form.slug = tenant.slug
        form.documento = tenant.documento || ''
        form.ativo = tenant.ativo
        form.admin_name = ''
        form.admin_email = ''
        form.admin_password = ''
    } else {
        form.reset()
        form.ativo = true
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingTenant.value = null
    form.reset()
}

const submitForm = () => {
    if (editingTenant.value) {
        form.put(route('tenants.update', editingTenant.value.id), {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post(route('tenants.store'), {
            onSuccess: () => closeModal(),
        })
    }
}
</script>
