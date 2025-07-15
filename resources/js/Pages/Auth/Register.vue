<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useTheme } from '@/composables/useTheme';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const { initTheme } = useTheme();

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

onMounted(() => {
    initTheme();
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <Head title="Cadastro - Sistema de Relatórios" />
        
        <!-- Theme Toggle -->
        <div class="absolute top-4 right-4">
            <ThemeToggle />
        </div>

        <!-- Logo e Título -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900 dark:text-white">
                Criar Conta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Cadastre-se para acessar o sistema
            </p>
        </div>

        <!-- Formulário de Registro -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-lg sm:rounded-lg sm:px-10 border border-gray-200 dark:border-gray-700">
                
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nome -->
                    <div>
                        <InputLabel for="name" value="Nome Completo" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1">
                            <TextInput
                                id="name"
                                type="text"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Digite seu nome completo"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="E-mail" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1">
                            <TextInput
                                id="email"
                                type="email"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="Digite seu e-mail"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Senha -->
                    <div>
                        <InputLabel for="password" value="Senha" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1">
                            <TextInput
                                id="password"
                                type="password"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="Digite uma senha"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Confirmar Senha -->
                    <div>
                        <InputLabel for="password_confirmation" value="Confirmar Senha" class="text-gray-700 dark:text-gray-300" />
                        <div class="mt-1">
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirme sua senha"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <!-- Botão de Cadastro -->
                    <div>
                        <PrimaryButton
                            type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ form.processing ? 'Cadastrando...' : 'Cadastrar' }}
                        </PrimaryButton>
                    </div>

                    <!-- Link para Login -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Já tem uma conta?
                            <Link 
                                :href="route('login')" 
                                class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300"
                            >
                                Faça login aqui
                            </Link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
