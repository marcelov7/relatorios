<template>
    <Head title="Editar Motor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Motor') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Cabeçalho -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Editar Motor: {{ motor.tag }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Atualize os dados do motor
                                </p>
                            </div>
                            <Link :href="route('motores.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Voltar
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Informações Básicas -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                    Informações Básicas
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- TAG -->
                                    <div>
                                        <InputLabel for="tag" value="TAG *" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="tag"
                                            v-model="form.tag"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.tag" />
                                    </div>

                                    <!-- Equipamento -->
                                    <div>
                                        <InputLabel for="equipamento" value="Equipamento *" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="equipamento"
                                            v-model="form.equipamento"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.equipamento" />
                                    </div>

                                    <!-- Carcaça -->
                                    <div>
                                        <InputLabel for="carcaca" value="Carcaça (Fabricante)" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="carcaca"
                                            v-model="form.carcaca"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.carcaca" />
                                    </div>

                                    <!-- Fabricante -->
                                    <div>
                                        <InputLabel for="fabricante" value="Fabricante" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="fabricante"
                                            v-model="form.fabricante"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.fabricante" />
                                    </div>
                                </div>
                            </div>

                            <!-- Especificações Técnicas -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                    Especificações Técnicas
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <!-- Potência KW -->
                                    <div>
                                        <InputLabel for="potencia_kw" value="Potência (KW)" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="potencia_kw"
                                            v-model="form.potencia_kw"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.potencia_kw" />
                                    </div>

                                    <!-- Potência CV -->
                                    <div>
                                        <InputLabel for="potencia_cv" value="Potência (CV)" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="potencia_cv"
                                            v-model="form.potencia_cv"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.potencia_cv" />
                                    </div>

                                    <!-- Rotação -->
                                    <div>
                                        <InputLabel for="rotacao" value="Rotação (RPM)" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="rotacao"
                                            v-model="form.rotacao"
                                            type="number"
                                            min="0"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.rotacao" />
                                    </div>

                                    <!-- Corrente de Placa -->
                                    <div>
                                        <InputLabel for="corrente_placa" value="Corrente de Placa (A)" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="corrente_placa"
                                            v-model="form.corrente_placa"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.corrente_placa" />
                                    </div>

                                    <!-- Corrente Configurada -->
                                    <div>
                                        <InputLabel for="corrente_configurada" value="Corrente Configurada (A)" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="corrente_configurada"
                                            v-model="form.corrente_configurada"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.corrente_configurada" />
                                    </div>

                                    <!-- Tipo de Equipamento -->
                                    <div>
                                        <InputLabel for="tipo_equipamento" value="Tipo de Equipamento/Modelo" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="tipo_equipamento"
                                            v-model="form.tipo_equipamento"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.tipo_equipamento" />
                                    </div>
                                </div>
                            </div>

                            <!-- Localização e Status -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                    Localização e Status
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <!-- Local -->
                                    <div>
                                        <InputLabel for="local_id" value="Local *" class="text-gray-700 dark:text-gray-300" />
                                        <select
                                            id="local_id"
                                            v-model="form.local_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="">Selecione um local</option>
                                            <option v-for="local in locais" :key="local.id" :value="local.id">
                                                {{ local.nome }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.local_id" />
                                    </div>

                                    <!-- Armazenamento -->
                                    <div>
                                        <InputLabel for="armazenamento" value="Armazenamento *" class="text-gray-700 dark:text-gray-300" />
                                        <select
                                            id="armazenamento"
                                            v-model="form.armazenamento"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="">Selecione o status</option>
                                            <option value="Instalado">Instalado</option>
                                            <option value="Almoxarifado">Almoxarifado</option>
                                            <option value="Manutenção">Manutenção</option>
                                            <option value="Descartado">Descartado</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.armazenamento" />
                                    </div>

                                    <!-- Reserva Almox -->
                                    <div class="flex items-center">
                                        <Checkbox
                                            id="reserva_almox"
                                            v-model:checked="form.reserva_almox"
                                            name="reserva_almox"
                                        />
                                        <InputLabel for="reserva_almox" value="Reserva no Almoxarifado" class="ml-2 text-gray-700 dark:text-gray-300" />
                                    </div>
                                </div>
                            </div>

                            <!-- Foto -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                    Foto do Motor
                                </h4>
                                <div>
                                    <InputLabel for="foto" value="Foto" class="text-gray-700 dark:text-gray-300" />
                                    <input
                                        id="foto"
                                        ref="fotoInput"
                                        type="file"
                                        @change="handleFileUpload"
                                        accept="image/*"
                                        class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800"
                                    />
                                    <InputError class="mt-2" :message="form.errors.foto" />
                                    
                                    <!-- Preview da foto atual -->
                                    <div v-if="motor.foto_url" class="mt-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Foto atual:</p>
                                        <img :src="motor.foto_url" alt="Foto atual" class="w-32 h-32 object-cover rounded-md">
                                    </div>
                                    
                                    <!-- Preview da nova foto -->
                                    <div v-if="photoPreview" class="mt-4">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Nova foto:</p>
                                        <img :src="photoPreview" alt="Preview" class="w-32 h-32 object-cover rounded-md">
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                                    Observações
                                </h4>
                                <div>
                                    <InputLabel for="observacoes" value="Observações" class="text-gray-700 dark:text-gray-300" />
                                    <textarea
                                        id="observacoes"
                                        v-model="form.observacoes"
                                        rows="4"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        placeholder="Observações adicionais sobre o motor..."
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.observacoes" />
                                </div>
                            </div>

                            <!-- Status Ativo -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex items-center">
                                    <Checkbox
                                        id="ativo"
                                        v-model:checked="form.ativo"
                                        name="ativo"
                                    />
                                    <InputLabel for="ativo" value="Motor Ativo" class="ml-2 text-gray-700 dark:text-gray-300" />
                                </div>
                            </div>

                            <!-- Botões de ação -->
                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    {{ __('Atualizar Motor') }}
                                </PrimaryButton>
                                <Link :href="route('motores.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Cancelar') }}
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'

const props = defineProps({
    motor: Object,
    locais: Array,
})

const photoPreview = ref(null)
const fotoInput = ref(null)

const form = useForm({
    tag: props.motor.tag,
    equipamento: props.motor.equipamento,
    carcaca: props.motor.carcaca || '',
    potencia_kw: props.motor.potencia_kw || '',
    potencia_cv: props.motor.potencia_cv || '',
    rotacao: props.motor.rotacao || '',
    corrente_placa: props.motor.corrente_placa || '',
    corrente_configurada: props.motor.corrente_configurada || '',
    tipo_equipamento: props.motor.tipo_equipamento || '',
    fabricante: props.motor.fabricante || '',
    reserva_almox: props.motor.reserva_almox,
    local_id: props.motor.local_id,
    foto: null,
    armazenamento: props.motor.armazenamento,
    observacoes: props.motor.observacoes || '',
    ativo: props.motor.ativo,
})

const handleFileUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.foto = file
        const reader = new FileReader()
        reader.onload = (e) => {
            photoPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const submit = () => {
    form.put(route('motores.update', props.motor.id))
}
</script> 