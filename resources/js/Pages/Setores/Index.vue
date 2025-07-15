<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Gerenciar Setores
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Setores</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Gerencie os setores e seus equipamentos</p>
          </div>
          <Link
            :href="route('setores.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Novo Setor
          </Link>
        </div>

        <!-- Filtro de busca -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
          <form @submit.prevent="aplicarBusca" class="flex gap-4">
            <input
              v-model="busca"
              type="text"
              placeholder="Buscar setores..."
              class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
            />
            <button
              type="submit"
              class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              Buscar
            </button>
          </form>
        </div>

        <!-- Grid de setores -->
        <div v-if="setores.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="setor in setores.data"
            :key="setor.id"
            class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ setor.nome }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ setor.descricao || '-' }}</p>
              </div>
              <span :class="setor.ativo ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 py-1 text-xs rounded-full">
                {{ setor.ativo ? 'Ativo' : 'Inativo' }}
              </span>
            </div>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs text-gray-500 dark:text-gray-400">Equipamentos: {{ setor.equipamentos_count }}</span>
              <div class="flex gap-2">
                <Link :href="route('setores.show', setor.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 flex items-center" title="Visualizar">
                  <i class="fas fa-eye mr-1"></i> Visualizar
                </Link>
                <Link :href="route('setores.edit', setor.id)" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 flex items-center font-semibold" title="Editar">
                  <i class="fas fa-edit mr-1"></i> Editar
                </Link>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
          </svg>
          <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum setor encontrado</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ busca ? 'Tente ajustar a busca ou' : 'Comece' }} criando um novo setor.
          </p>
          <div class="mt-6">
            <Link
              :href="route('setores.create')"
              class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Novo Setor
            </Link>
          </div>
        </div>
        <!-- Paginação -->
        <div v-if="setores.data.length > 0" class="mt-6">
          <Pagination :links="setores" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  setores: Object,
  filtros: Object
})

const busca = ref(props.filtros.busca || '')

function aplicarBusca() {
  router.get(route('setores.index'), { busca: busca.value }, { preserveState: true, replace: true })
}
</script> 