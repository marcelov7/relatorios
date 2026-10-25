<template>
    <AppLayout title="Histórico de Auditoria - Endereços Lógicos">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
                        <span class="p-2 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        Histórico de Auditoria & Alterações
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Registro detalhado de quem cadastrou, editou ou excluiu tags de CLP / Supervisório.
                    </p>
                </div>
                <Link
                    href="/enderecos-logicos"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 shadow-sm transition-all"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar aos Endereços
                </Link>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-4">
            <!-- Barra de Filtros de Logs -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        v-model="search"
                        @input="filtrarLogs"
                        type="text"
                        placeholder="Buscar por TAG ou nome do usuário..."
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                    />
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-xs text-gray-400 font-medium">Filtrar Ação:</span>
                    <select
                        v-model="acao"
                        @change="filtrarLogs"
                        class="text-xs rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900/60 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                    >
                        <option value="">Todas as Ações</option>
                        <option value="criou">➕ Cadastrou</option>
                        <option value="editou">✏️ Editou</option>
                        <option value="excluiu">🗑️ Excluiu</option>
                    </select>
                </div>
            </div>

            <!-- Tabela de Auditoria -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/75 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Data / Hora
                                </th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Usuário Responsável
                                </th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Ação
                                </th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    TAG Afetada
                                </th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Detalhes das Alterações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800 text-xs">
                            <tr v-for="log in logs.data" :key="log.id" class="hover:bg-purple-50/30 dark:hover:bg-gray-700/30 transition-colors">
                                <!-- Data e Hora -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    {{ formatarData(log.created_at) }}
                                </td>

                                <!-- Usuário -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ log.user_name || 'Usuário Desconhecido' }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ log.user_email || '-' }}
                                    </div>
                                </td>

                                <!-- Ação com Badge Colorida -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span
                                        v-if="log.acao === 'criou'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300"
                                    >
                                        ➕ Cadastrou
                                    </span>
                                    <span
                                        v-else-if="log.acao === 'editou'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300"
                                    >
                                        ✏️ Editou
                                    </span>
                                    <span
                                        v-else-if="log.acao === 'excluiu'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300"
                                    >
                                        🗑️ Excluiu
                                    </span>
                                    <span v-else class="text-gray-500">{{ log.acao }}</span>
                                </td>

                                <!-- TAG -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono font-bold bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                                        {{ log.tag }}
                                    </span>
                                </td>

                                <!-- Detalhes da Modificação (Antes vs Depois) -->
                                <td class="px-5 py-3.5">
                                    <div v-if="log.acao === 'editou' && log.dados_anteriores && log.dados_novos" class="space-y-1 max-w-md">
                                        <div v-for="(novoValor, campo) in log.dados_novos" :key="campo">
                                            <div v-if="log.dados_anteriores[campo] !== novoValor" class="text-[11px]">
                                                <strong class="capitalize text-gray-700 dark:text-gray-300">{{ campo }}:</strong>
                                                <span class="line-through text-red-500 mr-1 ml-1">{{ log.dados_anteriores[campo] || '(vazio)' }}</span>
                                                <span class="text-green-600 dark:text-green-400 font-medium">➔ {{ novoValor || '(vazio)' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else-if="log.acao === 'criou' && log.dados_novos" class="text-[11px] text-gray-600 dark:text-gray-300">
                                        <strong>Endereço:</strong> <code class="font-mono text-emerald-600 dark:text-emerald-400">{{ log.dados_novos.endereco }}</code> |
                                        <strong>Desc:</strong> {{ log.dados_novos.descricao || '-' }}
                                    </div>
                                    <div v-else-if="log.acao === 'excluiu' && log.dados_anteriores" class="text-[11px] text-gray-500">
                                        Endereço que foi removido: <code class="font-mono text-red-500">{{ log.dados_anteriores.endereco }}</code> ({{ log.dados_anteriores.descricao || '-' }})
                                    </div>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação de Logs -->
                <div v-if="logs.links && logs.links.length > 3" class="px-5 py-3.5 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-xs text-gray-500">
                        Total de <strong>{{ logs.total }}</strong> registros de auditoria
                    </span>
                    <div class="flex gap-1">
                        <template v-for="(link, i) in logs.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="link.active ? 'bg-purple-600 text-white font-bold' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100'"
                                class="px-3 py-1 text-xs rounded-lg border border-gray-200 dark:border-gray-700 transition-colors"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    logs: Object,
    filters: Object,
})

const search = ref(props.filters.search || '')
const acao = ref(props.filters.acao || '')
let searchTimeout = null

const filtrarLogs = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get('/enderecos-logicos/logs', {
            search: search.value || undefined,
            acao: acao.value || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        })
    }, 250)
}

const formatarData = (dataStr) => {
    if (!dataStr) return '-'
    const d = new Date(dataStr)
    return d.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    })
}
</script>
