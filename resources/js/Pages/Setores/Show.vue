<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Detalhes do Setor
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Cabeçalho do Setor -->
            <div class="mb-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ setor.nome }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ setor.descricao || '-' }}</p>
                <div class="flex items-center gap-4">
                  <span :class="setor.ativo ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'" class="px-2 py-1 text-xs rounded-full">
                    {{ setor.ativo ? 'Ativo' : 'Inativo' }}
                  </span>
                  <span class="text-sm text-gray-600 dark:text-gray-400">
                    Total de equipamentos: {{ totalEquipamentos }}
                  </span>
                </div>
              </div>
              <div class="flex gap-2">
                <Link :href="route('setores.index')" class="inline-flex items-center px-3 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="Voltar">
                  <i class="fas fa-arrow-left mr-1"></i> Voltar
                </Link>
                <Link :href="route('setores.edit', setor.id)" class="inline-flex items-center px-3 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:bg-yellow-700 dark:focus:bg-yellow-600 active:bg-yellow-900 dark:active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" title="Editar">
                  <i class="fas fa-edit mr-1"></i> Editar
                </Link>
              </div>
            </div>

            <!-- Seção de Equipamentos -->
            <div>
              <div class="flex justify-between items-center mb-4">
                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100">Equipamentos do Setor</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                  Mostrando {{ equipamentos.data ? equipamentos.data.length : 0 }} de {{ totalEquipamentos }} equipamentos
                </div>
              </div>

              <!-- Filtros de Equipamentos -->
              <div class="mb-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar equipamento</label>
                    <input
                      v-model="filtros.busca"
                      @input="buscarEquipamentos"
                      type="text"
                      placeholder="TAG, nome, marca..."
                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select
                      v-model="filtros.status"
                      @change="buscarEquipamentos"
                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                    >
                      <option value="">Todos os status</option>
                      <option value="Operacional">Operacional</option>
                      <option value="Manutenção">Manutenção</option>
                      <option value="Inativo">Inativo</option>
                      <option value="Defeito">Defeito</option>
                    </select>
                  </div>
                  <div class="flex items-end">
                    <button
                      @click="limparFiltros"
                      class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                      <i class="fas fa-times mr-1"></i> Limpar Filtros
                    </button>
                  </div>
                </div>
              </div>

              <!-- Loading -->
              <div v-if="carregando" class="text-center py-8">
                <div class="inline-flex items-center text-gray-500 dark:text-gray-400">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Carregando equipamentos...
                </div>
              </div>

              <!-- Lista de Equipamentos -->
              <div v-else>
                <div v-if="equipamentos.data && equipamentos.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div v-for="equipamento in equipamentos.data" :key="equipamento.id" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 flex flex-col gap-2 hover:shadow-md transition-shadow duration-200">
                    <div class="flex justify-between items-center">
                      <div>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ equipamento.equipment_tag }}</span>
                        <span class="ml-2 text-gray-600 dark:text-gray-400">{{ equipamento.nome }}</span>
                      </div>
                      <span :class="getStatusClass(equipamento.status)" class="px-2 py-1 text-xs rounded-full">
                        {{ equipamento.status }}
                      </span>
                    </div>
                    <div class="flex justify-between items-center">
                      <span class="text-xs text-gray-500 dark:text-gray-400">Marca: {{ equipamento.marca || '-' }}</span>
                      <Link :href="route('equipamentos.show', equipamento.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Visualizar">
                        <i class="fas fa-eye"></i>
                      </Link>
                    </div>
                  </div>
                </div>
                
                <!-- Estado vazio -->
                <div v-else class="text-center py-8">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                  </svg>
                  <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ filtros.busca || filtros.status ? 'Nenhum equipamento encontrado' : 'Nenhum equipamento vinculado a este setor' }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ filtros.busca || filtros.status ? 'Tente ajustar os filtros de busca.' : 'Adicione equipamentos a este setor para visualizá-los aqui.' }}
                  </p>
                </div>

                <!-- Paginação -->
                <div v-if="equipamentos.data && equipamentos.data.length > 0" class="mt-6">
                  <AjaxPagination
                    :current-page="equipamentos.current_page"
                    :last-page="equipamentos.last_page"
                    :from="equipamentos.from"
                    :to="equipamentos.to"
                    :total="equipamentos.total"
                    @page-change="buscarEquipamentos"
                  />
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
import { ref, onMounted, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AjaxPagination from '@/Components/AjaxPagination.vue'
import axios from 'axios'

const props = defineProps({ 
  setor: Object,
  totalEquipamentos: Number
})

const equipamentos = ref({ data: [], current_page: 1, total: 0, from: 0, to: 0, links: [] })
const carregando = ref(false)
const filtros = ref({
  busca: '',
  status: ''
})

// Debounce para busca
let timeoutBusca = null

const buscarEquipamentos = async (page = 1) => {
  carregando.value = true
  try {
    const params = {
      page,
      ...filtros.value
    }
    
    const { data } = await axios.get(`/api/setores/${props.setor.id}/equipamentos`, { params })
    equipamentos.value = data
  } catch (error) {
    console.error('Erro ao carregar equipamentos:', error)
    equipamentos.value = { data: [], current_page: 1, total: 0, from: 0, to: 0, links: [] }
  } finally {
    carregando.value = false
  }
}



const limparFiltros = () => {
  filtros.value = { busca: '', status: '' }
  buscarEquipamentos(1)
}

// Debounce para busca
watch(() => filtros.value.busca, (novoValor) => {
  clearTimeout(timeoutBusca)
  timeoutBusca = setTimeout(() => {
    buscarEquipamentos(1)
  }, 500)
})

watch(() => filtros.value.status, () => {
  buscarEquipamentos(1)
})

function getStatusClass(status) {
  switch (status) {
    case 'Operacional': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
    case 'Manutenção': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
    case 'Inativo': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    case 'Defeito': return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }
}

onMounted(() => {
  buscarEquipamentos(1)
})
</script> 