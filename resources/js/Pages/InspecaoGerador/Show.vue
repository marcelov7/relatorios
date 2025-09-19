<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Inspeção de Gerador #{{ inspecao.id }}
                </h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('inspecao-geradores.edit', inspecao.id)"
                        class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Editar
                    </Link>
                    <Link
                        :href="route('inspecao-geradores.index')"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Voltar
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Debug Info (temporary) -->
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    <strong>DEBUG:</strong> 
                    <div>Inspeção ID: {{ inspecao?.id || 'UNDEFINED' }}</div>
                    <div>Data: {{ inspecao?.data_inspecao || 'UNDEFINED' }}</div>
                    <div>Colaborador: {{ inspecao?.colaborador?.name || 'UNDEFINED' }}</div>
                    <div>Setor: {{ inspecao?.setor?.nome || 'UNDEFINED' }}</div>
                    <div>Situação: {{ inspecao?.situacao || 'UNDEFINED' }}</div>
                    <div>Situação Class: {{ inspecao?.situacao_class || 'UNDEFINED' }}</div>
                    <div>Situação Icon: {{ inspecao?.situacao_icon || 'UNDEFINED' }}</div>
                    <div>Tensão A: {{ inspecao?.tensao_a || 'UNDEFINED' }}</div>
                    <div>Frequência: {{ inspecao?.frequencia || 'UNDEFINED' }}</div>
                    <div>Props completas: {{ JSON.stringify(inspecao, null, 2) }}</div>
                </div>

                <!-- Situação -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Situação Geral</h3>
                            <span
                                :class="[
                                    'px-3 py-1 text-sm font-medium rounded-full',
                                    inspecao.situacao_class
                                ]"
                            >
                                {{ inspecao.situacao_icon }} {{ inspecao.situacao }}
                            </span>
                        </div>
                        
                        <!-- Resumo dos Problemas -->
                        <div v-if="getProblemasDetectados().length > 0" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-red-800 dark:text-red-300 mb-2">⚠️ Problemas Detectados:</h4>
                            <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                                <li v-for="problema in getProblemasDetectados()" :key="problema" class="flex items-center">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                    {{ problema }}
                                </li>
                            </ul>
                        </div>
                        
                        <div v-else class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                            <div class="flex items-center text-sm text-green-800 dark:text-green-300">
                                <span class="mr-2">✅</span>
                                <span>Todos os parâmetros estão dentro dos padrões normais</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações Básicas -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações Básicas</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data da Inspeção</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ formatDate(inspecao.data_inspecao) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Colaborador</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ inspecao.colaborador?.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Setor</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ inspecao.setor_text || inspecao.setor?.nome || 'Não informado' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Criado por</label>
                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ inspecao.user?.name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Níveis (Motor Parado) -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Níveis (Motor Parado)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nível de Óleo do Motor</label>
                                <span
                                    :class="[
                                        'inline-block px-2 py-1 text-xs font-medium rounded',
                                        inspecao.nivel_oleo_motor_parado === 'NORMAL'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                    ]"
                                >
                                    {{ inspecao.nivel_oleo_motor_parado }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nível de Água</label>
                                <span
                                    :class="[
                                        'inline-block px-2 py-1 text-xs font-medium rounded',
                                        inspecao.nivel_agua_parado === 'NORMAL'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                    ]"
                                >
                                    {{ inspecao.nivel_agua_parado }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sincronização -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Sincronização</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sync Gerador</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.sync_gerador, 0, 999)">
                                    {{ getStatusIcon(inspecao.sync_gerador, 0, 999) }} {{ formatNumber(inspecao.sync_gerador) }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sync Rede</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.sync_rede, 0, 999)">
                                    {{ getStatusIcon(inspecao.sync_rede, 0, 999) }} {{ formatNumber(inspecao.sync_rede) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Temperatura -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Temperatura</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Temperatura da Água (20°C à 80°C)</label>
                            <p class="text-sm" :class="getStatusClass(inspecao.temperatura_agua, 20, 80)">
                                {{ getStatusIcon(inspecao.temperatura_agua, 20, 80) }} {{ formatNumber(inspecao.temperatura_agua) }}°C
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pressão e Frequência -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pressão e Frequência</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pressão de Óleo (3 à 6 bar)</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.pressao_oleo, 3, 6)">
                                    {{ getStatusIcon(inspecao.pressao_oleo, 3, 6) }} {{ formatNumber(inspecao.pressao_oleo) }} bar
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Frequência (57 à 63 Hz)</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.frequencia, 57, 63)">
                                    {{ getStatusIcon(inspecao.frequencia, 57, 63) }} {{ formatNumber(inspecao.frequencia) }} Hz
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tensões -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tensões (210V à 240V)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tensão A</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.tensao_a, 210, 240)">
                                    {{ getStatusIcon(inspecao.tensao_a, 210, 240) }} {{ formatNumber(inspecao.tensao_a) }}V
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tensão B</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.tensao_b, 210, 240)">
                                    {{ getStatusIcon(inspecao.tensao_b, 210, 240) }} {{ formatNumber(inspecao.tensao_b) }}V
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tensão C</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.tensao_c, 210, 240)">
                                    {{ getStatusIcon(inspecao.tensao_c, 210, 240) }} {{ formatNumber(inspecao.tensao_c) }}V
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RPM e Tensões do Sistema -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">RPM e Tensões do Sistema</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">RPM 1800</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.rpm_1800, 1700, 1900)">
                                    {{ getStatusIcon(inspecao.rpm_1800, 1700, 1900) }} {{ formatInteger(inspecao.rpm_1800) }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tensão da Bateria (24V à 26V)</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.tensao_bateria_parado, 24, 26)">
                                    {{ getStatusIcon(inspecao.tensao_bateria_parado, 24, 26) }} {{ formatNumber(inspecao.tensao_bateria_parado) }}V
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tensão do Alternador (24V à 28V)</label>
                                <p class="text-sm" :class="getStatusClass(inspecao.tensao_alternador_marcha, 24, 28)">
                                    {{ getStatusIcon(inspecao.tensao_alternador_marcha, 24, 28) }} {{ formatNumber(inspecao.tensao_alternador_marcha) }}V
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nível de Combustível -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Combustível</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nível de Combustível (acima de 50%)</label>
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div
                                        class="h-2 rounded-full transition-all duration-300"
                                        :class="[
                                            inspecao.nivel_combustivel >= 50
                                                ? 'bg-green-500'
                                                : inspecao.nivel_combustivel >= 25
                                                ? 'bg-yellow-500'
                                                : 'bg-red-500'
                                        ]"
                                        :style="{ width: (inspecao.nivel_combustivel || 0) + '%' }"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium" :class="getStatusClass(inspecao.nivel_combustivel, 50, 100)">
                                    {{ getStatusIcon(inspecao.nivel_combustivel, 50, 100) }} {{ inspecao.nivel_combustivel || 0 }}%
                                </span>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span v-if="inspecao.nivel_combustivel >= 50">✅ Nível adequado</span>
                                <span v-else-if="inspecao.nivel_combustivel >= 25">⚠️ Nível baixo - atenção</span>
                                <span v-else-if="inspecao.nivel_combustivel > 0">❌ Nível crítico - reabastecer</span>
                                <span v-else>❓ Nível não informado</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Condições da Sala -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Condições da Sala</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Iluminação da Sala Deficiente</label>
                                <span
                                    :class="[
                                        'inline-block px-2 py-1 text-xs font-medium rounded',
                                        inspecao.iluminacao_sala_deficiente
                                            ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                            : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                    ]"
                                >
                                    {{ inspecao.iluminacao_sala_deficiente ? 'SIM' : 'NÃO' }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Limpeza da Sala Realizada</label>
                                <span
                                    :class="[
                                        'inline-block px-2 py-1 text-xs font-medium rounded',
                                        inspecao.limpeza_sala_realizada
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
                                    ]"
                                >
                                    {{ inspecao.limpeza_sala_realizada ? 'SIM' : 'NÃO' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div v-if="inspecao.observacoes" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Observações</h3>
                        <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ inspecao.observacoes }}</p>
                    </div>
                </div>

                <!-- Informações do Sistema -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Informações do Sistema</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                            <div>
                                <label class="block font-medium">Criado em:</label>
                                <p>{{ formatDate(inspecao.created_at) }}</p>
                            </div>
                            <div>
                                <label class="block font-medium">Última atualização:</label>
                                <p>{{ formatDate(inspecao.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    inspecao: Object,
})

// Debug: Log the props when component mounts
onMounted(() => {
    console.log('=== DEBUG SHOW.VUE PROPS ===')
    console.log('Full props object:', props)
    console.log('Inspecao object:', props.inspecao)
    console.log('Data inspeção:', props.inspecao?.data_inspecao)
    console.log('Colaborador:', props.inspecao?.colaborador)
    console.log('Setor:', props.inspecao?.setor)
    console.log('Tensão A:', props.inspecao?.tensao_a)
    console.log('Frequência:', props.inspecao?.frequencia)
    console.log('Pressão óleo:', props.inspecao?.pressao_oleo)
    console.log('Temperatura:', props.inspecao?.temperatura_agua)
    console.log('Nível combustível:', props.inspecao?.nivel_combustivel)
    console.log('=== END DEBUG ===')
})

const formatDate = (date) => {
    if (!date) return 'Não informado'
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const formatNumber = (value) => {
    if (value === null || value === undefined || value === '') {
        return 'Não informado'
    }
    const numValue = Number(value)
    if (isNaN(numValue)) {
        return 'Não informado'
    }
    return numValue.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
}

const formatInteger = (value) => {
    if (value === null || value === undefined || value === '') {
        return 'Não informado'
    }
    const numValue = Number(value)
    if (isNaN(numValue)) {
        return 'Não informado'
    }
    return numValue.toLocaleString('pt-BR')
}

const getStatusClass = (value, min, max) => {
    if (value === null || value === undefined || value === '') {
        return 'text-gray-500 dark:text-gray-400'
    }
    const numValue = Number(value)
    if (isNaN(numValue)) {
        return 'text-gray-500 dark:text-gray-400'
    }
    if (numValue >= min && numValue <= max) {
        return 'text-green-600 dark:text-green-400 font-medium'
    } else {
        return 'text-red-600 dark:text-red-400 font-medium'
    }
}

const getStatusIcon = (value, min, max) => {
    if (value === null || value === undefined || value === '') {
        return '❓'
    }
    const numValue = Number(value)
    if (isNaN(numValue)) {
        return '❓'
    }
    if (numValue >= min && numValue <= max) {
        return '✅'
    } else {
        return '❌'
    }
}

const getProblemasDetectados = () => {
    const problemas = []
    
    // Verificar níveis
    if (props.inspecao.nivel_oleo_motor_parado === 'MÍNIMO') {
        problemas.push('Nível de óleo no mínimo')
    }
    
    if (props.inspecao.nivel_agua_parado === 'MÍNIMO') {
        problemas.push('Nível de água no mínimo')
    }
    
    // Verificar pressão de óleo (3 a 6 bar)
    if (props.inspecao.pressao_oleo && (props.inspecao.pressao_oleo < 3 || props.inspecao.pressao_oleo > 6)) {
        problemas.push('Pressão de óleo fora do padrão (3-6 bar)')
    }
    
    // Verificar frequência (57 a 63 Hz)
    if (props.inspecao.frequencia && (props.inspecao.frequencia < 57 || props.inspecao.frequencia > 63)) {
        problemas.push('Frequência fora do padrão (57-63 Hz)')
    }
    
    // Verificar tensões (210V a 240V)
    const tensoes = [props.inspecao.tensao_a, props.inspecao.tensao_b, props.inspecao.tensao_c]
    tensoes.forEach((tensao, index) => {
        if (tensao && (tensao < 210 || tensao > 240)) {
            problemas.push(`Tensão ${String.fromCharCode(65 + index)} fora do padrão (210-240V)`)
        }
    })
    
    // Verificar temperatura da água (20°C a 80°C)
    if (props.inspecao.temperatura_agua && (props.inspecao.temperatura_agua < 20 || props.inspecao.temperatura_agua > 80)) {
        problemas.push('Temperatura da água fora do padrão (20-80°C)')
    }
    
    // Verificar tensão da bateria (24V a 26V)
    if (props.inspecao.tensao_bateria_parado && (props.inspecao.tensao_bateria_parado < 24 || props.inspecao.tensao_bateria_parado > 26)) {
        problemas.push('Tensão da bateria fora do padrão (24-26V)')
    }
    
    // Verificar tensão do alternador (24V a 28V)
    if (props.inspecao.tensao_alternador_marcha && (props.inspecao.tensao_alternador_marcha < 24 || props.inspecao.tensao_alternador_marcha > 28)) {
        problemas.push('Tensão do alternador fora do padrão (24-28V)')
    }
    
    // Verificar nível de combustível (acima de 50%)
    if (props.inspecao.nivel_combustivel && props.inspecao.nivel_combustivel < 50) {
        problemas.push('Nível de combustível baixo (abaixo de 50%)')
    }
    
    // Verificar iluminação e limpeza
    if (props.inspecao.iluminacao_sala_deficiente) {
        problemas.push('Iluminação da sala deficiente')
    }
    
    if (!props.inspecao.limpeza_sala_realizada) {
        problemas.push('Limpeza da sala não realizada')
    }
    
    return problemas
}
</script> 