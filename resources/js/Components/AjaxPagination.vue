<template>
    <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 rounded-lg">
        <!-- Mobile -->
        <div class="flex flex-1 justify-between sm:hidden">
            <button
                @click="$emit('page-change', currentPage - 1)"
                :disabled="!hasPrevPage"
                class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Anterior
            </button>
            <button
                @click="$emit('page-change', currentPage + 1)"
                :disabled="!hasNextPage"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Próximo
            </button>
        </div>

        <!-- Desktop -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Mostrando
                    <span class="font-medium">{{ from }}</span>
                    a
                    <span class="font-medium">{{ to }}</span>
                    de
                    <span class="font-medium">{{ total }}</span>
                    resultados
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <!-- Anterior -->
                    <button
                        @click="$emit('page-change', currentPage - 1)"
                        :disabled="!hasPrevPage"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 dark:text-gray-500 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="sr-only">Anterior</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Números das páginas -->
                    <template v-for="page in visiblePages" :key="page">
                        <button
                            v-if="page !== '...'"
                            @click="$emit('page-change', page)"
                            :class="[
                                page === currentPage
                                    ? 'relative z-10 inline-flex items-center bg-blue-600 dark:bg-blue-700 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-700'
                                    : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0'
                            ]"
                        >
                            {{ page }}
                        </button>
                        <span
                            v-else
                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800"
                        >
                            ...
                        </span>
                    </template>

                    <!-- Próximo -->
                    <button
                        @click="$emit('page-change', currentPage + 1)"
                        :disabled="!hasNextPage"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 dark:text-gray-500 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="sr-only">Próximo</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    currentPage: {
        type: Number,
        required: true
    },
    lastPage: {
        type: Number,
        required: true
    },
    from: {
        type: Number,
        default: 0
    },
    to: {
        type: Number,
        default: 0
    },
    total: {
        type: Number,
        default: 0
    }
})

const emit = defineEmits(['page-change'])

const hasPrevPage = computed(() => props.currentPage > 1)
const hasNextPage = computed(() => props.currentPage < props.lastPage)

const visiblePages = computed(() => {
    const pages = []
    const current = props.currentPage
    const last = props.lastPage
    
    if (last <= 7) {
        // Se temos 7 páginas ou menos, mostrar todas
        for (let i = 1; i <= last; i++) {
            pages.push(i)
        }
    } else {
        // Sempre mostrar primeira página
        pages.push(1)
        
        if (current <= 4) {
            // Páginas 1, 2, 3, 4, 5, ..., last
            for (let i = 2; i <= 5; i++) {
                pages.push(i)
            }
            pages.push('...')
            pages.push(last)
        } else if (current >= last - 3) {
            // Páginas 1, ..., last-4, last-3, last-2, last-1, last
            pages.push('...')
            for (let i = last - 4; i <= last; i++) {
                pages.push(i)
            }
        } else {
            // Páginas 1, ..., current-1, current, current+1, ..., last
            pages.push('...')
            for (let i = current - 1; i <= current + 1; i++) {
                pages.push(i)
            }
            pages.push('...')
            pages.push(last)
        }
    }
    
    return pages
})
</script> 