<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Novo Motor
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Informações Básicas -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Informações Básicas
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="tag" value="Tag" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="tag"
                                            v-model="form.tag"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.tag" />
                                    </div>

                                    <div>
                                        <InputLabel for="equipamento" value="Equipamento" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="equipamento"
                                            v-model="form.equipamento"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.equipamento" />
                                    </div>

                                    <div>
                                        <InputLabel for="carcaca_fabricante" value="Carcaça (Fabricante)" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="carcaca_fabricante"
                                            v-model="form.carcaca_fabricante"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.carcaca_fabricante" />
                                    </div>

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

                                    <div>
                                        <InputLabel for="tipo_equipamento_modelo" value="Tipo de Equipamento / Modelo" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="tipo_equipamento_modelo"
                                            v-model="form.tipo_equipamento_modelo"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.tipo_equipamento_modelo" />
                                    </div>

                                    <div>
                                        <InputLabel for="local" value="Local" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="local"
                                            v-model="form.local"
                                            type="text"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.local" />
                                    </div>
                                </div>
                            </div>

                            <!-- Especificações Técnicas -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Especificações Técnicas
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="potencia_kw" value="Potência (kW)" class="text-gray-700 dark:text-gray-300" />
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

                                    <div>
                                        <InputLabel for="reserva_almox" value="Reserva no Almoxarifado" class="text-gray-700 dark:text-gray-300" />
                                        <TextInput
                                            id="reserva_almox"
                                            v-model="form.reserva_almox"
                                            type="text"
                                            placeholder="Ex: Prateleira A, Gaveta 3, etc."
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        />
                                        <InputError class="mt-2" :message="form.errors.reserva_almox" />
                                    </div>
                                </div>
                            </div>

                            <!-- Foto -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Foto do Motor
                                </h3>
                                <div>
                                    <InputLabel for="foto" value="Foto" class="text-gray-700 dark:text-gray-300" />
                                    <input
                                        id="foto"
                                        type="file"
                                        @change="handleFileUpload"
                                        accept="image/*"
                                        class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-400"
                                    />
                                    <InputError class="mt-2" :message="form.errors.foto" />
                                </div>
                            </div>

                            <!-- Observações -->
                            <div>
                                <InputLabel for="observacoes" value="Observações" class="text-gray-700 dark:text-gray-300" />
                                <textarea
                                    id="observacoes"
                                    v-model="form.observacoes"
                                    rows="4"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.observacoes" />
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="flex items-center">
                                    <Checkbox
                                        v-model:checked="form.ativo"
                                        name="ativo"
                                    />
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Motor ativo</span>
                                </label>
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center gap-4">
                                <PrimaryButton :disabled="form.processing">
                                    {{ form.processing ? 'Salvando...' : 'Cadastrar Motor' }}
                                </PrimaryButton>
                                <Link
                                    :href="route('motores.index')"
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
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Checkbox from '@/Components/Checkbox.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    locais: Array,
})

const form = useForm({
    tag: '',
    equipamento: '',
    carcaca_fabricante: '',
    potencia_kw: '',
    potencia_cv: '',
    rotacao: '',
    corrente_placa: '',
    corrente_configurada: '',
    tipo_equipamento_modelo: '',
    fabricante: '',
    reserva_almox: '',
    local: '',
    foto: null,
    observacoes: '',
    ativo: true,
})

const handleFileUpload = (event) => {
    form.foto = event.target.files[0]
}

const submit = () => {
    form.post(route('motores.store'), {
        onSuccess: () => {
            form.reset()
        },
    })
}
</script> 