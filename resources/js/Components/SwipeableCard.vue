<template>
    <div class="relative overflow-hidden rounded-lg shadow dark:shadow-gray-900/20 hover:shadow-lg dark:hover:shadow-gray-900/30 transition-shadow duration-200">
        <!-- Ações de fundo (reveladas no swipe) - só visíveis quando necessário -->
        <div class="absolute inset-y-0 right-0 flex items-center z-0 pointer-events-none" 
             :class="{ 
                'opacity-0 pointer-events-none': translateX >= 0, 
                'opacity-100 pointer-events-auto': translateX < 0 
             }">
            <button @click.stop="emit('edit')" 
                    class="bg-yellow-500 dark:bg-yellow-600 hover:bg-yellow-600 dark:hover:bg-yellow-700 text-white px-6 h-full flex items-center justify-center transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </button>
            <button @click.stop="emit('delete')" 
                    class="bg-red-500 dark:bg-red-600 hover:bg-red-600 dark:hover:bg-red-700 text-white px-6 h-full flex items-center justify-center transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
        
        <!-- Conteúdo principal do card -->
        <div 
            ref="cardRef"
            class="bg-white dark:bg-gray-800 relative z-20 transition-transform duration-200 ease-out cursor-pointer border border-gray-200 dark:border-gray-700"
            :style="{ transform: `translateX(${translateX}px)` }"
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
            @click="handleClick"
        >
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['edit', 'delete', 'click'])

const cardRef = ref(null)
const translateX = ref(0)
const startX = ref(0)
const currentX = ref(0)
const isDragging = ref(false)
const hasSwipeStarted = ref(false)

const MAX_SWIPE = -120 // Máximo que o card pode ser arrastado para a esquerda

const handleTouchStart = (e) => {
    startX.value = e.touches[0].clientX
    currentX.value = e.touches[0].clientX
    isDragging.value = true
    hasSwipeStarted.value = false
}

const handleTouchMove = (e) => {
    if (!isDragging.value) return
    
    currentX.value = e.touches[0].clientX
    const diffX = currentX.value - startX.value
    
    // Só permite swipe para a esquerda
    if (diffX < 0) {
        hasSwipeStarted.value = true
        translateX.value = Math.max(diffX, MAX_SWIPE)
        e.preventDefault() // Previne scroll quando fazendo swipe
    }
}

const handleTouchEnd = () => {
    if (!isDragging.value) return
    
    isDragging.value = false
    
    // Se arrastou mais que metade, mantém aberto
    if (translateX.value < MAX_SWIPE / 2) {
        translateX.value = MAX_SWIPE
    } else {
        translateX.value = 0
    }
}

const handleClick = (e) => {
    // Se houve swipe, não emite click
    if (hasSwipeStarted.value) {
        e.preventDefault()
        return
    }
    
    // Se o card está com swipe aberto, fecha
    if (translateX.value < 0) {
        translateX.value = 0
        e.preventDefault()
        return
    }
    
    // Emite evento de click normal
    emit('click', e)
}

// Função para fechar o swipe externamente
const closeSwipe = () => {
    translateX.value = 0
}

defineExpose({ closeSwipe })
</script> 