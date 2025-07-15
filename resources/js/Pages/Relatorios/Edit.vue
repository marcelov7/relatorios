<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar Relatório
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Título -->
                                <div class="md:col-span-2">
                                    <label for="titulo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Título *
                                    </label>
                                    <input
                                        id="titulo"
                                        v-model="form.titulo"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.titulo }"
                                        required
                                        placeholder="Título do relatório"
                                    />
                                    <div v-if="form.errors.titulo" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.titulo }}
                                    </div>
                                </div>

                                <!-- Atividade -->
                                <div>
                                    <label for="activity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Atividade *
                                    </label>
                                    <select
                                        id="activity"
                                        v-model="form.activity"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.activity }"
                                        required
                                    >
                                        <option value="">Selecione uma atividade</option>
                                        <option value="Manutenção Preventiva">Manutenção Preventiva</option>
                                        <option value="Manutenção Corretiva">Manutenção Corretiva</option>
                                        <option value="Inspeção">Inspeção</option>
                                        <option value="Calibração">Calibração</option>
                                        <option value="Limpeza">Limpeza</option>
                                        <option value="Treinamento">Treinamento</option>
                                        <option value="Auditoria">Auditoria</option>
                                        <option value="Análise">Análise</option>
                                        <option value="Instalação">Instalação</option>
                                        <option value="Outros">Outros</option>
                                    </select>
                                    <div v-if="form.errors.activity" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.activity }}
                                    </div>
                                </div>

                                <!-- Nome do Responsável -->
                                <div>
                                    <label for="nome_responsavel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nome do Responsável *
                                    </label>
                                    <input
                                        id="nome_responsavel"
                                        v-model="form.nome_responsavel"
                                        type="text"
                                        readonly
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 shadow-sm cursor-not-allowed"
                                        required
                                        placeholder="Nome completo do responsável"
                                    />
                                    <div v-if="form.errors.nome_responsavel" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.nome_responsavel }}
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Campo bloqueado - definido no cadastro
                                    </p>
                                </div>

                                <!-- Cargo do Responsável -->
                                <div>
                                    <label for="cargo_responsavel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Cargo do Responsável
                                    </label>
                                    <input
                                        id="cargo_responsavel"
                                        v-model="form.cargo_responsavel"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.cargo_responsavel }"
                                        placeholder="Cargo/função do responsável"
                                    />
                                    <div v-if="form.errors.cargo_responsavel" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.cargo_responsavel }}
                                    </div>
                                </div>

                                <!-- Data e Hora de Criação -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Data e Hora de Criação *
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Data -->
                                        <div>
                                            <label for="date_created" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                Data
                                            </label>
                                            <input
                                                id="date_created"
                                                v-model="form.date_created"
                                                type="date"
                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                                :class="{ 'border-red-500': form.errors.date_created }"
                                                required
                                            />
                                            <div v-if="form.errors.date_created" class="text-red-600 text-sm mt-1">
                                                {{ form.errors.date_created }}
                                            </div>
                                        </div>
                                        
                                        <!-- Hora -->
                                        <div>
                                            <label for="time_created" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                Hora
                                            </label>
                                            <div class="relative">
                                                <input
                                                    id="time_created"
                                                    v-model="form.time_created"
                                                    type="time"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm pr-10"
                                                    :class="{ 'border-red-500': form.errors.time_created }"
                                                />
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div v-if="form.errors.time_created" class="text-red-600 text-sm mt-1">
                                                {{ form.errors.time_created }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Equipamentos de Teste -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Equipamentos
                                    </label>
                                    <div class="mb-2 flex gap-2 items-center relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </span>
                                        <input
                                            v-model="buscaEquipamento"
                                            @input="buscarEquipamentosTeste"
                                            type="text"
                                            placeholder="Buscar por nome, TAG ou setor..."
                                            class="w-full pl-10 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        />
                                    </div>
                                    <div v-if="carregandoEquipamentosTeste" class="text-sm text-gray-500 dark:text-gray-400">Carregando equipamentos...</div>
                                    <div v-else-if="equipamentosTeste.length === 0" class="text-sm text-gray-500 dark:text-gray-400">Nenhum equipamento encontrado.</div>
                                    <div v-else class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-700">
                                        <div
                                            v-for="equip in equipamentosTeste"
                                            :key="equip.id"
                                            class="flex items-center space-x-3 p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors duration-150"
                                        >
                                            <input
                                                :id="`equipamento-teste-${equip.id}`"
                                                v-model="form.equipment_test_ids"
                                                :value="equip.id"
                                                type="checkbox"
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded"
                                            />
                                            <label
                                                :for="`equipamento-teste-${equip.id}`"
                                                class="flex-1 cursor-pointer"
                                            >
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ equip.tag }}
                                                        </span>
                                                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">
                                                            {{ equip.nome }}
                                                        </span>
                                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300 ml-2">
                                                            {{ equip.setor }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs px-2 py-1 rounded-full"
                                                          :class="equip.status === 'Operacional' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : (equip.status === 'Manutenção' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400')">
                                                        {{ equip.status }}
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="form.equipment_test_ids.length > 0" class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipamentos Selecionados:</label>
                                        <div class="flex flex-wrap gap-2">
                                            <template v-for="equip in equipamentosSelecionados" :key="equip.id">
                                                <span v-if="equip" class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold">
                                                    <span class="font-bold mr-2">{{ equip.tag }}</span>
                                                    <span class="px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ml-1">{{ equip.setor }}</span>
                                                    <button type="button" @click="removerEquipamentoSelecionado(equip.id)" class="ml-2 text-red-500 hover:text-red-700 dark:hover:text-red-300 focus:outline-none">
                                                        &times;
                                                    </button>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.equipment_test_ids" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.equipment_test_ids }}
                                    </div>
                                </div>

                                <!-- Progresso -->
                                <div class="md:col-span-2">
                                    <label for="progresso" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Progresso: {{ form.progresso }}%
                                    </label>
                                    <div class="space-y-3">
                                        <!-- Slider -->
                                        <input
                                            id="progresso"
                                            v-model="form.progresso"
                                            type="range"
                                            min="0"
                                            max="100"
                                            step="1"
                                            class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer slider"
                                            @input="atualizarStatusPorProgresso"
                                        />
                                        
                                        <!-- Barra de progresso visual -->
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                            <div 
                                                class="h-full rounded-full transition-all duration-300 ease-out"
                                                :class="getProgressBarClass(form.progresso)"
                                                :style="{ width: form.progresso + '%' }"
                                            ></div>
                                        </div>
                                        
                                        <!-- Marcadores de progresso -->
                                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                            <span>0%</span>
                                            <span>25%</span>
                                            <span>50%</span>
                                            <span>75%</span>
                                            <span>100%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status (Dinâmico) -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Status
                                    </label>
                                    <div class="relative">
                                        <input
                                            id="status"
                                            v-model="form.status"
                                            type="text"
                                            readonly
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            :class="getStatusInputClass(form.status)"
                                        />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <div class="w-3 h-3 rounded-full transition-colors duration-300"
                                                 :class="getStatusIndicatorClass(form.status)">
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Status atualizado automaticamente baseado no progresso
                                    </p>
                                </div>

                                <!-- Detalhes -->
                                <div class="md:col-span-2">
                                    <label for="detalhes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Detalhes *
                                    </label>
                                    <input id="detalhes" type="hidden" v-model="form.detalhes" name="detalhes">
                                    <trix-editor input="detalhes"></trix-editor>
                                    <div v-if="form.errors.detalhes" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.detalhes }}
                                    </div>
                                </div>

                                <!-- Imagens Existentes -->
                                <div v-if="existingImages.length > 0" class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Imagens Atuais
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                        <div
                                            v-for="(image, index) in existingImages"
                                            :key="index"
                                            class="relative group"
                                        >
                                            <img
                                                :src="`/storage/${image.path}`"
                                                :alt="image.original_name"
                                                class="w-full h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                                            />
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                <button
                                                    type="button"
                                                    @click="removeExistingImage(index)"
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-red-500 hover:bg-red-600 text-white rounded-full p-2"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                {{ image.original_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload de Novas Imagens -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Adicionar Novas Imagens
                                    </label>
                                    
                                    <!-- Área de Upload -->
                                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200">
                                        <input
                                            ref="fileInput"
                                            type="file"
                                            multiple
                                            accept="image/*"
                                            class="hidden"
                                            @change="handleFileUpload"
                                        />
                                        <div class="space-y-2">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                <button
                                                    type="button"
                                                    @click="$refs.fileInput.click()"
                                                    class="font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500"
                                                >
                                                    Clique para adicionar imagens
                                                </button>
                                                ou arraste e solte aqui
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                PNG, JPG, GIF até 10MB cada
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Preview das Novas Imagens -->
                                    <div v-if="imagePreviews.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div
                                            v-for="(preview, index) in imagePreviews"
                                            :key="index"
                                            class="relative group"
                                        >
                                            <img
                                                :src="preview.url"
                                                :alt="preview.name"
                                                class="w-full h-24 object-cover rounded-lg border border-gray-300 dark:border-gray-600"
                                            />
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                <button
                                                    type="button"
                                                    @click="removeNewImage(index)"
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-red-500 hover:bg-red-600 text-white rounded-full p-2"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                {{ preview.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mt-6">
                                <button
                                    type="submit"
                                    :disabled="isSubmitting"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ isSubmitting ? 'Atualizando...' : 'Atualizar Relatório' }}
                                </button>
                                <Link
                                    :href="route('relatorios.index')"
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
import { ref, onMounted, watch, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useNotifications } from '@/composables/useNotifications'
import axios from 'axios'

const { success, error } = useNotifications()

const props = defineProps({
    relatorio: Object,
    locais: Array,
    equipamentos: Array,
    setores: Array,
    equipamentosTeste: {
        type: Array,
        default: () => [],
    },
})

const equipamentosTeste = props.equipamentosTeste || []

console.log('relatorio recebido:', props.relatorio)

// Função para formatar data - versão melhorada
function formatDateForInput(dateString) {
    if (!dateString) return new Date().toISOString().split('T')[0]
    
    // Se já está no formato Y-m-d, retorna como está
    if (dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
        return dateString
    }
    
    // Remove qualquer 'T' e parte de tempo se existir
    const cleanDate = dateString.split('T')[0]
    
    // Se após limpeza está no formato correto
    if (cleanDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
        return cleanDate
    }
    
    // Tenta converter para Date e formatar
    try {
        const date = new Date(dateString)
        
        if (isNaN(date.getTime())) {
            return new Date().toISOString().split('T')[0]
        }
        
        // Formatar para Y-m-d
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')
        
        return `${year}-${month}-${day}`
    } catch (error) {
        return new Date().toISOString().split('T')[0]
    }
}

// Refs reativas
const equipamentosDisponiveis = ref(props.equipamentos || [])
const carregandoEquipamentos = ref(false)
const imagePreviews = ref([])
const selectedFiles = ref([])
const existingImages = ref([...(props.relatorio.images || [])])
const isSubmitting = ref(false)

// Form
const form = useForm({
    titulo: props.relatorio.titulo || '',
    activity: props.relatorio.activity || '',
    nome_responsavel: props.relatorio.nome_responsavel || (props.relatorio.autor ? props.relatorio.autor.name : ''),
    cargo_responsavel: props.relatorio.cargo_responsavel || (props.relatorio.autor ? props.relatorio.autor.cargo : ''),
    date_created: formatDateForInput(props.relatorio.date_created),
    time_created: props.relatorio.time_created || '', // Adicionado campo de hora
    setor_id: props.relatorio.setor_id ? String(props.relatorio.setor_id) : '',
    equipment_test_ids: (props.equipamentosTeste || []).map(e => e.id),
    status: props.relatorio.status || 'Aberta',
    progresso: props.relatorio.progresso || 0,
    detalhes: props.relatorio.detalhes || '',
    images: [],
    keep_images: []
})

onMounted(() => {
    // Força o Trix a atualizar o conteúdo se necessário
    const trix = document.querySelector('trix-editor')
    if (trix && form.detalhes) {
        trix.editor.loadHTML(form.detalhes)
    }
})


// Funções para status
function getStatusInputClass(status) {
    const classes = {
        'Aberta': 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-300',
        'Em Andamento': 'border-yellow-300 dark:border-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-900 dark:text-yellow-300',
        'Concluída': 'border-green-300 dark:border-green-600 bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-300',
        'Cancelada': 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/20 text-gray-900 dark:text-gray-300'
    }
    return classes[status] || 'border-gray-300 dark:border-gray-600'
}

function getStatusIndicatorClass(status) {
    const classes = {
        'Aberta': 'bg-red-500',
        'Em Andamento': 'bg-yellow-500',
        'Concluída': 'bg-green-500',
        'Cancelada': 'bg-gray-500'
    }
    return classes[status] || 'bg-gray-500'
}

function getProgressBarClass(progresso) {
    if (progresso >= 100) {
        return 'bg-green-500'
    } else if (progresso >= 75) {
        return 'bg-blue-500'
    } else if (progresso >= 50) {
        return 'bg-yellow-500'
    } else if (progresso >= 25) {
        return 'bg-orange-500'
    } else {
        return 'bg-red-500'
    }
}

function atualizarStatusPorProgresso() {
    const progresso = parseInt(form.progresso)
    if (progresso === 0) {
        form.status = 'Aberta'
    } else if (progresso >= 1 && progresso <= 99) {
        form.status = 'Em Andamento'
    } else if (progresso === 100) {
        form.status = 'Concluída'
    }
}

// Funções para equipamentos
async function carregarEquipamentos() {
    if (!form.setor_id) {
        equipamentosDisponiveis.value = []
        form.equipment_test_ids = []
        return
    }

    carregandoEquipamentos.value = true
    try {
        const response = await axios.get(`/api/equipamentos-por-setor?setor_id=${form.setor_id}`)
        equipamentosDisponiveis.value = response.data
        
        // Manter apenas equipamentos que ainda existem no setor selecionado
        const equipamentosExistentes = response.data.map(eq => eq.id)
        form.equipment_test_ids = form.equipment_test_ids.filter(id => equipamentosExistentes.includes(id))
    } catch (err) {
        console.error('Erro ao carregar equipamentos:', err)
        error('Erro ao carregar equipamentos')
        equipamentosDisponiveis.value = []
    } finally {
        carregandoEquipamentos.value = false
    }
}

// Funções para imagens
function removeExistingImage(index) {
    existingImages.value.splice(index, 1)
}

function handleFileUpload(event) {
    const files = Array.from(event.target.files)
    
    files.forEach(file => {
        if (file.type.startsWith('image/')) {
            if (file.size > 10 * 1024 * 1024) { // 10MB
                error(`Arquivo ${file.name} é muito grande. Máximo 10MB.`)
                return
            }
            
            const reader = new FileReader()
            reader.onload = (e) => {
                imagePreviews.value.push({
                    url: e.target.result,
                    name: file.name,
                    file: file
                })
                selectedFiles.value.push(file)
            }
            reader.readAsDataURL(file)
        } else {
            error(`Arquivo ${file.name} não é uma imagem válida.`)
        }
    })
    
    // Limpar o input
    event.target.value = ''
}

function removeNewImage(index) {
    imagePreviews.value.splice(index, 1)
    selectedFiles.value.splice(index, 1)
}

// Função de submit - usando axios com notificações elegantes
function submit() {
    // Prevenir múltiplos envios
    if (isSubmitting.value) return
    
    // Validações básicas
    if (!form.titulo.trim()) {
        error('O campo Título é obrigatório')
        return
    }
    
    if (!form.activity.trim()) {
        error('O campo Atividade é obrigatório')
        return
    }
    
    if (!form.nome_responsavel.trim()) {
        error('O campo Nome do Responsável é obrigatório')
        return
    }
    
    if (!form.date_created.trim()) {
        error('O campo Data de Criação é obrigatório')
        return
    }
    
    if (!form.time_created.trim()) {
        error('O campo Hora de Criação é obrigatório')
        return
    }
    
    if (!form.detalhes.trim()) {
        error('O campo Detalhes é obrigatório')
        return
    }

    // Garantir que a data está no formato correto
    const formattedDate = formatDateForInput(form.date_created)
    if (!formattedDate) {
        error('Erro na formatação da data. Verifique se a data é válida.')
        return
    }
    
    // Validação final da data
    if (!formattedDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
        error('Data deve estar no formato AAAA-MM-DD, mas está: ' + formattedDate)
        return
    }

    // Atualizar a data formatada
    form.date_created = formattedDate

    // Adicionar arquivos ao form
    form.images = selectedFiles.value
    
    // Adicionar índices das imagens existentes que devem ser mantidas
    form.keep_images = existingImages.value.map((_, index) => index)
    
    // Marcar como enviando
    isSubmitting.value = true
    
    // Criar FormData para envio correto com arquivos
    const formData = new FormData()
    formData.append('_method', 'PUT')
    formData.append('titulo', form.titulo)
    formData.append('activity', form.activity)
    formData.append('nome_responsavel', form.nome_responsavel)
    formData.append('cargo_responsavel', form.cargo_responsavel || '')
    formData.append('date_created', form.date_created)
    formData.append('time_created', form.time_created) // Adicionado campo de hora
    formData.append('setor_id', form.setor_id || '')
    formData.append('status', form.status)
    formData.append('progresso', form.progresso)
    formData.append('detalhes', form.detalhes)
    
    // Adicionar equipment_test_ids
    form.equipment_test_ids.forEach((id, index) => {
        formData.append(`equipment_test_ids[${index}]`, id)
    })
    
    // Adicionar keep_images
    form.keep_images.forEach((index, i) => {
        formData.append(`keep_images[${i}]`, index)
    })
    
    // Adicionar images
    form.images.forEach((file, index) => {
        formData.append(`images[${index}]`, file)
    })

    // Enviar via axios mas com notificações elegantes
    axios.post(route('relatorios.update', { relatorio: props.relatorio.id }), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        }
    })
        .then(response => {
            success('Relatório atualizado com sucesso! As alterações foram salvas e estão disponíveis para visualização.')
            // Aguardar um pouco para a notificação aparecer antes do redirect
            setTimeout(() => {
                window.location.href = route('relatorios.show', { relatorio: props.relatorio.id })
            }, 1500)
        })
        .catch(error => {
            console.error('Erro ao atualizar relatorio:', error.response?.data)
            if (error.response?.data?.errors) {
                // Mostrar erros de validação específicos
                const errors = error.response.data.errors
                Object.keys(errors).forEach(field => {
                    errors[field].forEach(message => {
                        error(`${field}: ${message}`)
                    })
                })
            } else {
                error('Erro ao atualizar relatório. Verifique os campos obrigatórios e tente novamente.')
            }
        })
        .finally(() => {
            // Sempre resetar o estado de loading
            isSubmitting.value = false
        })
}


</script>