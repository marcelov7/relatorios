<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detalhes do Motor
            </h2>
        </template>



        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Cabeçalho com botões de ação -->
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
                        <Link
                            :href="route('motores.edit', motor.id)"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-900 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </Link>
                        <Link
                            :href="route('motores.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Voltar
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Informações Principais -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Informações Básicas -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Informações Básicas
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tag</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.tag }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipamento</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.equipamento }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Carcaça (Fabricante)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.carcaca_fabricante || 'Não informado' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fabricante</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.fabricante || 'Não informado' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Equipamento / Modelo</dt>
                                        <dd class="mt-1">
                                            <span v-if="motor.tipo_equipamento_modelo" :class="getTipoEquipamentoModeloClass(motor.tipo_equipamento_modelo)" class="px-2 py-1 text-xs rounded-full">
                                                {{ motor.tipo_equipamento_modelo }}
                                            </span>
                                            <span v-else class="text-sm text-gray-500 dark:text-gray-400">Não informado</span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reserva no Almoxarifado</dt>
                                        <dd class="mt-1">
                                            <span :class="getReservaAlmoxClass(motor.reserva_almox)" class="px-2 py-1 text-xs rounded-full">
                                                {{ motor.reserva_almox || 'Não Reservado' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status de Armazenamento</dt>
                                        <dd class="mt-1">
                                            <span v-if="motor.armazenamento" class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                {{ motor.armazenamento }}
                                            </span>
                                            <span v-else class="text-sm text-gray-500 dark:text-gray-400">Não informado</span>
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Especificações Técnicas -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Especificações Técnicas
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Potência (kW)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.potencia_kw || 'Não informado' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Potência (CV)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.potencia_cv || 'Não informado' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rotação (RPM)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.rotacao || 'Não informado' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Corrente de Placa (A)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.corrente_placa || 'Não informado' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Corrente Configurada (A)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ motor.corrente_configurada || 'Não informado' }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Localização -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Localização
                                </h3>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Local</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {{ motor.local || 'Não informado' }}
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div v-if="motor.observacoes" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Observações
                                </h3>
                                <div class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">
                                    {{ motor.observacoes }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Foto do Motor -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Foto do Motor
                                </h3>
                                <div v-if="motor.foto" class="text-center">
                                    <img 
                                        :src="`/storage/${motor.foto}`" 
                                        :alt="motor.tag"
                                        class="w-full h-64 object-cover rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer"
                                        @click="openImageModal"
                                    />
                                </div>
                                <div v-else class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nenhuma foto disponível</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informações do Sistema -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Informações do Sistema
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                        <dd class="mt-1">
                                            <span :class="motor.ativo ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'" class="px-2 py-1 text-xs rounded-full">
                                                {{ motor.ativo ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Criado em</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(motor.created_at) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Última atualização</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(motor.updated_at) }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <!-- Modal da Imagem -->
        <div v-if="showImageModal" class="fixed inset-0 z-50 overflow-y-auto" @click="closeImageModal">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ motor.tag }} - {{ motor.equipamento }}
                            </h3>
                            <button @click="closeImageModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex justify-center">
                            <img 
                                :src="`/storage/${motor.foto}`" 
                                :alt="motor.tag"
                                class="max-w-full max-h-96 object-contain rounded-lg"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    motor: Object,
})

const showImageModal = ref(false)

const openImageModal = () => {
    showImageModal.value = true
}

const closeImageModal = () => {
    showImageModal.value = false
}

const formatDate = (date) => {
    if (!date) return 'Não informado'
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getReservaAlmoxClass = (reservaAlmox) => {
    return reservaAlmox && reservaAlmox.trim() !== ''
        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
        : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

const getTipoEquipamentoModeloClass = (tipo) => {
    return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
}


</script> 