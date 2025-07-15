<template>
    <Head title="Detalhes do Motor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalhes do Motor') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Cabeçalho -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ motor.tag }} - {{ motor.equipamento }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Detalhes completos do motor
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <Link :href="route('motores.edit', motor.id)" class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-900 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </Link>
                                <Link :href="route('motores.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    Voltar
                                </Link>
                            </div>
                        </div>

                        <!-- Status e Badges -->
                        <div class="mb-6 flex flex-wrap gap-2">
                            <span :class="motor.armazenamento_class" class="px-3 py-1 text-sm rounded-full font-medium">
                                {{ motor.armazenamento }}
                            </span>
                            <span v-if="motor.reserva_almox" :class="motor.reserva_almox_class" class="px-3 py-1 text-sm rounded-full font-medium">
                                Reserva no Almoxarifado
                            </span>
                            <span :class="motor.ativo ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'" class="px-3 py-1 text-sm rounded-full font-medium">
                                {{ motor.ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        <!-- Informações Básicas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                    Informações Básicas
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">TAG:</span>
                                        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ motor.tag }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Equipamento:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.equipamento }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Carcaça:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.carcaca || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Fabricante:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.fabricante || 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Tipo/Modelo:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.tipo_equipamento || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                    Especificações Técnicas
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Potência KW:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.potencia_kw ? `${motor.potencia_kw} kW` : 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Potência CV:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.potencia_cv ? `${motor.potencia_cv} CV` : 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Rotação:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.rotacao ? `${motor.rotacao} RPM` : 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Corrente de Placa:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.corrente_placa ? `${motor.corrente_placa} A` : 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Corrente Configurada:</span>
                                        <span class="text-gray-900 dark:text-gray-100">{{ motor.corrente_configurada ? `${motor.corrente_configurada} A` : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Localização -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                Localização
                            </h4>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Local:</span>
                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ motor.local?.nome || 'N/A' }}</span>
                            </div>
                        </div>

                        <!-- Foto do Motor -->
                        <div v-if="motor.foto_url" class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                Foto do Motor
                            </h4>
                            <div class="flex justify-center">
                                <img 
                                    :src="motor.foto_url" 
                                    :alt="motor.equipamento"
                                    class="max-w-md h-auto rounded-lg shadow-md"
                                    @click="showImageModal = true"
                                    style="cursor: pointer;"
                                >
                            </div>
                        </div>

                        <!-- Observações -->
                        <div v-if="motor.observacoes" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                Observações
                            </h4>
                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ motor.observacoes }}</p>
                        </div>

                        <!-- Informações do Sistema -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                Informações do Sistema
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Criado em:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ formatDate(motor.created_at) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Última atualização:</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ formatDate(motor.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para visualizar imagem -->
        <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" @click="showImageModal = false">
            <div class="max-w-4xl max-h-full p-4">
                <img 
                    :src="motor.foto_url" 
                    :alt="motor.equipamento"
                    class="max-w-full max-h-full object-contain rounded-lg"
                    @click.stop
                >
                <button 
                    @click="showImageModal = false"
                    class="absolute top-4 right-4 text-white hover:text-gray-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    motor: Object,
})

const showImageModal = ref(false)

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script> 