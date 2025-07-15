<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Gerenciar Setor / Equipamentos
      </h2>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="mb-4 flex flex-col md:flex-row md:items-center gap-4">
              <input
                v-model="busca"
                @input="buscarEquipamentos"
                type="text"
                placeholder="Buscar por nome, TAG ou setor..."
                class="w-full md:w-1/3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              />
            </div>

            <div v-if="carregando" class="text-gray-500 dark:text-gray-400">Carregando equipamentos...</div>
            <div v-else>
              <div v-if="equipamentos.length === 0" class="text-gray-500 dark:text-gray-400">Nenhum equipamento encontrado.</div>
              <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="equip in equipamentos" :key="equip.id" class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4">
                  <div class="flex justify-between items-center mb-2">
                    <div>
                      <span class="font-semibold text-gray-900 dark:text-gray-100">{{ equip.tag }}</span>
                      <span class="ml-2 text-gray-600 dark:text-gray-400">{{ equip.nome }}</span>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">{{ equip.setor || '-' }}</span>
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">Área: {{ equip.area || '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const equipamentos = ref([])
const busca = ref('')
const carregando = ref(false)

const buscarEquipamentos = async () => {
  carregando.value = true
  try {
    const { data } = await axios.get('/api/equipamentos-geral', {
      params: { busca: busca.value }
    })
    equipamentos.value = data
  } catch (e) {
    equipamentos.value = []
  } finally {
    carregando.value = false
  }
}

onMounted(() => {
  buscarEquipamentos()
})
</script> 