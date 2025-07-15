<template>
    <teleport to="body">
        <div class="fixed top-4 right-4 z-50 space-y-3 max-w-sm">
            <transition-group
                name="toast"
                tag="div"
                class="space-y-3"
            >
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg dark:shadow-gray-900/20 p-4 max-w-sm w-full transform transition-all duration-300"
                    :class="getNotificationClasses(notification.type)"
                >
                    <div class="flex items-start">
                        <!-- Ícone -->
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center"
                                 :class="getIconBgClass(notification.type)">
                                <svg class="w-4 h-4" :class="getIconClass(notification.type)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <!-- Ícone de Sucesso -->
                                    <path v-if="notification.icon === 'check-circle'" 
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    <!-- Ícone de Erro -->
                                    <path v-else-if="notification.icon === 'x-circle'" 
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    <!-- Ícone de Aviso -->
                                    <path v-else-if="notification.icon === 'exclamation-triangle'" 
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    <!-- Ícone de Info -->
                                    <path v-else-if="notification.icon === 'information-circle'" 
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Conteúdo -->
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ notification.title }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ notification.message }}
                            </p>
                        </div>
                        
                        <!-- Botão Fechar -->
                        <div class="ml-4 flex-shrink-0">
                            <button
                                @click="removeNotification(notification.id)"
                                class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            >
                                <span class="sr-only">Fechar</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Barra de Progresso -->
                    <div v-if="notification.showProgress !== false" class="mt-3">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                            <div 
                                class="h-1 rounded-full transition-all duration-100 ease-linear"
                                :class="getProgressBarClass(notification.type)"
                                :style="{ width: getProgressWidth(notification) + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </transition-group>
        </div>
    </teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { useNotifications } from '@/composables/useNotifications'

const { notifications, removeNotification } = useNotifications()

// Controle da barra de progresso
const progressIntervals = ref(new Map())

const getNotificationClasses = (type) => {
    const baseClasses = 'border-l-4'
    switch (type) {
        case 'success':
            return `${baseClasses} border-green-500`
        case 'error':
            return `${baseClasses} border-red-500`
        case 'warning':
            return `${baseClasses} border-yellow-500`
        case 'info':
            return `${baseClasses} border-blue-500`
        default:
            return `${baseClasses} border-gray-500`
    }
}

const getIconBgClass = (type) => {
    switch (type) {
        case 'success':
            return 'bg-green-100 dark:bg-green-900/20'
        case 'error':
            return 'bg-red-100 dark:bg-red-900/20'
        case 'warning':
            return 'bg-yellow-100 dark:bg-yellow-900/20'
        case 'info':
            return 'bg-blue-100 dark:bg-blue-900/20'
        default:
            return 'bg-gray-100 dark:bg-gray-900/20'
    }
}

const getIconClass = (type) => {
    switch (type) {
        case 'success':
            return 'text-green-600 dark:text-green-400'
        case 'error':
            return 'text-red-600 dark:text-red-400'
        case 'warning':
            return 'text-yellow-600 dark:text-yellow-400'
        case 'info':
            return 'text-blue-600 dark:text-blue-400'
        default:
            return 'text-gray-600 dark:text-gray-400'
    }
}

const getProgressBarClass = (type) => {
    switch (type) {
        case 'success':
            return 'bg-green-500'
        case 'error':
            return 'bg-red-500'
        case 'warning':
            return 'bg-yellow-500'
        case 'info':
            return 'bg-blue-500'
        default:
            return 'bg-gray-500'
    }
}

const getProgressWidth = (notification) => {
    if (!notification.progress) return 0
    return notification.progress
}

// Gerenciar barras de progresso
const startProgressBar = (notification) => {
    if (notification.showProgress === false) return
    
    const duration = notification.duration || 5000
    const interval = 50 // Atualizar a cada 50ms
    const step = (100 / duration) * interval
    
    notification.progress = 100
    
    const progressInterval = setInterval(() => {
        notification.progress -= step
        if (notification.progress <= 0) {
            clearInterval(progressInterval)
            progressIntervals.value.delete(notification.id)
        }
    }, interval)
    
    progressIntervals.value.set(notification.id, progressInterval)
}

// Observar novas notificações
watch(notifications, (newNotifications) => {
    newNotifications.forEach(notification => {
        if (!progressIntervals.value.has(notification.id)) {
            startProgressBar(notification)
        }
    })
}, { deep: true })

onUnmounted(() => {
    // Limpar todos os intervalos
    progressIntervals.value.forEach(interval => clearInterval(interval))
    progressIntervals.value.clear()
})
</script>

<style scoped>
/* Animações dos toasts */
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.toast-move {
    transition: transform 0.3s ease;
}
</style> 