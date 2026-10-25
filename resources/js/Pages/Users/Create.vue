<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Criar Novo Usuário
                </h2>
                <Link :href="route('users.index')" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar para lista
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-100 dark:border-gray-700">
                    
                    <!-- Cabeçalho do Card -->
                    <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-900 text-white">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <span>📋 Cadastro de Usuário</span>
                        </h3>
                        <p class="text-xs text-blue-100 dark:text-blue-200 mt-1">
                            Preencha as informações abaixo para conceder acesso ao sistema
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-8">
                        
                        <!-- SEÇÃO 1: DADOS PESSOAIS -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                1. Informações Pessoais & Contato
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div>
                                    <InputLabel for="name" value="Nome Completo *" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all"
                                        placeholder="Ex: João da Silva"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-1 text-xs" :message="form.errors.name" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <InputLabel for="email" value="E-mail de Acesso *" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all"
                                        placeholder="joao@empresa.com.br"
                                        required
                                    />
                                    <InputError class="mt-1 text-xs" :message="form.errors.email" />
                                </div>

                                <!-- Telefone -->
                                <div class="md:col-span-2">
                                    <InputLabel for="telefone" value="Telefone / WhatsApp" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <TextInput
                                        id="telefone"
                                        v-model="form.telefone"
                                        type="tel"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all"
                                        placeholder="(11) 99999-9999"
                                    />
                                    <InputError class="mt-1 text-xs" :message="form.errors.telefone" />
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 2: PERFIL E ATRIBUÇÃO -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                2. Perfil de Acesso & Organização
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tipo de Usuário -->
                                <div>
                                    <InputLabel for="role" value="Perfil / Permissão *" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all text-sm"
                                        required
                                    >
                                        <option value="">Selecione o perfil</option>
                                        <option value="user">👤 Usuário Comum</option>
                                        <option value="admin">⭐ Administrador do Tenant</option>
                                        <option value="super_admin">👑 Super Admin Global (SaaS)</option>
                                    </select>
                                    <InputError class="mt-1 text-xs" :message="form.errors.role" />
                                </div>

                                <!-- Empresa / Tenant (Visível para Super Admin) -->
                                <div v-if="$page.props.auth.user?.is_super_admin">
                                    <InputLabel for="tenant_id" value="Empresa Vinculada (Tenant)" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <select
                                        id="tenant_id"
                                        v-model="form.tenant_id"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all text-sm"
                                    >
                                        <option value="">🏢 Nenhuma (Acesso Global SaaS)</option>
                                        <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                                            🏢 {{ tenant.nome }}
                                        </option>
                                    </select>
                                    <InputError class="mt-1 text-xs" :message="form.errors.tenant_id" />
                                </div>

                                <!-- Setor -->
                                <div>
                                    <InputLabel for="setor" value="Setor / Departamento" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <TextInput
                                        id="setor"
                                        v-model="form.setor"
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all"
                                        placeholder="Ex: Manutenção Elétrica"
                                    />
                                    <InputError class="mt-1 text-xs" :message="form.errors.setor" />
                                </div>

                                <!-- Cargo -->
                                <div>
                                    <InputLabel for="cargo" value="Cargo / Função" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <TextInput
                                        id="cargo"
                                        v-model="form.cargo"
                                        type="text"
                                        class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all"
                                        placeholder="Ex: Técnico Eletricista"
                                    />
                                    <InputError class="mt-1 text-xs" :message="form.errors.cargo" />
                                </div>
                            </div>

                            <!-- Permissões Especiais: Endereços Lógicos -->
                            <div class="mt-6 p-4 rounded-xl bg-blue-50/60 dark:bg-gray-900/60 border border-blue-100 dark:border-blue-900/40">
                                <h5 class="text-xs font-bold text-blue-900 dark:text-blue-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Permissões para Endereços Lógicos (PLC / Supervisório)
                                </h5>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                    Por padrão todos os usuários podem pesquisar e visualizar. Marque abaixo caso este usuário possa alterar ou excluir registros:
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="flex items-start">
                                        <Checkbox
                                            id="can_edit_enderecos"
                                            v-model:checked="form.can_edit_enderecos"
                                            class="mt-0.5 rounded w-4 h-4 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                        />
                                        <label for="can_edit_enderecos" class="ml-2.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                            <span class="font-semibold block text-gray-900 dark:text-white">Pode Cadastrar / Editar Endereços</span>
                                            Permite adicionar novos tags e atualizar descrições e endereços.
                                        </label>
                                    </div>
                                    <div class="flex items-start">
                                        <Checkbox
                                            id="can_delete_enderecos"
                                            v-model:checked="form.can_delete_enderecos"
                                            class="mt-0.5 rounded w-4 h-4 text-red-600 focus:ring-red-500 cursor-pointer"
                                        />
                                        <label for="can_delete_enderecos" class="ml-2.5 text-xs text-gray-700 dark:text-gray-300 cursor-pointer">
                                            <span class="font-semibold block text-red-700 dark:text-red-400">Pode Excluir Endereços</span>
                                            Permissão crítica para deletar tags e endereços lógicos.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 3: SEGURANÇA E SENHA -->
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                3. Credenciais de Acesso (Senha)
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Senha com Olho -->
                                <div>
                                    <InputLabel for="password" value="Senha *" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <div class="relative mt-1">
                                        <input
                                            id="password"
                                            v-model="form.password"
                                            :type="showPassword ? 'text' : 'password'"
                                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm pr-10 transition-all text-sm"
                                            placeholder="Mínimo 8 caracteres"
                                            required
                                        />
                                        <button 
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none"
                                            tabindex="-1"
                                        >
                                            <!-- Ícone Olho Aberto -->
                                            <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <!-- Ícone Olho Fechado -->
                                            <svg v-else class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.997 8.997 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21f-3.23-3.230" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                            </svg>
                                        </button>
                                    </div>
                                    <InputError class="mt-1 text-xs" :message="form.errors.password" />
                                </div>

                                <!-- Confirmar Senha com Olho -->
                                <div>
                                    <InputLabel for="password_confirmation" value="Confirmar Senha *" class="text-gray-700 dark:text-gray-300 font-medium" />
                                    <div class="relative mt-1">
                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            :type="showConfirmPassword ? 'text' : 'password'"
                                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm pr-10 transition-all text-sm"
                                            placeholder="Repita a senha"
                                            required
                                        />
                                        <button 
                                            type="button"
                                            @click="showConfirmPassword = !showConfirmPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none"
                                            tabindex="-1"
                                        >
                                            <!-- Ícone Olho Aberto -->
                                            <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <!-- Ícone Olho Fechado -->
                                            <svg v-else class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.997 8.997 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21f-3.23-3.230" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                            </svg>
                                        </button>
                                    </div>
                                    <InputError class="mt-1 text-xs" :message="form.errors.password_confirmation" />
                                </div>
                            </div>
                        </div>

                        <!-- SEÇÃO 4: STATUS -->
                        <div class="pt-2">
                            <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                                <Checkbox
                                    id="ativo"
                                    v-model:checked="form.ativo"
                                    class="rounded w-5 h-5 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500 shadow-sm cursor-pointer"
                                />
                                <label for="ativo" class="ml-3 text-sm font-semibold text-gray-800 dark:text-gray-200 cursor-pointer">
                                    Usuário Ativo no Sistema
                                    <span class="block text-xs font-normal text-gray-500 dark:text-gray-400">
                                        Usuários inativos não conseguem fazer login no aplicativo ou painel web.
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- BOTÕES DE AÇÃO -->
                        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <Link :href="route('users.index')" 
                                class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold text-sm hover:bg-gray-50 dark:hover:bg-gray-600 text-center transition-all">
                                Cancelar
                            </Link>

                            <PrimaryButton 
                                class="w-full sm:w-auto px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                                :disabled="form.processing"
                            >
                                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ form.processing ? 'Cadastrando...' : 'Cadastrar Usuário' }}
                            </PrimaryButton>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'

// Props
const props = defineProps({
    tenants: Array
})

// Controle de Visibilidade de Senha
const showPassword = ref(false)
const showConfirmPassword = ref(false)

// Formulário
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    setor: '',
    cargo: '',
    telefone: '',
    role: '',
    can_edit_enderecos: false,
    can_delete_enderecos: false,
    tenant_id: '',
    ativo: true
})

// Submissão do Formulário
const submit = () => {
    form.post(route('users.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        }
    })
}
</script>