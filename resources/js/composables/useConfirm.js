import { ref, reactive } from 'vue'

const confirmDialog = ref(null)

export function useConfirm() {
    const showConfirm = (options = {}) => {
        return new Promise((resolve) => {
            confirmDialog.value = {
                show: true,
                title: options.title || 'Confirmar Ação',
                message: options.message || 'Tem certeza que deseja continuar?',
                type: options.type || 'warning', // warning, danger, info, success
                confirmText: options.confirmText || 'Confirmar',
                cancelText: options.cancelText || 'Cancelar',
                icon: options.icon || 'exclamation-triangle',
                onConfirm: () => {
                    confirmDialog.value = null
                    resolve(true)
                },
                onCancel: () => {
                    confirmDialog.value = null
                    resolve(false)
                }
            }
        })
    }
    
    const confirmDelete = (itemName = 'este item') => {
        return showConfirm({
            title: 'Confirmar Exclusão',
            message: `Tem certeza que deseja excluir ${itemName}? Esta ação não pode ser desfeita.`,
            type: 'danger',
            confirmText: 'Excluir',
            cancelText: 'Cancelar',
            icon: 'trash'
        })
    }
    
    const confirmSave = (message = 'Deseja salvar as alterações?') => {
        return showConfirm({
            title: 'Salvar Alterações',
            message,
            type: 'info',
            confirmText: 'Salvar',
            cancelText: 'Cancelar',
            icon: 'save'
        })
    }
    
    const confirmLeave = (message = 'Você tem alterações não salvas. Deseja sair mesmo assim?') => {
        return showConfirm({
            title: 'Alterações Não Salvas',
            message,
            type: 'warning',
            confirmText: 'Sair sem Salvar',
            cancelText: 'Continuar Editando',
            icon: 'exclamation-triangle'
        })
    }
    
    const hideConfirm = () => {
        if (confirmDialog.value) {
            confirmDialog.value.onCancel()
        }
    }
    
    return {
        confirmDialog,
        showConfirm,
        confirmDelete,
        confirmSave,
        confirmLeave,
        hideConfirm
    }
} 