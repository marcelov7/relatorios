<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar Usuário
            </h2>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <!-- Cabeçalho -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Editar Usuário: {{ user.name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Atualize as informações do usuário no sistema
                            </p>
                        </div>

                        <!-- Formulário -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Informações Básicas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nome -->
                                <div>
                                    <InputLabel for="name" value="Nome Completo" class="text-gray-700 dark:text-gray-300" />
                                    <TextInput
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>

                                <!-- Setor -->
                                <div>
                                    <InputLabel for="setor" value="Setor" class="text-gray-700 dark:text-gray-300" />
                                    <TextInput
                                        id="setor"
                                        v-model="form.setor"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        placeholder="Ex: Tecnologia da Informação"
                                    />
                                    <InputError class="mt-2" :message="form.errors.setor" />
                                </div>

                                <!-- Cargo -->
                                <div>
                                    <InputLabel for="cargo" value="Cargo" class="text-gray-700 dark:text-gray-300" />
                                    <TextInput
                                        id="cargo"
                                        v-model="form.cargo"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        placeholder="Ex: Desenvolvedor Senior"
                                    />
                                    <InputError class="mt-2" :message="form.errors.cargo" />
                                </div>

                                <!-- Telefone -->
                                <div>
                                    <InputLabel for="telefone" value="Telefone" class="text-gray-700 dark:text-gray-300" />
                                    <TextInput
                                        id="telefone"
                                        v-model="form.telefone"
                                        type="tel"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        placeholder="(11) 99999-9999"
                                    />
                                    <InputError class="mt-2" :message="form.errors.telefone" />
                                </div>

                                <!-- Tipo de Usuário -->
                                <div>
                                    <InputLabel for="role" value="Tipo de Usuário" class="text-gray-700 dark:text-gray-300" />
                                    <select
                                        id="role"
                                        v-model="form.role"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">Selecione o tipo</option>
                                        <option value="user">Usuário</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.role" />
                                </div>
                            </div>

                            <!-- Senha (opcional para edição) -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Alterar Senha (opcional)
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Nova Senha -->
                                    <div>
                                        <InputLabel for="password" value="Nova Senha" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            placeholder="Deixe em branco para manter a senha atual"
                                        />
                                        <InputError class="mt-2" :message="form.errors.password" />
                                    </div>

                                    <!-- Confirmar Nova Senha -->
                                    <div>
                                        <InputLabel for="password_confirmation" value="Confirmar Nova Senha" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            placeholder="Confirme a nova senha"
                                        />
                                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    Deixe os campos de senha em branco se não desejar alterá-la.
                                </p>
                            </div>

                            <!-- Status do usuário -->
                            <div class="flex items-center">
                                <Checkbox
                                    id="ativo"
                                    v-model:checked="form.ativo"
                                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                />
                                <label for="ativo" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Usuário ativo
                                </label>
                            </div>

                            <!-- Informações adicionais -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-sm">
                                        <p class="text-blue-800 dark:text-blue-200 font-medium">Informações do usuário</p>
                                        <p class="text-blue-700 dark:text-blue-300 mt-1">
                                            Criado em {{ formatarData(user.created_at) }}
                                        </p>
                                        <p v-if="user.email_verified_at" class="text-blue-700 dark:text-blue-300">
                                            Email verificado em {{ formatarData(user.email_verified_at) }}
                                        </p>
                                        <p v-else class="text-orange-700 dark:text-orange-300">
                                            Email não verificado
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de ação -->
                            <div class="flex flex-col sm:flex-row items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Atualizando...' : 'Atualizar Usuário' }}
                                </PrimaryButton>
                                
                                <Link :href="route('users.show', user.id)" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Visualizar
                                </Link>
                                
                                <Link :href="route('users.index')" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Cancelar
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'
import { Link } from '@inertiajs/vue3'

// Props
const props = defineProps({
    user: Object
})

// Formulário
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    setor: props.user.setor || '',
    cargo: props.user.cargo || '',
    telefone: props.user.telefone || '',
    role: props.user.role,
    ativo: props.user.ativo
})

// Métodos
const submit = () => {
    form.put(route('users.update', props.user.id), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        }
    })
}

const formatarData = (data) => {
    if (!data) return ''
    return new Date(data).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script> 