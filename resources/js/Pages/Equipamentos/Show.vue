<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Detalhes do Equipamento
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="mb-6">
              <button
                @click="$inertia.visit(route('equipamentos.index'))"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mb-4"
              >
                Voltar para lista
              </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ equipamento.equipment_tag }} - {{ equipamento.nome }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ equipamento.tipo }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Setor: {{ equipamento.setor?.nome || '-' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Marca: {{ equipamento.marca || '-' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Modelo: {{ equipamento.modelo || '-' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Número de Série: {{ equipamento.numero_serie || '-' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Status: {{ equipamento.status }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Ativo: <span :class="equipamento.ativo ? 'text-green-600' : 'text-red-600'">{{ equipamento.ativo ? 'Sim' : 'Não' }}</span></p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Data de Instalação: {{ equipamento.data_instalacao || '-' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Última Manutenção: {{ equipamento.ultima_manutencao || '-' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Próxima Manutenção: {{ equipamento.proxima_manutencao || '-' }}</p>
              </div>
              <div>
                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">Descrição</h4>
                <div class="text-gray-900 dark:text-gray-200 whitespace-pre-line">{{ equipamento.descricao || '-' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
const props = defineProps({
  equipamento: Object
})
</script> 