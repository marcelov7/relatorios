<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Nova Inspeção de Gerador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Informações Básicas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Data -->
                                <div>
                                    <label for="data_inspecao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Data *
                                    </label>
                                    <input
                                        id="data_inspecao"
                                        v-model="form.data_inspecao"
                                        type="datetime-local"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.data_inspecao }"
                                        required
                                    />
                                    <div v-if="form.errors.data_inspecao" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.data_inspecao }}
                                    </div>
                                </div>

                                <!-- Colaborador -->
                                <div>
                                    <label for="colaborador_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Colaborador *
                                    </label>
                                    <select
                                        id="colaborador_id"
                                        v-model="form.colaborador_id"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.colaborador_id }"
                                        required
                                    >
                                        <option v-for="colaborador in colaboradores" :key="colaborador.id" :value="colaborador.id">
                                            {{ colaborador.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.colaborador_id" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.colaborador_id }}
                                    </div>
                                    <div v-if="form.colaborador_id" class="text-xs text-green-600 dark:text-green-400 mt-1">
                                        ✅ Colaborador selecionado: {{ getColaboradorName(form.colaborador_id) }}
                                    </div>
                                </div>

                                <!-- Setor -->
                                <div>
                                    <label for="setor_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Setor
                                    </label>
                                    <input
                                        id="setor_text"
                                        v-model="form.setor_text"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.setor_text }"
                                        placeholder="Digite o nome do setor..."
                                    />
                                    <div v-if="form.errors.setor_text" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.setor_text }}
                                    </div>
                                </div>
                            </div>

                            <!-- Níveis (Parado) -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Níveis (Motor Parado)</h3>
                                
                                <!-- Nível de Óleo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        NÍVEL DE ÓLEO DO MOTOR (PARADO) *
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <button
                                            type="button"
                                            @click="form.nivel_oleo_motor_parado = 'MÍNIMO'"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.nivel_oleo_motor_parado === 'MÍNIMO'
                                                    ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            MÍNIMO
                                        </button>
                                        <button
                                            type="button"
                                            @click="form.nivel_oleo_motor_parado = 'NORMAL'"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.nivel_oleo_motor_parado === 'NORMAL'
                                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            NORMAL
                                        </button>
                                    </div>
                                    <div v-if="form.errors.nivel_oleo_motor_parado" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.nivel_oleo_motor_parado }}
                                    </div>
                                </div>

                                <!-- Nível de Água -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        NÍVEL ÁGUA (PARADO) *
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <button
                                            type="button"
                                            @click="form.nivel_agua_parado = 'MÍNIMO'"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.nivel_agua_parado === 'MÍNIMO'
                                                    ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            MÍNIMO
                                        </button>
                                        <button
                                            type="button"
                                            @click="form.nivel_agua_parado = 'NORMAL'"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.nivel_agua_parado === 'NORMAL'
                                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            NORMAL
                                        </button>
                                    </div>
                                    <div v-if="form.errors.nivel_agua_parado" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.nivel_agua_parado }}
                                    </div>
                                </div>
                            </div>

                            <!-- Sincronização -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sincronização</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Sync Gerador -->
                                    <div>
                                        <label for="sync_gerador" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            SYNC GERADOR *
                                        </label>
                                        <input
                                            id="sync_gerador"
                                            v-model="form.sync_gerador"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="999.99"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            :class="{ 'border-red-500': form.errors.sync_gerador }"
                                            placeholder="0,00"
                                        />
                                        <div v-if="form.errors.sync_gerador" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.sync_gerador }}
                                        </div>
                                    </div>

                                    <!-- Sync Rede -->
                                    <div>
                                        <label for="sync_rede" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            SYNC REDE *
                                        </label>
                                        <input
                                            id="sync_rede"
                                            v-model="form.sync_rede"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="999.99"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            :class="{ 'border-red-500': form.errors.sync_rede }"
                                            placeholder="0,00"
                                        />
                                        <div v-if="form.errors.sync_rede" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.sync_rede }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Temperatura -->
                            <div>
                                <label for="temperatura_agua" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    TEMPERATURA ÁGUA 20°C À 80°C *
                                </label>
                                <input
                                    id="temperatura_agua"
                                    v-model="form.temperatura_agua"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="999.99"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    :class="{ 'border-red-500': form.errors.temperatura_agua }"
                                    placeholder="0,00"
                                />
                                <div v-if="form.errors.temperatura_agua" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.temperatura_agua }}
                                </div>
                            </div>

                            <!-- Pressão e Frequência -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pressão Óleo -->
                                <div>
                                    <label for="pressao_oleo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        PRESSÃO ÓLEO 3 À 6bar *
                                    </label>
                                    <input
                                        id="pressao_oleo"
                                        v-model="form.pressao_oleo"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="999.99"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.pressao_oleo }"
                                        placeholder="0,00"
                                    />
                                    <div v-if="form.errors.pressao_oleo" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.pressao_oleo }}
                                    </div>
                                </div>

                                <!-- Frequência -->
                                <div>
                                    <label for="frequencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        FREQUENCIA 57 À 63Hz *
                                    </label>
                                    <input
                                        id="frequencia"
                                        v-model="form.frequencia"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="999.99"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.frequencia }"
                                        placeholder="0,00"
                                    />
                                    <div v-if="form.errors.frequencia" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.frequencia }}
                                    </div>
                                </div>
                            </div>

                            <!-- Tensões -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tensões (210V à 240V)</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Tensão A -->
                                    <div>
                                        <label for="tensao_a" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            TENSÃO A *
                                        </label>
                                        <input
                                            id="tensao_a"
                                            v-model="form.tensao_a"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="9999.99"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            :class="{ 'border-red-500': form.errors.tensao_a }"
                                            placeholder="0,00"
                                        />
                                        <div v-if="form.errors.tensao_a" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.tensao_a }}
                                        </div>
                                    </div>

                                    <!-- Tensão B -->
                                    <div>
                                        <label for="tensao_b" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            TENSÃO B *
                                        </label>
                                        <input
                                            id="tensao_b"
                                            v-model="form.tensao_b"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="9999.99"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            :class="{ 'border-red-500': form.errors.tensao_b }"
                                            placeholder="0,00"
                                        />
                                        <div v-if="form.errors.tensao_b" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.tensao_b }}
                                        </div>
                                    </div>

                                    <!-- Tensão C -->
                                    <div>
                                        <label for="tensao_c" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            TENSÃO C *
                                        </label>
                                        <input
                                            id="tensao_c"
                                            v-model="form.tensao_c"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="9999.99"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                            :class="{ 'border-red-500': form.errors.tensao_c }"
                                            placeholder="0,00"
                                        />
                                        <div v-if="form.errors.tensao_c" class="text-red-600 text-sm mt-1">
                                            {{ form.errors.tensao_c }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RPM e Tensões do Sistema -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- RPM 1800 -->
                                <div>
                                    <label for="rpm_1800" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        RPM 1800 *
                                    </label>
                                    <input
                                        id="rpm_1800"
                                        v-model="form.rpm_1800"
                                        type="number"
                                        min="0"
                                        max="9999"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.rpm_1800 }"
                                        placeholder="0"
                                    />
                                    <div v-if="form.errors.rpm_1800" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.rpm_1800 }}
                                    </div>
                                </div>

                                <!-- Tensão Bateria -->
                                <div>
                                    <label for="tensao_bateria_parado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        VERIFICAR TENSÃO BATERIA 24V A 26V PARADO *
                                    </label>
                                    <input
                                        id="tensao_bateria_parado"
                                        v-model="form.tensao_bateria_parado"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="999.99"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.tensao_bateria_parado }"
                                        placeholder="0,00"
                                    />
                                    <div v-if="form.errors.tensao_bateria_parado" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.tensao_bateria_parado }}
                                    </div>
                                </div>

                                <!-- Tensão Alternador -->
                                <div>
                                    <label for="tensao_alternador_marcha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        TENSÃO DO ALTERNADOR 24V A 28V EM MARCHA *
                                    </label>
                                    <input
                                        id="tensao_alternador_marcha"
                                        v-model="form.tensao_alternador_marcha"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="999.99"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                        :class="{ 'border-red-500': form.errors.tensao_alternador_marcha }"
                                        placeholder="0,00"
                                    />
                                    <div v-if="form.errors.tensao_alternador_marcha" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.tensao_alternador_marcha }}
                                    </div>
                                </div>
                            </div>

                            <!-- Nível de Combustível -->
                            <div>
                                <label for="nivel_combustivel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    NÍVEL DE COMBUSTÍVEL ACIMA DE 50% EM LITROS *
                                </label>
                                <div class="flex items-center space-x-4">
                                    <button
                                        type="button"
                                        @click="decreaseCombustivel"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                    >
                                        -
                                    </button>
                                    <input
                                        id="nivel_combustivel"
                                        v-model="form.nivel_combustivel"
                                        type="number"
                                        min="0"
                                        max="100"
                                        class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-center"
                                        :class="{ 'border-red-500': form.errors.nivel_combustivel }"
                                        placeholder="0"
                                    />
                                    <button
                                        type="button"
                                        @click="increaseCombustivel"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                    >
                                        +
                                    </button>
                                    <span class="text-gray-700 dark:text-gray-300">%</span>
                                </div>
                                <div v-if="form.errors.nivel_combustivel" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.nivel_combustivel }}
                                </div>
                            </div>

                            <!-- Condições da Sala -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Condições da Sala</h3>
                                
                                <!-- Iluminação -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        ILUMINAÇÃO DA SALA DEFICIENTE? *
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <button
                                            type="button"
                                            @click="form.iluminacao_sala_deficiente = false"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.iluminacao_sala_deficiente === false
                                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            NÃO
                                        </button>
                                        <button
                                            type="button"
                                            @click="form.iluminacao_sala_deficiente = true"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.iluminacao_sala_deficiente === true
                                                    ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            SIM
                                        </button>
                                    </div>
                                    <div v-if="form.errors.iluminacao_sala_deficiente" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.iluminacao_sala_deficiente }}
                                    </div>
                                </div>

                                <!-- Limpeza -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        REALIZADO LIMPEZA DA SALA? *
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <button
                                            type="button"
                                            @click="form.limpeza_sala_realizada = false"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.limpeza_sala_realizada === false
                                                    ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            NÃO
                                        </button>
                                        <button
                                            type="button"
                                            @click="form.limpeza_sala_realizada = true"
                                            :class="[
                                                'p-4 rounded-lg border-2 text-center font-medium transition-colors',
                                                form.limpeza_sala_realizada === true
                                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300'
                                                    : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600'
                                            ]"
                                        >
                                            SIM
                                        </button>
                                    </div>
                                    <div v-if="form.errors.limpeza_sala_realizada" class="text-red-600 text-sm mt-1">
                                        {{ form.errors.limpeza_sala_realizada }}
                                    </div>
                                </div>
                            </div>

                            <!-- Observações -->
                            <div>
                                <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Observações
                                </label>
                                <textarea
                                    id="observacoes"
                                    v-model="form.observacoes"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                    :class="{ 'border-red-500': form.errors.observacoes }"
                                    placeholder="Observações adicionais..."
                                ></textarea>
                                <div v-if="form.errors.observacoes" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.observacoes }}
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end space-x-4">
                                <Link
                                    :href="route('inspecao-geradores.index')"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-500 hover:bg-blue-700 disabled:bg-blue-300 text-white font-bold py-2 px-4 rounded"
                                >
                                    {{ form.processing ? 'Salvando...' : 'Salvar Inspeção' }}
                                </button>
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
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    colaboradores: Array,
    setores: Array,
    user: Object,
    defaultColaborador: Object,
})

const form = useForm({
    data_inspecao: new Date().toISOString().slice(0, 16),
    colaborador_id: props.defaultColaborador?.id || props.user?.id || '',
    setor_id: '',
    setor_text: '',
    nivel_oleo_motor_parado: '',
    nivel_agua_parado: '',
    sync_gerador: '',
    sync_rede: '',
    temperatura_agua: '',
    pressao_oleo: '',
    frequencia: '',
    tensao_a: '',
    tensao_b: '',
    tensao_c: '',
    rpm_1800: '',
    tensao_bateria_parado: '',
    tensao_alternador_marcha: '',
    nivel_combustivel: 50,
    iluminacao_sala_deficiente: false,
    limpeza_sala_realizada: false,
    observacoes: '',
})

const submit = () => {
    form.post(route('inspecao-geradores.store'))
}

const increaseCombustivel = () => {
    if (form.nivel_combustivel < 100) {
        form.nivel_combustivel++
    }
}

const decreaseCombustivel = () => {
    if (form.nivel_combustivel > 0) {
        form.nivel_combustivel--
    }
}

const getColaboradorName = (colaboradorId) => {
    const colaborador = props.colaboradores.find(c => c.id === colaboradorId)
    return colaborador ? colaborador.name : ''
}
</script> 