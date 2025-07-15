<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Novo Setor
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nome -->
                <div class="md:col-span-2">
                  <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nome *
                  </label>
                  <input
                    id="nome"
                    v-model="form.nome"
                    type="text"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                    :class="{ 'border-red-500': form.errors.nome }"
                    required
                    placeholder="Nome do setor"
                  />
                  <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">
                    {{ form.errors.nome }}
                  </div>
                </div>
                <!-- Descrição -->
                <div class="md:col-span-2">
                  <label for="descricao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Descrição
                  </label>
                  <textarea
                    id="descricao"
                    v-model="form.descricao"
                    rows="3"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                    :class="{ 'border-red-500': form.errors.descricao }"
                    placeholder="Descrição do setor"
                  ></textarea>
                  <div v-if="form.errors.descricao" class="text-red-600 text-sm mt-1">
                    {{ form.errors.descricao }}
                  </div>
                </div>
                <!-- Ativo -->
                <div class="md:col-span-2">
                  <div class="flex items-center">
                    <input
                      id="ativo"
                      v-model="form.ativo"
                      type="checkbox"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    />
                    <label for="ativo" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                      Setor ativo
                    </label>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-4 mt-6">
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50"
                >
                  <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ form.processing ? 'Salvando...' : 'Salvar' }}
                </button>
                <Link
                  :href="route('setores.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                  Cancelar
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({
  nome: '',
  descricao: '',
  ativo: true,
})

const submit = () => {
  form.post(route('setores.store'))
}
</script> 