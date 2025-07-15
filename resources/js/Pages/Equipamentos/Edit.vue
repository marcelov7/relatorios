<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar Equipamento
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Equipment Tag -->
                                <div>
                                    <label for="equipment_tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        TAG do Equipamento *
                                    </label>
                                    <input
                                        id="equipment_tag"
                                        v-model="form.equipment_tag"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.equipment_tag }"
                                        required
                                        placeholder="Ex: EQ-001"
                                    />
                                    <div v-if="form.errors.equipment_tag" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.equipment_tag }}
                                    </div>
                                </div>

                                <!-- Nome -->
                                <div>
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
                                        placeholder="Nome do equipamento"
                                    />
                                    <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.nome }}
                                    </div>
                                </div>

                                <!-- Setor -->
                                <div>
                                    <label for="setor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Setor *
                                    </label>
                                    <select
                                        id="setor_id"
                                        v-model="form.setor_id"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.setor_id }"
                                        required
                                    >
                                        <option value="">Selecione um setor</option>
                                        <option v-for="setor in setores" :key="setor.id" :value="setor.id">
                                            {{ setor.nome }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.setor_id" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.setor_id }}
                                    </div>
                                </div>

                                <!-- Tipo -->
                                <div>
                                    <label for="tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tipo
                                    </label>
                                    <select
                                        id="tipo"
                                        v-model="form.tipo"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.tipo }"
                                    >
                                        <option value="">Selecione um tipo</option>
                                        <option v-for="tipo in tipos" :key="tipo" :value="tipo">
                                            {{ tipo }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.tipo" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.tipo }}
                                    </div>
                                </div>

                                <!-- Marca -->
                                <div>
                                    <label for="marca" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Marca
                                    </label>
                                    <input
                                        id="marca"
                                        v-model="form.marca"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.marca }"
                                        placeholder="Marca do equipamento"
                                    />
                                    <div v-if="form.errors.marca" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.marca }}
                                    </div>
                                </div>

                                <!-- Modelo -->
                                <div>
                                    <label for="modelo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Modelo
                                    </label>
                                    <input
                                        id="modelo"
                                        v-model="form.modelo"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.modelo }"
                                        placeholder="Modelo do equipamento"
                                    />
                                    <div v-if="form.errors.modelo" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.modelo }}
                                    </div>
                                </div>

                                <!-- Número de Série -->
                                <div>
                                    <label for="numero_serie" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Número de Série
                                    </label>
                                    <input
                                        id="numero_serie"
                                        v-model="form.numero_serie"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.numero_serie }"
                                        placeholder="Número de série"
                                    />
                                    <div v-if="form.errors.numero_serie" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.numero_serie }}
                                    </div>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Status *
                                    </label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.status }"
                                        required
                                    >
                                        <option value="">Selecione um status</option>
                                        <option value="Operacional">Operacional</option>
                                        <option value="Manutenção">Manutenção</option>
                                        <option value="Inativo">Inativo</option>
                                        <option value="Defeito">Defeito</option>
                                    </select>
                                    <div v-if="form.errors.status" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.status }}
                                    </div>
                                </div>

                                <!-- Data de Instalação -->
                                <div>
                                    <label for="data_instalacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Data de Instalação
                                    </label>
                                    <input
                                        id="data_instalacao"
                                        v-model="form.data_instalacao"
                                        type="date"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.data_instalacao }"
                                    />
                                    <div v-if="form.errors.data_instalacao" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.data_instalacao }}
                                    </div>
                                </div>

                                <!-- Última Manutenção -->
                                <div>
                                    <label for="ultima_manutencao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Última Manutenção
                                    </label>
                                    <input
                                        id="ultima_manutencao"
                                        v-model="form.ultima_manutencao"
                                        type="date"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.ultima_manutencao }"
                                    />
                                    <div v-if="form.errors.ultima_manutencao" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.ultima_manutencao }}
                                    </div>
                                </div>

                                <!-- Próxima Manutenção -->
                                <div>
                                    <label for="proxima_manutencao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Próxima Manutenção
                                    </label>
                                    <input
                                        id="proxima_manutencao"
                                        v-model="form.proxima_manutencao"
                                        type="date"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.proxima_manutencao }"
                                    />
                                    <div v-if="form.errors.proxima_manutencao" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.proxima_manutencao }}
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
                                        rows="4"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.descricao }"
                                        placeholder="Descrição detalhada do equipamento..."
                                    ></textarea>
                                    <div v-if="form.errors.descricao" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.descricao }}
                                    </div>
                                </div>

                                <!-- Status Ativo -->
                                <div class="md:col-span-2">
                                    <div class="flex items-center">
                                        <input
                                            id="ativo"
                                            v-model="form.ativo"
                                            type="checkbox"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <label for="ativo" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                            Equipamento ativo
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Equipamentos inativos não aparecerão nas listas de seleção
                                    </p>
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
                                    {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                                </button>
                                <Link
                                    :href="route('equipamentos.index')"
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
import { ref, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useNotifications } from '@/composables/useNotifications'

const { success, error } = useNotifications()

const props = defineProps({
    equipamento: Object,
    tipos: Array,
})

const setores = ref([])

const form = useForm({
    equipment_tag: props.equipamento.equipment_tag || '',
    nome: props.equipamento.nome || '',
    setor_id: props.equipamento.setor_id || '', // novo campo obrigatório
    tipo: props.equipamento.tipo || '',
    marca: props.equipamento.marca || '',
    modelo: props.equipamento.modelo || '',
    numero_serie: props.equipamento.numero_serie || '',
    status: props.equipamento.status || '',
    data_instalacao: props.equipamento.data_instalacao || '',
    ultima_manutencao: props.equipamento.ultima_manutencao || '',
    proxima_manutencao: props.equipamento.proxima_manutencao || '',
    ativo: props.equipamento.ativo ?? true,
})

onMounted(async () => {
    // Buscar setores ativos para o select
    const res = await fetch(route('api.setores.ativos'))
    setores.value = await res.json()
})

const submit = () => {
    form.put(route('equipamentos.update', props.equipamento.id), {
        onSuccess: () => success('Equipamento atualizado com sucesso!'),
        onError: () => error('Erro ao atualizar equipamento. Verifique os campos obrigatórios.')
    })
}
</script> 