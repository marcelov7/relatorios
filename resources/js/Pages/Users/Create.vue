<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Criar Novo Usuário
            </h2>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <!-- Cabeçalho -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Informações do Usuário
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Preencha os dados para criar um novo usuário no sistema
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

                            <!-- Senha -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Senha -->
                                <div>
                                    <InputLabel for="password" value="Senha" class="text-gray-700 dark:text-gray-300" />
                                    <TextInput
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.password" />
                                </div>

                                <!-- Confirmar Senha -->
                                <div>
                                    <InputLabel for="password_confirmation" value="Confirmar Senha" class="text-gray-700 dark:text-gray-300" />
                                    <TextInput
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                </div>
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

                            <!-- Botões de ação -->
                            <div class="flex flex-col sm:flex-row items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Criando...' : 'Criar Usuário' }}
                                </PrimaryButton>
                                
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
    ativo: true
})

// Métodos
const submit = () => {
    form.post(route('users.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        }
    })
}
</script> 