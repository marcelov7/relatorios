import { ref, reactive } from 'vue'

const notifications = ref([])
let notificationId = 0

export function useNotifications() {
    const addNotification = (notification) => {
        const id = ++notificationId
        const newNotification = {
            id,
            ...notification,
            timestamp: Date.now()
        }
        
        notifications.value.push(newNotification)
        
        // Auto-remove após 5 segundos (ou tempo customizado)
        setTimeout(() => {
            removeNotification(id)
        }, notification.duration || 5000)
        
        return id
    }
    
    const removeNotification = (id) => {
        const index = notifications.value.findIndex(n => n.id === id)
        if (index > -1) {
            notifications.value.splice(index, 1)
        }
    }
    
    const success = (message, options = {}) => {
        return addNotification({
            type: 'success',
            title: options.title || 'Sucesso!',
            message,
            icon: 'check-circle',
            ...options
        })
    }
    
    const error = (message, options = {}) => {
        return addNotification({
            type: 'error',
            title: options.title || 'Erro!',
            message,
            icon: 'x-circle',
            duration: 7000, // Erros ficam mais tempo
            ...options
        })
    }
    
    const warning = (message, options = {}) => {
        return addNotification({
            type: 'warning',
            title: options.title || 'Atenção!',
            message,
            icon: 'exclamation-triangle',
            ...options
        })
    }
    
    const info = (message, options = {}) => {
        return addNotification({
            type: 'info',
            title: options.title || 'Informação',
            message,
            icon: 'information-circle',
            ...options
        })
    }
    
    const clear = () => {
        notifications.value = []
    }
    
    return {
        notifications,
        addNotification,
        removeNotification,
        success,
        error,
        warning,
        info,
        clear
    }
} 