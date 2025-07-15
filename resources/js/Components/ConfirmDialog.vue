<template>
    <teleport to="body">
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="confirmDialog?.show" 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Overlay -->
                    <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" 
                         @click="confirmDialog.onCancel"></div>
                    
                    <!-- Modal -->
                    <transition
                        enter-active-class="transition duration-300 ease-out transform"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="transition duration-200 ease-in transform"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                            <!-- Cabeçalho -->
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <!-- Ícone -->
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                                         :class="getIconBgClass(confirmDialog.type)">
                                        <svg class="h-6 w-6" :class="getIconClass(confirmDialog.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <!-- Ícone de Trash/Excluir -->
                                            <path v-if="confirmDialog.icon === 'trash'" 
                                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            <!-- Ícone de Aviso -->
                                            <path v-else-if="confirmDialog.icon === 'exclamation-triangle'" 
                                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            <!-- Ícone de Info -->
                                            <path v-else-if="confirmDialog.icon === 'information-circle'" 
                                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            <!-- Ícone de Save -->
                                            <path v-else-if="confirmDialog.icon === 'save'" 
                                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                            <!-- Ícone de Check/Sucesso -->
                                            <path v-else-if="confirmDialog.icon === 'check-circle'" 
                                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            <!-- Ícone padrão (aviso) -->
                                            <path v-else 
                                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Conteúdo -->
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                            {{ confirmDialog.title }}
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ confirmDialog.message }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Rodapé com Botões -->
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <!-- Botão Confirmar -->
                                <button 
                                    @click="confirmDialog.onConfirm" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                                    :class="getConfirmButtonClass(confirmDialog.type)"
                                >
                                    {{ confirmDialog.confirmText }}
                                </button>
                                
                                <!-- Botão Cancelar -->
                                <button 
                                    @click="confirmDialog.onCancel" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200"
                                >
                                    {{ confirmDialog.cancelText }}
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<script setup>
import { useConfirm } from '@/composables/useConfirm'

const { confirmDialog } = useConfirm()

const getIconBgClass = (type) => {
    switch (type) {
        case 'danger':
            return 'bg-red-100 dark:bg-red-900/20'
        case 'warning':
            return 'bg-yellow-100 dark:bg-yellow-900/20'
        case 'info':
            return 'bg-blue-100 dark:bg-blue-900/20'
        case 'success':
            return 'bg-green-100 dark:bg-green-900/20'
        default:
            return 'bg-gray-100 dark:bg-gray-900/20'
    }
}

const getIconClass = (type) => {
    switch (type) {
        case 'danger':
            return 'text-red-600 dark:text-red-400'
        case 'warning':
            return 'text-yellow-600 dark:text-yellow-400'
        case 'info':
            return 'text-blue-600 dark:text-blue-400'
        case 'success':
            return 'text-green-600 dark:text-green-400'
        default:
            return 'text-gray-600 dark:text-gray-400'
    }
}

const getConfirmButtonClass = (type) => {
    const baseClasses = 'focus:ring-offset-2 dark:focus:ring-offset-gray-800'
    
    switch (type) {
        case 'danger':
            return `${baseClasses} bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 focus:ring-red-500 dark:focus:ring-red-600`
        case 'warning':
            return `${baseClasses} bg-yellow-600 dark:bg-yellow-700 hover:bg-yellow-700 dark:hover:bg-yellow-800 focus:ring-yellow-500 dark:focus:ring-yellow-600`
        case 'info':
            return `${baseClasses} bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-800 focus:ring-blue-500 dark:focus:ring-blue-600`
        case 'success':
            return `${baseClasses} bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 focus:ring-green-500 dark:focus:ring-green-600`
        default:
            return `${baseClasses} bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-800 focus:ring-gray-500 dark:focus:ring-gray-600`
    }
}
</script> 