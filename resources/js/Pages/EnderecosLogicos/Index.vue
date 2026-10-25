<template>
    <AppLayout title="Endereços Lógicos" subtitle="Busca rápida de tags e endereços de PLC/Supervisório">
        <div class="space-y-6">
            <!-- Cabeçalho & Ações Principais -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Endereços Lógicos</h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">
                                {{ enderecos.total }} cadastrados
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Consulte tags, descrições e endereços lógicos sem precisar abrir a lógica do CLP.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Botão Exportar CSV -->
                    <a
                        :href="exportUrl"
                        class="inline-flex items-center px-3.5 py-2 text-xs font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 shadow-sm transition-all"
                        title="Exportar para Excel (CSV)"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar
                    </a>

                    <!-- Botão Importar CSV -->
                    <button
                        @click="abrirModalImport"
                        class="inline-flex items-center px-3.5 py-2 text-xs font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 shadow-sm transition-all"
                        title="Importar planilha CSV"
                    >
                        <svg class="w-4 h-4 mr-1.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12" />
                        </svg>
                        Importar
                    </button>

                    <!-- Botão Novo Endereço -->
                    <button
                        @click="abrirModalNovo"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-blue-500/20 transition-all transform active:scale-95"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Endereço
                    </button>
                </div>
            </div>

            <!-- Barra de Busca Rápida em Tempo Real -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 space-y-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        ref="searchInput"
                        v-model="searchTerm"
                        @input="handleSearchInput"
                        type="text"
                        placeholder="Digite qualquer TAG, Descrição ou Endereço (ex: K2C, DVN2, Motor, Pressão, etc)..."
                        class="block w-full pl-10 pr-10 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 transition duration-150"
                    />
                    <button
                        v-if="searchTerm"
                        @click="limparBusca"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        title="Limpar pesquisa"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Filtros rápidos por Área (Pills com rolagem horizontal) -->
                <div v-if="areas.length > 0" class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                    <span class="text-gray-400 dark:text-gray-500 text-xs font-medium mr-1 flex items-center flex-shrink-0">
                        Área:
                    </span>
                    <button
                        @click="filtrarPorArea('')"
                        :class="selectedArea === '' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2.5 py-1 rounded-lg font-medium transition-colors flex-shrink-0"
                    >
                        Todas
                    </button>
                    <button
                        v-for="area in areas"
                        :key="area"
                        @click="filtrarPorArea(area)"
                        :class="selectedArea === area ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                        class="px-2.5 py-1 rounded-lg font-medium transition-colors flex-shrink-0"
                    >
                        {{ area }}
                    </button>
                </div>
            </div>

            <!-- Feedback de Carregamento / Resultados -->
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 px-1">
                <span>
                    Mostrando <strong>{{ enderecos.data.length }}</strong> de <strong>{{ enderecos.total }}</strong> registros encontrados
                    <span v-if="searchTerm || selectedArea">(filtrado)</span>
                </span>
                <span class="hidden sm:inline">
                    💡 Dica: clique nos botões para copiar instantaneamente para a área de transferência.
                </span>
            </div>

            <!-- Listagem: Visualização Desktop (Tabela Moderna) -->
            <div class="hidden md:block bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/75 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    TAG
                                </th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Descrição da Função
                                </th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Endereço Lógico (CLP / Supervisório)
                                </th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Área
                                </th>
                                <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800">
                            <tr
                                v-for="item in enderecos.data"
                                :key="item.id"
                                class="hover:bg-blue-50/40 dark:hover:bg-gray-700/30 transition-colors group"
                            >
                                <!-- TAG com botão de cópia rápida -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60">
                                            {{ item.tag }}
                                        </span>
                                        <button
                                            @click="copiarTexto(item.tag, `tag-${item.id}`)"
                                            class="p-1 rounded-md text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 opacity-60 group-hover:opacity-100 transition-all"
                                            :title="copiedId === `tag-${item.id}` ? 'Copiado!' : 'Copiar TAG'"
                                        >
                                            <svg v-if="copiedId === `tag-${item.id}`" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                <!-- Descrição -->
                                <td class="px-5 py-3.5 text-sm text-gray-800 dark:text-gray-200">
                                    <span class="line-clamp-2" :title="item.descricao">{{ item.descricao || '-' }}</span>
                                </td>

                                <!-- Endereço Lógico com botão de cópia em destaque -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <code class="px-2.5 py-1 text-xs font-mono font-medium rounded-lg bg-gray-100 dark:bg-gray-900/80 text-gray-800 dark:text-emerald-400 border border-gray-200 dark:border-gray-700/80 select-all">
                                            {{ item.endereco }}
                                        </code>
                                        <button
                                            @click="copiarTexto(item.endereco, `end-${item.id}`)"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md transition-all"
                                            :class="copiedId === `end-${item.id}` ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 font-semibold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                        >
                                            <svg v-if="copiedId === `end-${item.id}`" class="w-3.5 h-3.5 mr-1 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg v-else class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                            </svg>
                                            {{ copiedId === `end-${item.id}` ? 'Copiado!' : 'Copiar' }}
                                        </button>
                                    </div>
                                </td>

                                <!-- Área -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    <span v-if="item.area" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ item.area }}
                                    </span>
                                    <span v-else class="text-gray-300 dark:text-gray-600">-</span>
                                </td>

                                <!-- Ações -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            @click="abrirModalEdicao(item)"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            title="Editar"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="confirmarExclusao(item)"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                            title="Excluir"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Listagem: Visualização Mobile (Cards Touch-Friendly) -->
            <div class="block md:hidden space-y-3">
                <div
                    v-for="item in enderecos.data"
                    :key="item.id"
                    class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 space-y-3"
                >
                    <!-- Topo do Card: TAG e Área + Menu de Ações -->
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-mono font-bold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60">
                                {{ item.tag }}
                            </span>
                            <span v-if="item.area" class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ item.area }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button
                                @click="abrirModalEdicao(item)"
                                class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                title="Editar"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click="confirmarExclusao(item)"
                                class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20"
                                title="Excluir"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Descrição da Função -->
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ item.descricao || 'Sem descrição cadastrada' }}
                    </p>

                    <!-- Endereço Lógico em Destaque com Botão Largo de Cópia com 1 Toque -->
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/80 rounded-xl border border-gray-200/80 dark:border-gray-700/80 space-y-2">
                        <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                            Endereço Lógico
                        </div>
                        <div class="text-xs font-mono font-medium text-gray-900 dark:text-emerald-400 break-all select-all">
                            {{ item.endereco }}
                        </div>
                        <button
                            @click="copiarTexto(item.endereco, `end-m-${item.id}`)"
                            class="w-full flex items-center justify-center py-2 px-3 rounded-lg text-xs font-semibold transition-all mt-1 shadow-sm"
                            :class="copiedId === `end-m-${item.id}` ? 'bg-green-600 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white'"
                        >
                            <svg v-if="copiedId === `end-m-${item.id}`" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg v-else class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            {{ copiedId === `end-m-${item.id}` ? 'Endereço Copiado!' : 'Copiar Endereço' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estado Vazio -->
            <div v-if="enderecos.data.length === 0" class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 p-6">
                <div class="w-16 h-16 mx-auto rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nenhum endereço lógico encontrado</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-md mx-auto">
                    {{ searchTerm || selectedArea ? 'Tente ajustar ou limpar seus filtros de pesquisa para visualizar outros resultados.' : 'Não há endereços cadastrados no momento.' }}
                </p>
                <div class="mt-4 flex justify-center gap-2">
                    <button
                        v-if="searchTerm || selectedArea"
                        @click="limparFiltros"
                        class="px-4 py-2 text-xs font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        Limpar Filtros
                    </button>
                    <button
                        @click="abrirModalNovo"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20"
                    >
                        Cadastrar Novo Endereço
                    </button>
                </div>
            </div>

            <!-- Paginação Rápida -->
            <div v-if="enderecos.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Página <strong>{{ enderecos.current_page }}</strong> de <strong>{{ enderecos.last_page }}</strong>
                </div>

                <div class="flex items-center gap-1.5 flex-wrap justify-center">
                    <button
                        v-for="link in enderecos.links"
                        :key="link.label"
                        @click="navegarPagina(link.url)"
                        :disabled="!link.url || link.active"
                        v-html="link.label"
                        :class="[
                            link.active ? 'bg-blue-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700',
                            !link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer'
                        ]"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
                    />
                </div>
            </div>
        </div>

        <!-- MODAL: Criar / Editar Endereço Lógico -->
        <div v-if="modalAberto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-lg overflow-hidden transform transition-all animate-fadeIn">
                <!-- Cabeçalho do Modal -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            {{ modoEdicao ? 'Editar Endereço Lógico' : 'Novo Endereço Lógico' }}
                        </h3>
                    </div>
                    <button
                        @click="fecharModal"
                        class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Formulário -->
                <form @submit.prevent="salvarEndereco" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- TAG -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                                TAG <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.tag"
                                type="text"
                                required
                                placeholder="Ex: K2C041, F2P01C73"
                                class="w-full text-sm font-mono rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Área -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                                Área / Setor
                            </label>
                            <input
                                v-model="form.area"
                                type="text"
                                placeholder="Ex: Forno, Resfriador, Silos"
                                class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Endereço Lógico -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Endereço Lógico (Tag do CLP) <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.endereco"
                            type="text"
                            required
                            placeholder="Ex: DVN2_N01_S00_POL_I.Data.0"
                            class="w-full text-sm font-mono rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Descrição da Função -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Descrição da Função
                        </label>
                        <textarea
                            v-model="form.descricao"
                            rows="2"
                            placeholder="Ex: Curto Circuito - Programador Filtro Mangas"
                            class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <!-- Observações -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                            Observações Opcionais
                        </label>
                        <input
                            v-model="form.observacoes"
                            type="text"
                            placeholder="Notas de manutenção, detalhes do painel..."
                            class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Botões de Ação do Formulário -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button
                            type="button"
                            @click="fecharModal"
                            class="px-4 py-2 text-xs font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="salvando"
                            class="px-5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 disabled:opacity-50"
                        >
                            {{ salvando ? 'Salvando...' : (modoEdicao ? 'Salvar Alterações' : 'Cadastrar Endereço') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Importar CSV -->
        <div v-if="modalImportAberto" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-md overflow-hidden transform transition-all animate-fadeIn">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>📥</span> Importar Planilha CSV
                    </h3>
                    <button
                        @click="modalImportAberto = false"
                        class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submeterImportacao" class="p-6 space-y-4">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        O arquivo deve ser um CSV separado por ponto e vírgula (<code class="bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded">;</code>) com as colunas:
                        <strong>TAGS; DESCRIÇÃO; ENDEREÇO</strong>.
                    </p>

                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-6 text-center hover:border-blue-500 transition-colors bg-gray-50/50 dark:bg-gray-900/30">
                        <input
                            ref="fileInput"
                            type="file"
                            accept=".csv,text/csv,text/plain"
                            class="hidden"
                            @change="onFileSelected"
                        />
                        <button
                            type="button"
                            @click="$refs.fileInput.click()"
                            class="inline-flex flex-col items-center justify-center cursor-pointer"
                        >
                            <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">
                                {{ selectedFileName || 'Clique para selecionar o arquivo CSV' }}
                            </span>
                            <span class="text-[11px] text-gray-400 mt-1">Formatos suportados: .csv (máx 5MB)</span>
                        </button>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="modalImportAberto = false"
                            class="px-4 py-2 text-xs font-medium rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="!selectedFile || importando"
                            class="px-5 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 disabled:opacity-50"
                        >
                            {{ importando ? 'Importando...' : 'Iniciar Importação' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useNotifications } from '@/composables/useNotifications'

const props = defineProps({
    enderecos: {
        type: Object,
        required: true
    },
    areas: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({ search: '', area: '', per_page: 50 })
    },
    stats: {
        type: Object,
        default: () => ({ total: 0 })
    }
})

const { success, error } = useNotifications()

// Estado de busca e filtros
const searchInput = ref(null)
const searchTerm = ref(props.filters.search || '')
const selectedArea = ref(props.filters.area || '')
let searchTimeout = null

// Estado de cópia para feedback visual instantâneo
const copiedId = ref(null)

// Modal de Criação / Edição
const modalAberto = ref(false)
const modoEdicao = ref(false)
const editandoId = ref(null)
const salvando = ref(false)
const form = ref({
    tag: '',
    endereco: '',
    descricao: '',
    area: '',
    observacoes: ''
})

// Modal de Importação CSV
const modalImportAberto = ref(false)
const selectedFile = ref(null)
const selectedFileName = ref('')
const importando = ref(false)

// URL de Exportação dinâmica com filtros
const exportUrl = computed(() => {
    const params = new URLSearchParams()
    if (searchTerm.value) params.append('search', searchTerm.value)
    if (selectedArea.value) params.append('area', selectedArea.value)
    return `/enderecos-logicos/export?${params.toString()}`
})

// Busca em tempo real com debounce de 250ms
const handleSearchInput = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        executarFiltros()
    }, 250)
}

const filtrarPorArea = (area) => {
    selectedArea.value = area
    executarFiltros()
}

const limparBusca = () => {
    searchTerm.value = ''
    executarFiltros()
    searchInput.value?.focus()
}

const limparFiltros = () => {
    searchTerm.value = ''
    selectedArea.value = ''
    executarFiltros()
}

const executarFiltros = () => {
    router.get('/enderecos-logicos', {
        search: searchTerm.value || undefined,
        area: selectedArea.value || undefined,
        per_page: props.filters.per_page || 50
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    })
}

const navegarPagina = (url) => {
    if (!url) return
    router.get(url, {}, {
        preserveState: true,
        preserveScroll: true
    })
}

// Cópia ultra-rápida com fallback
const copiarTexto = async (texto, id) => {
    if (!texto) return

    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(texto)
        } else {
            // Fallback para contextos não-HTTPS ou navegadores antigos
            const textarea = document.createElement('textarea')
            textarea.value = texto
            textarea.style.position = 'fixed'
            textarea.style.opacity = '0'
            document.body.appendChild(textarea)
            textarea.select()
            document.execCommand('copy')
            document.body.removeChild(textarea)
        }

        copiedId.value = id
        setTimeout(() => {
            if (copiedId.value === id) {
                copiedId.value = null
            }
        }, 1800)
    } catch (err) {
        console.error('Falha ao copiar:', err)
        error('Não foi possível copiar o texto automaticamente.')
    }
}

// Abertura de Modais
const abrirModalNovo = () => {
    modoEdicao.value = false
    editandoId.value = null
    form.value = {
        tag: '',
        endereco: '',
        descricao: '',
        area: selectedArea.value || '',
        observacoes: ''
    }
    modalAberto.value = true
}

const abrirModalEdicao = (item) => {
    modoEdicao.value = true
    editandoId.value = item.id
    form.value = {
        tag: item.tag || '',
        endereco: item.endereco || '',
        descricao: item.descricao || '',
        area: item.area || '',
        observacoes: item.observacoes || ''
    }
    modalAberto.value = true
}

const fecharModal = () => {
    modalAberto.value = false
    salvando.value = false
}

// Salvar Endereço (Store / Update)
const salvarEndereco = () => {
    if (salvando.value) return
    salvando.value = true

    if (modoEdicao.value && editandoId.value) {
        router.put(`/enderecos-logicos/${editandoId.value}`, form.value, {
            preserveScroll: true,
            onSuccess: () => {
                fecharModal()
                success('Endereço atualizado com sucesso!')
            },
            onError: (errs) => {
                salvando.value = false
                const firstErr = Object.values(errs)[0] || 'Erro ao atualizar endereço.'
                error(firstErr)
            }
        })
    } else {
        router.post('/enderecos-logicos', form.value, {
            preserveScroll: true,
            onSuccess: () => {
                fecharModal()
                success('Novo endereço cadastrado com sucesso!')
            },
            onError: (errs) => {
                salvando.value = false
                const firstErr = Object.values(errs)[0] || 'Erro ao cadastrar endereço.'
                error(firstErr)
            }
        })
    }
}

// Excluir Endereço
const confirmarExclusao = (item) => {
    if (confirm(`Tem certeza que deseja excluir o endereço "${item.tag}" (${item.endereco})?`)) {
        router.delete(`/enderecos-logicos/${item.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                success(`Endereço ${item.tag} excluído com sucesso!`)
            },
            onError: () => {
                error('Não foi possível excluir o endereço.')
            }
        })
    }
}

// Importação CSV
const abrirModalImport = () => {
    selectedFile.value = null
    selectedFileName.value = ''
    modalImportAberto.value = true
}

const onFileSelected = (e) => {
    const file = e.target.files[0]
    if (file) {
        selectedFile.value = file
        selectedFileName.value = file.name
    }
}

const submeterImportacao = () => {
    if (!selectedFile.value || importando.value) return
    importando.value = true

    const formData = new FormData()
    formData.append('arquivo', selectedFile.value)

    router.post('/enderecos-logicos/import', formData, {
        preserveScroll: true,
        onSuccess: () => {
            modalImportAberto.value = false
            importando.value = false
            success('Planilha importada com sucesso!')
        },
        onError: (errs) => {
            importando.value = false
            const firstErr = Object.values(errs)[0] || 'Erro ao importar arquivo CSV.'
            error(firstErr)
        }
    })
}
</script>

<style scoped>
/* Ocultar barra de rolagem horizontal mantendo rolagem funcional */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.96); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
    animation: fadeIn 0.15s ease-out forwards;
}
</style>
