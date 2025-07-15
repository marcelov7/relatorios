<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Equipamentos de Teste
      </h2>
    </template>

    <div class="py-8">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Busca global -->
            <div class="mb-4 flex flex-col md:flex-row md:items-center gap-4">
              <input
                v-model="busca"
                @input="buscarEquipamentos"
                type="text"
                placeholder="Buscar por nome, TAG ou setor..."
                class="w-full md:w-1/3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
              />
              <button @click="abrirModal()" class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md font-semibold text-xs uppercase hover:bg-blue-700 dark:hover:bg-blue-600 transition">Novo Equipamento</button>
              <button @click="exportarCsv" class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md font-semibold text-xs uppercase hover:bg-green-700 dark:hover:bg-green-600 transition">Exportar CSV</button>
              <label class="px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white rounded-md font-semibold text-xs uppercase hover:bg-gray-700 dark:hover:bg-gray-600 transition cursor-pointer">
                Importar CSV
                <input type="file" accept=".csv" @change="importarCsv" class="hidden" />
              </label>
            </div>

            <!-- Listagem -->
            <div v-if="carregando" class="text-gray-500 dark:text-gray-400">Carregando equipamentos...</div>
            <div v-else>
              <div v-if="equipamentos.data.length === 0" class="text-gray-500 dark:text-gray-400">Nenhum equipamento encontrado.</div>
              <form @submit.prevent>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-for="equip in equipamentos.data" :key="equip.id"
                       class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4 flex flex-col justify-between h-full min-h-[120px]">
                    <div>
                      <div class="flex justify-between items-center mb-1">
                        <div>
                          <span class="font-semibold text-gray-900 dark:text-gray-100">{{ equip.tag }}</span>
                          <span class="ml-2 text-gray-600 dark:text-gray-400">{{ equip.nome }}</span>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">{{ equip.setor }}</span>
                      </div>
                      <div class="flex items-center gap-2 text-xs mt-1">
                        <span :class="statusClass(equip.status)">{{ equip.status }}</span>
                      </div>
                    </div>
                    <div class="flex flex-row gap-4 items-center justify-end mt-4">
                      <button type="button" @click="abrirModal(equip)" class="p-2 rounded hover:bg-yellow-100 dark:hover:bg-yellow-900/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L5 12.828a2 2 0 010-2.828L9 13z" />
                        </svg>
                      </button>
                      <button type="button" @click="confirmarExclusao(equip)" class="p-2 rounded hover:bg-red-100 dark:hover:bg-red-900/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="flex flex-wrap gap-2 justify-center mt-6" v-if="equipamentos.last_page > 1">
              <button @click="buscarEquipamentos(equipamentos.current_page - 1)" :disabled="equipamentos.current_page === 1"
                class="px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 disabled:opacity-50">Anterior</button>
              <button v-for="page in equipamentos.last_page" :key="page"
                @click="buscarEquipamentos(page)"
                :class="[
                  'px-3 py-1 rounded',
                  equipamentos.current_page === page ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
                  'transition'
                ]"
                :disabled="equipamentos.current_page === page">
                {{ page }}
              </button>
              <button @click="buscarEquipamentos(equipamentos.current_page + 1)" :disabled="equipamentos.current_page === equipamentos.last_page"
                class="px-3 py-1 rounded bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 disabled:opacity-50">Próxima</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal de cadastro/edição -->
      <div v-if="modalAberto" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 w-full max-w-md relative">
          <button @click="fecharModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-900 dark:hover:text-white"><i class="fas fa-times"></i></button>
          <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">{{ editando ? 'Editar' : 'Novo' }} Equipamento</h3>
          <form @submit.prevent="salvarEquipamento">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">TAG</label>
              <input v-model="form.tag" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome</label>
              <input v-model="form.nome" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Setor</label>
              <input v-model="form.setor" type="text" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required />
            </div>
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
              <select v-model="form.status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" required>
                <option value="Operacional">Operacional</option>
                <option value="Manutenção">Manutenção</option>
                <option value="Inativo">Inativo</option>
              </select>
            </div>
            <div class="flex items-center gap-4">
              <button type="submit" class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md font-semibold text-xs uppercase hover:bg-blue-700 dark:hover:bg-blue-600 transition">Salvar</button>
              <button type="button" @click="fecharModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md font-semibold text-xs uppercase hover:bg-gray-300 dark:hover:bg-gray-600 transition">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const equipamentos = ref({ data: [], current_page: 1, last_page: 1, total: 0 })
const busca = ref('')
const carregando = ref(false)
const selecionados = ref([])

const modalAberto = ref(false)
const editando = ref(false)
const form = reactive({ id: null, tag: '', nome: '', setor: '', status: 'Operacional' })

const buscarEquipamentos = async (page = 1) => {
  carregando.value = true
  try {
    const { data } = await axios.get('/equipamento-tests-list', {
      params: { busca: busca.value, page }
    })
    equipamentos.value = data
  } catch (e) {
    equipamentos.value = { data: [], current_page: 1, last_page: 1, total: 0 }
  } finally {
    carregando.value = false
  }
}

const abrirModal = (equip = null) => {
  if (equip) {
    form.id = equip.id
    form.tag = equip.tag
    form.nome = equip.nome
    form.setor = equip.setor
    form.status = equip.status
    editando.value = true
  } else {
    form.id = null
    form.tag = ''
    form.nome = ''
    form.setor = ''
    form.status = 'Operacional'
    editando.value = false
  }
  modalAberto.value = true
}

const fecharModal = () => {
  modalAberto.value = false
}

const salvarEquipamento = async () => {
  try {
    if (editando.value) {
      await axios.put(`/equipamento-tests/${form.id}`, form)
    } else {
      await axios.post('/equipamento-tests', form)
    }
    buscarEquipamentos()
    fecharModal()
  } catch (e) {
    alert('Erro ao salvar equipamento. Verifique os campos.')
  }
}

const confirmarExclusao = async (equip) => {
  if (confirm('Tem certeza que deseja excluir este equipamento?')) {
    try {
      await axios.delete(`/equipamento-tests/${equip.id}`)
      buscarEquipamentos()
    } catch (e) {
      alert('Erro ao excluir equipamento.')
    }
  }
}

const statusClass = (status) => {
  switch (status) {
    case 'Operacional': return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 px-2 py-1 rounded';
    case 'Manutenção': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400 px-2 py-1 rounded';
    case 'Inativo': return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400 px-2 py-1 rounded';
    default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400 px-2 py-1 rounded';
  }
}

const exportarCsv = () => {
  window.open('/equipamento-tests-export', '_blank')
}

const importarCsv = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  const formData = new FormData()
  formData.append('file', file)
  try {
    await axios.post('/equipamento-tests-import', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
    alert('Importação concluída com sucesso!')
    buscarEquipamentos()
  } catch (e) {
    alert('Erro ao importar CSV. Verifique o arquivo.')
  }
  event.target.value = ''
}

onMounted(() => {
  buscarEquipamentos()
})
</script> 