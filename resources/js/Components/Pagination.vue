<template>
    <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 rounded-lg">
        <!-- Mobile -->
        <div class="flex flex-1 justify-between sm:hidden">
            <Link v-if="links.prev" 
                  :href="links.prev" 
                  class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Anterior
            </Link>
            <span v-else 
                  class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-400 dark:text-gray-500 cursor-not-allowed">
                Anterior
            </span>
            
            <Link v-if="links.next" 
                  :href="links.next" 
                  class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Próximo
            </Link>
            <span v-else 
                  class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-400 dark:text-gray-500 cursor-not-allowed">
                Próximo
            </span>
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
                    <Link v-if="links.prev" 
                          :href="links.prev" 
                          class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 dark:text-gray-500 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0">
                        <span class="sr-only">Anterior</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <span v-else 
                          class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-300 dark:text-gray-600 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 cursor-not-allowed">
                        <span class="sr-only">Anterior</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                        </svg>
                    </span>

                    <!-- Números das páginas -->
                    <template v-for="(link, index) in paginationLinks" :key="index">
                        <Link v-if="link.url && !link.active" 
                              :href="link.url" 
                              class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0">
                            {{ traduzirLabel(link.label) }}
                        </Link>
                        <span v-else-if="link.active" 
                              class="relative z-10 inline-flex items-center bg-blue-600 dark:bg-blue-700 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-700">
                            {{ traduzirLabel(link.label) }}
                        </span>
                        <span v-else 
                              class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-400 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800">
                            {{ traduzirLabel(link.label) }}
                        </span>
                    </template>

                    <!-- Próximo -->
                    <Link v-if="links.next" 
                          :href="links.next" 
                          class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 dark:text-gray-500 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0">
                        <span class="sr-only">Próximo</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </Link>
                    <span v-else 
                          class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-300 dark:text-gray-600 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white dark:bg-gray-800 cursor-not-allowed">
                        <span class="sr-only">Próximo</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </nav>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    links: Object,
})

const paginationLinks = computed(() => {
    return props.links.links || []
})

const links = computed(() => ({
    prev: props.links.prev_page_url,
    next: props.links.next_page_url,
}))

const from = computed(() => props.links.from || 0)
const to = computed(() => props.links.to || 0)
const total = computed(() => props.links.total || 0)

function traduzirLabel(label) {
    if (label === '&laquo; Previous') return 'Anterior'
    if (label === 'Next &raquo;') return 'Próxima'
    return label.replaceAll('&laquo;', '').replaceAll('&raquo;', '').trim()
}
</script> 