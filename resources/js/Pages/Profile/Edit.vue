<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue'

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100"
            >
                Perfil
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-4 shadow-sm sm:rounded-lg sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-4 shadow-sm sm:rounded-lg sm:p-8"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 p-4 shadow-sm sm:rounded-lg sm:p-8"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>

                <!-- Histórico de Relatórios -->
                <div class="bg-white dark:bg-gray-700 p-4 shadow sm:rounded-lg sm:p-8">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Histórico de Relatórios</h3>
                    <div v-if="$page.props.relatorios && $page.props.relatorios.data.length">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                            <li v-for="relatorio in $page.props.relatorios.data" :key="relatorio.id" class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ relatorio.titulo }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ relatorio.created_at ? (() => {
                                            const data = new Date(relatorio.created_at);
                                            const dataFormatada = data.toLocaleDateString('pt-BR');
                                            const horaFormatada = data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                                            return `${dataFormatada} às ${horaFormatada}`;
                                        })() : '' }}
                                    </div>
                                    <div class="text-xs">
                                        Status:
                                        <span :class="{
                                            'text-green-600 dark:text-green-400': relatorio.status === 'Concluída',
                                            'text-yellow-600 dark:text-yellow-400': relatorio.status === 'Em Andamento',
                                            'text-blue-600 dark:text-blue-400': relatorio.status === 'Aberta',
                                            'text-red-600 dark:text-red-400': relatorio.status === 'Cancelada',
                                            'text-gray-600 dark:text-gray-300': !['Concluída','Em Andamento','Aberta','Cancelada'].includes(relatorio.status)
                                        }">{{ relatorio.status }}</span>
                                    </div>
                                </div>
                                <a :href="`/relatorios/${relatorio.id}`" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Ver</a>
                            </li>
                        </ul>
                        <Pagination :links="$page.props.relatorios" class="mt-6" />
                    </div>
                    <div v-else class="text-gray-500 dark:text-gray-400 text-sm">Nenhum relatório criado ainda.</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
