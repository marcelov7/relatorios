<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Visualizar relatório
            </h2>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Cabeçalho da Página -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ relatorio.titulo }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Criado em {{ formatarData(relatorio.date_created) }}
                            </p>
                        </div>
                        
                        <!-- Botões Desktop -->
                        <div class="hidden sm:flex items-center gap-2">
                            <!-- Botão Editar -->
                            <Link v-if="podeEditar"
                                :href="route('relatorios.edit', relatorio.id)"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </Link>
                            
                            <!-- Botão Excluir -->
                            <button v-if="podeExcluir"
                                @click="confirmarExclusao"
                                class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Excluir
                            </button>
                            
                            <!-- Aviso sobre permissões de exclusão -->
                            <div v-if="ehAutor && !$page.props.auth.user.isAdmin && tempoRestanteExclusao !== null && tempoRestanteExclusao === 0" 
                                 class="inline-flex items-center px-4 py-2 bg-orange-100 dark:bg-orange-900 border border-orange-300 dark:border-orange-700 rounded-md">
                                <svg class="w-4 h-4 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs text-orange-700 dark:text-orange-300">
                                    Prazo para exclusão expirado
                                </span>
                            </div>
                            <Link 
                                :href="route('relatorios.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                Voltar
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Botões Mobile -->
                    <div class="sm:hidden mt-4 flex flex-col gap-2">
                        <div class="flex gap-2">
                            <!-- Botão Editar Mobile -->
                            <Link v-if="podeEditar"
                                :href="route('relatorios.edit', relatorio.id)"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </Link>
                            
                            <!-- Botão Excluir Mobile -->
                            <button v-if="podeExcluir"
                                @click="confirmarExclusao"
                                class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Excluir
                            </button>
                        </div>
                        
                        <!-- Aviso de permissões Mobile -->
                        <div v-if="ehAutor && !$page.props.auth.user.isAdmin && tempoRestanteExclusao !== null && tempoRestanteExclusao === 0" 
                             class="px-4 py-2 bg-orange-100 dark:bg-orange-900 border border-orange-300 dark:border-orange-700 rounded-md">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs text-orange-700 dark:text-orange-300">
                                    Prazo para exclusão expirado (24h). Apenas administradores podem excluir este relatório.
                                </span>
                            </div>
                        </div>
                        <Link 
                            :href="route('relatorios.index')"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            Voltar
                        </Link>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <!-- Informações Básicas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                            <!-- Atividade -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Atividade
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ relatorio.activity }}
                                </p>
                            </div>

                            <!-- Responsável -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Responsável
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ relatorio.nome_responsavel || (relatorio.autor ? relatorio.autor.name : 'Não informado') }}
                                </p>
                                <p v-if="relatorio.cargo_responsavel || (relatorio.autor && relatorio.autor.cargo)" class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ relatorio.cargo_responsavel || (relatorio.autor ? relatorio.autor.cargo : '') }}
                                </p>
                            </div>

                            <!-- Data de Criação -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data de Criação
                                </label>
                                <p class="text-sm text-gray-900 dark:text-gray-200 font-medium">
                                    {{ formatarData(relatorio.date_created) }}
                                </p>
                            </div>
                        </div>

                        <!-- Equipamentos de Teste -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Equipamentos
                            </label>
                            <div v-if="equipamentosTeste && equipamentosTeste.length > 0" class="space-y-2">
                                <ul class="space-y-1">
                                    <li v-for="equip in equipamentosTeste" :key="equip.id" class="flex flex-wrap items-center gap-x-2 gap-y-1 min-w-0 w-full">
                                        <span class="font-semibold text-blue-700 dark:text-blue-300 text-[11px] truncate max-w-[110px] sm:max-w-[160px] md:max-w-[200px]">{{ equip.tag }}</span>
                                        <span class="text-[11px] text-gray-700 dark:text-gray-200 break-words max-w-[140px] sm:max-w-[220px] md:max-w-[320px] whitespace-normal">{{ equip.nome }}</span>
                                        <span class="text-[11px] px-1 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 truncate max-w-[80px] sm:max-w-[120px]">{{ equip.setor || 'Sem setor' }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[11px] font-bold border whitespace-nowrap"
                                              :class="equip.status === 'Operacional' ? 'bg-green-100 border-green-400 text-green-800 dark:bg-green-900/40 dark:text-green-300 dark:border-green-600' : 'bg-yellow-100 border-yellow-400 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 dark:border-yellow-600'">
                                            {{ equip.status }}
                                        </span>
                                    </li>
                                </ul>
                                <div class="mt-2">
                                    <p class="text-sm text-blue-600 dark:text-blue-400">
                                        {{ equipamentosTeste.length }} equipamento(s) de teste selecionado(s)
                                    </p>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500 dark:text-gray-400 italic">
                                Nenhum equipamento de teste vinculado a este relatório.
                            </div>
                        </div>



                        <!-- Progresso e Status -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Progresso: {{ relatorio.progresso }}%
                                </label>
                                <button 
                                    v-if="podeAtualizar && relatorio.progresso < 100"
                                    @click="abrirModalProgresso"
                                    class="inline-flex items-center px-3 py-1 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 focus:bg-green-700 dark:focus:bg-green-600 active:bg-green-900 dark:active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Atualizar
                                </button>
                            </div>
                            <div class="relative">
                                <!-- Barra de progresso visual -->
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 relative overflow-hidden">
                                    <div 
                                        class="h-full transition-all duration-300 ease-out rounded-full"
                                        :class="getProgressBarClass(relatorio.progresso)"
                                        :style="{ width: relatorio.progresso + '%' }"
                                    ></div>
                                </div>
                                <!-- Labels de progresso -->
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>0%</span>
                                    <span>25%</span>
                                    <span>50%</span>
                                    <span>75%</span>
                                    <span>100%</span>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Status
                                </label>
                                <div class="flex items-center space-x-2">
                                    <div 
                                        class="w-3 h-3 rounded-full"
                                        :class="getStatusIndicatorClass(relatorio.status)"
                                    ></div>
                                    <span 
                                        class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium border"
                                        :class="getStatusInputClass(relatorio.status)"
                                    >
                                        {{ relatorio.status }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Status atualizado automaticamente baseado no progresso
                                </p>
                            </div>
                        </div>

                        <!-- Detalhes -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Detalhes
                            </label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div v-if="relatorio.detalhes" class="text-sm text-gray-900 dark:text-gray-100 rich-text-content" v-html="relatorio.detalhes"></div>
                                <p v-else class="text-sm text-gray-500 dark:text-gray-400 italic">Nenhum detalhe informado</p>
                            </div>
                        </div>

                        <!-- Imagens -->
                        <div v-if="relatorio.images && relatorio.images.length > 0" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Imagens ({{ relatorio.images.length }})
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                                <div
                                    v-for="(image, index) in relatorio.images"
                                    :key="index"
                                    class="relative group cursor-pointer"
                                    @click="openImageModal(image, index)"
                                >
                                    <img
                                        :src="`/storage/${image.thumb || image.path}`"
                                        :alt="image.original_name"
                                        loading="lazy"
                                        class="w-full h-24 sm:h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-lg transition-shadow duration-200"
                                    />
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div class="absolute bottom-1 sm:bottom-2 left-1 sm:left-2 right-1 sm:right-2">
                                        <p class="text-xs text-white bg-black bg-opacity-50 rounded px-1 sm:px-2 py-1 truncate">
                                            {{ image.original_name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Histórico de Atualizações -->
                        <div v-if="relatorio.atualizacoes && relatorio.atualizacoes.length > 0" class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Histórico de Atualizações ({{ relatorio.atualizacoes.length }})
                                </label>
                                <button 
                                    @click="timelineExpandida = !timelineExpandida"
                                    class="inline-flex items-center px-3 py-1 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                >
                                    <span v-if="timelineExpandida">Ver Menos</span>
                                    <span v-else>Ver Tudo</span>
                                    <svg 
                                        class="w-4 h-4 ml-1 transition-transform duration-200" 
                                        :class="{ 'rotate-180': timelineExpandida }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Timeline -->
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    <li 
                                        v-for="(atualizacao, index) in (timelineExpandida ? relatorio.atualizacoes : relatorio.atualizacoes.slice(0, 3))"
                                        :key="atualizacao.id"
                                        class="relative"
                                    >
                                        <div v-if="index !== (timelineExpandida ? relatorio.atualizacoes.length - 1 : Math.min(2, relatorio.atualizacoes.length - 1))" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></div>
                                        
                                        <div class="relative flex space-x-3">
                                            <!-- Ícone da timeline -->
                                            <div>
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800"
                                                      :class="getTimelineIconClass(atualizacao.progresso_novo)">
                                                    <svg v-if="atualizacao.progresso_novo === 100" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    <svg v-else class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            
                                            <!-- Conteúdo da atualização -->
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                                    <!-- Header da atualização -->
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-2">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                {{ atualizacao.usuario.name }}
                                                            </p>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ atualizacao.usuario.cargo }}
                                                            </span>
                                                        </div>
                                                        <time class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ formatarData(atualizacao.created_at) }}
                                                        </time>
                                                    </div>
                                                    
                                                    <!-- Progresso -->
                                                    <div class="mb-3">
                                                        <div class="flex items-center justify-between text-sm mb-1">
                                                            <span class="text-gray-600 dark:text-gray-400">Progresso:</span>
                                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                                {{ atualizacao.progresso_anterior }}% → {{ atualizacao.progresso_novo }}%
                                                            </span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                            <div 
                                                                class="h-2 rounded-full transition-all duration-300"
                                                                :class="getProgressBarClass(atualizacao.progresso_novo)"
                                                                :style="{ width: atualizacao.progresso_novo + '%' }"
                                                            ></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Status -->
                                                    <div class="mb-3 flex items-center space-x-2">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                              :class="getStatusBadgeClass(atualizacao.status_anterior)">
                                                            {{ atualizacao.status_anterior }}
                                                        </span>
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                        </svg>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                              :class="getStatusBadgeClass(atualizacao.status_novo)">
                                                            {{ atualizacao.status_novo }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Descrição -->
                                                    <div class="mb-3">
                                                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                                            {{ atualizacao.descricao }}
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Imagens da atualização -->
                                                    <div v-if="atualizacao.imagens && atualizacao.imagens.length > 0" class="mt-3">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                            Imagens anexadas ({{ atualizacao.imagens.length }})
                                                        </p>
                                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3">
                                                            <div
                                                                v-for="(imagem, imgIndex) in atualizacao.imagens"
                                                                :key="`preview-${index}-${imgIndex}`"
                                                                class="relative group"
                                                            >
                                                                <div class="relative w-full h-16 md:h-20 bg-gray-100 dark:bg-gray-600 rounded border border-gray-300 dark:border-gray-600 overflow-hidden">
                                                                    <img
                                                                        v-if="imagem.path"
                                                                        :src="`/storage/${imagem.path}`"
                                                                        :alt="imagem.original_name || 'Imagem Anexada'"
                                                                        class="w-full h-full object-cover"
                                                                        @click="openImageModalHistorico(atualizacao.imagens, imgIndex)"
                                                                    />
                                                                    <div v-else class="flex items-center justify-center w-full h-full">
                                                                        <svg class="w-4 h-4 md:w-6 md:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate hidden md:block" :title="imagem.original_name">
                                                                    {{ imagem.original_name }}
                                                                </p>
                                                                <p class="text-xs text-gray-400 dark:text-gray-500 hidden md:block">
                                                                    {{ formatFileSize(imagem.size) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Mostra quantas atualizações estão ocultas -->
                            <div v-if="!timelineExpandida && relatorio.atualizacoes.length > 3" class="mt-4 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    + {{ relatorio.atualizacoes.length - 3 }} atualizações mais antigas
                                </p>
                            </div>
                        </div>
                        
                        <!-- Mensagem se não há histórico -->
                        <div v-else-if="relatorio.progresso > 0" class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Histórico de Atualizações
                            </label>
                            <div class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Nenhuma atualização de progresso registrada ainda
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Imagem -->
        <div v-if="imageModal.show" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-2 sm:p-4" @click="closeImageModal">
            <div class="w-full max-w-4xl max-h-full flex flex-col" @click.stop>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg overflow-hidden flex flex-col max-h-full">
                    <!-- Header do Modal -->
                    <div class="flex items-center justify-between p-3 sm:p-4 border-b border-gray-200 dark:border-gray-600 flex-shrink-0">
                        <h3 class="text-sm sm:text-lg font-medium text-gray-900 dark:text-gray-100 truncate mr-4">
                            {{ imageModal.image?.original_name }}
                        </h3>
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ imageModal.index + 1 }} de {{ imageModal.images.length }}
                            </span>
                            <button
                                @click="closeImageModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1"
                            >
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Imagem -->
                    <div class="flex-1 p-2 sm:p-4 flex items-center justify-center overflow-hidden">
                        <img
                            :src="`/storage/${imageModal.image?.path}`"
                            :alt="imageModal.image?.original_name"
                            class="max-w-full max-h-full object-contain rounded-lg"
                        />
                    </div>
                    
                    <!-- Footer do Modal -->
                    <div class="flex items-center justify-between p-3 sm:p-4 border-t border-gray-200 dark:border-gray-600 flex-shrink-0">
                        <!-- Botão Anterior -->
                        <button
                            @click="previousImage"
                            :disabled="imageModal.index === 0"
                            class="inline-flex items-center px-2 sm:px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="hidden sm:inline">Anterior</span>
                            <span class="sm:hidden">Ant</span>
                        </button>
                        
                        <!-- Info do arquivo (oculta no mobile) -->
                        <div class="hidden sm:block text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                            {{ formatFileSize(imageModal.image?.size) }}
                        </div>
                        
                        <!-- Botão Próxima -->
                        <button
                            @click="nextImage"
                            :disabled="imageModal.index === imageModal.images.length - 1"
                            class="inline-flex items-center px-2 sm:px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="hidden sm:inline">Próxima</span>
                            <span class="sm:hidden">Prox</span>
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Modal de Atualização de Progresso -->
        <div v-if="modalProgresso.show" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-0 md:p-4" @click="fecharModalProgresso">
            <!-- Container responsivo para a modal -->
            <div class="w-full h-full md:h-auto md:max-w-2xl md:max-h-[90vh] flex flex-col" @click.stop>
                <!-- Modal content -->
                <div class="bg-white dark:bg-gray-800 rounded-none md:rounded-lg shadow-xl flex flex-col h-full md:h-auto overflow-hidden">
                    <!-- Header do Modal -->
                    <div class="flex-shrink-0 flex items-center justify-between p-4 md:px-6 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg md:text-xl font-medium text-gray-900 dark:text-gray-100">
                            Atualizar Progresso do Relatório
                        </h3>
                        <button @click="fecharModalProgresso" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Botões de Ação no Topo -->
                    <div class="flex-shrink-0 flex items-center justify-center gap-3 p-4 md:px-6 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                        <button
                            @click="fecharModalProgresso"
                            class="flex-1 md:flex-none px-4 py-3 md:py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="atualizarProgresso"
                            :disabled="modalProgresso.loading || !modalProgresso.descricao.trim()"
                            class="flex-1 md:flex-none px-4 py-3 md:py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                        >
                            <span v-if="modalProgresso.loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Atualizando...
                            </span>
                            <span v-else>Atualizar Progresso</span>
                        </button>
                    </div>
                    
                    <!-- Conteúdo do Modal -->
                    <div class="flex-1 overflow-y-auto min-h-0">
                        <div class="p-4 md:px-6 md:py-4 space-y-4 md:space-y-6">
                            <!-- Progresso atual -->
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Progresso atual: <span class="font-medium">{{ relatorio.progresso }}%</span></p>
                            </div>

                            <!-- Novo progresso -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Novo Progresso: {{ modalProgresso.progresso }}%
                                </label>
                                <input
                                    v-model="modalProgresso.progresso"
                                    type="range"
                                    :min="relatorio.progresso"
                                    max="100"
                                    step="1"
                                    class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-lg appearance-none cursor-pointer"
                                />
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>{{ relatorio.progresso }}%</span>
                                    <span>100%</span>
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Descrição da Atualização *
                                </label>
                                <textarea
                                    v-model="modalProgresso.descricao"
                                    rows="3"
                                    placeholder="Descreva o que foi realizado nesta atualização..."
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm resize-none md:resize-y"
                                    required
                                ></textarea>
                            </div>

                            <!-- Upload de imagens -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Imagens (opcional)
                                </label>
                                <div 
                                    class="border-2 border-dashed rounded-lg text-center transition-all duration-200 h-20 p-2 md:h-32 md:p-6"
                                    :class="[
                                        dragActive 
                                            ? 'border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20' 
                                            : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'
                                    ]"
                                    @dragover="handleDragOver"
                                    @dragleave="handleDragLeave"
                                    @drop="handleDrop"
                                >
                                    <input
                                        type="file"
                                        multiple
                                        accept="image/jpeg,image/png,image/gif,image/webp"
                                        @change="adicionarImagemProgresso"
                                        class="hidden"
                                        ref="fileInputProgresso"
                                    />
                                    <div class="space-y-2">
                                        <svg class="mx-auto h-8 w-8 md:h-12 md:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <button
                                            type="button"
                                            @click="$refs.fileInputProgresso.click()"
                                            class="inline-flex items-center px-3 py-2 md:px-4 md:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-xs md:text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors duration-200"
                                        >
                                            <svg class="w-4 h-4 md:w-5 md:h-5 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span class="hidden sm:inline">Adicionar Imagens</span>
                                            <span class="sm:hidden">Adicionar</span>
                                        </button>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            PNG, JPG, GIF, WebP até 10MB cada
                                        </p>
                                        <p class="text-xs transition-colors duration-200" 
                                           :class="dragActive 
                                               ? 'text-blue-600 dark:text-blue-400 font-medium' 
                                               : 'text-gray-400 dark:text-gray-500'">
                                            <span v-if="dragActive">Solte as imagens aqui</span>
                                            <span v-else>Clique para selecionar ou arraste as imagens aqui</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Preview das imagens -->
                                <div v-if="modalProgresso.imagens.length > 0" class="mt-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        {{ modalProgresso.imagens.length }} imagem(ns) selecionada(s)
                                    </p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 max-h-[92px] md:max-h-[160px] overflow-y-auto">
                                        <div
                                            v-for="(imagem, index) in modalProgresso.imagens"
                                            :key="`preview-${index}`"
                                            class="relative group"
                                        >
                                            <div class="relative w-full h-16 md:h-20 bg-gray-100 dark:bg-gray-600 rounded border border-gray-300 dark:border-gray-600 overflow-hidden">
                                                <img
                                                    v-if="imagem.preview"
                                                    :src="imagem.preview"
                                                    :alt="imagem.name"
                                                    class="w-full h-full object-cover"
                                                    @error="handleImageError(index)"
                                                />
                                                <div v-else class="flex items-center justify-center w-full h-full">
                                                    <svg class="w-4 h-4 md:w-6 md:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <button
                                                @click="removerImagemProgresso(index)"
                                                class="absolute -top-1 -right-1 md:-top-2 md:-right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-0.5 md:p-1 opacity-0 group-hover:opacity-100 md:opacity-100 transition-opacity duration-200 shadow-lg"
                                                title="Remover imagem"
                                            >
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate hidden md:block" :title="imagem.name">
                                                {{ imagem.name }}
                                            </p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 hidden md:block">
                                                {{ formatFileSize(imagem.size) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
import { useConfirm } from '@/composables/useConfirm'
import { useNotifications } from '@/composables/useNotifications'
import axios from 'axios'

const props = defineProps({
    relatorio: Object,
    equipamentosTeste: Array,
    podeAtualizar: Boolean,
    ehAutor: Boolean,
    podeEditar: Boolean,
    podeExcluir: Boolean,
    tempoRestanteExclusao: Number,
})

const equipamentosTeste = props.equipamentosTeste

const { confirmDelete } = useConfirm()
const { success, error } = useNotifications()

const imageModal = ref({
    show: false,
    image: null,
    index: 0,
    images: [] // novo: array de imagens da galeria atual
})

// Estado dos modais
const modalProgresso = ref({
    show: false,
    progresso: props.relatorio.progresso || 0,
    descricao: '',
    imagens: [],
    loading: false
})

// Estado do drag and drop
const dragActive = ref(false)

// Estado da timeline
const timelineExpandida = ref(false)

// Funções para modal de imagens
const openImageModal = (image, index) => {
    imageModal.value = {
        show: true,
        image: image,
        index: index,
        images: props.relatorio.images || []
    }
}
// Função para abrir modal de imagem da galeria principal
const openImageModalHistorico = (imagens, index) => {
    imageModal.value = {
        show: true,
        image: imagens[index],
        index: index,
        images: imagens
    }
}

const closeImageModal = () => {
    imageModal.value = {
        show: false,
        image: null,
        index: 0
    }
}

const previousImage = () => {
    if (imageModal.value.index > 0) {
        imageModal.value.index--
        imageModal.value.image = imageModal.value.images[imageModal.value.index]
    }
}

const nextImage = () => {
    if (imageModal.value.index < imageModal.value.images.length - 1) {
        imageModal.value.index++
        imageModal.value.image = imageModal.value.images[imageModal.value.index]
    }
}

// Funções de formatação
const formatarData = (dateString) => {
    if (!dateString) return 'Não informada'
    // Se já vier no formato d/m/Y, apenas retorna
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateString)) {
        return dateString
    }
    // Se vier no formato 'YYYY-MM-DDTHH:mm:ss' ou 'YYYY-MM-DDTHH:mm:ssZ', extrai só a parte da data
    const match = dateString.match(/^(\d{4})-(\d{2})-(\d{2})/)
    if (match) {
        const [, ano, mes, dia] = match
        return `${dia}/${mes}/${ano}`
    }
    return dateString // Se não bater nenhum formato, retorna como veio
}

const formatFileSize = (bytes) => {
    if (!bytes) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}



// Funções para modal de progresso
const abrirModalProgresso = () => {
    modalProgresso.value.show = true
    modalProgresso.value.progresso = props.relatorio.progresso
}

const fecharModalProgresso = () => {
    // Limpar URLs de objetos antes de fechar para evitar vazamentos de memória
    modalProgresso.value.imagens.forEach(imagem => {
        if (imagem && imagem.preview) {
            URL.revokeObjectURL(imagem.preview)
        }
    })
    
    modalProgresso.value.show = false
    modalProgresso.value.progresso = props.relatorio.progresso
    modalProgresso.value.descricao = ''
    modalProgresso.value.imagens = []
}

const adicionarImagemProgresso = (event) => {
    const files = Array.from(event.target.files)
    processarArquivos(files)
    
    // Limpar o input para permitir selecionar o mesmo arquivo novamente
    event.target.value = ''
}

const removerImagemProgresso = (index) => {
    // Limpar URL object se existir para evitar vazamentos de memória
    const imagem = modalProgresso.value.imagens[index]
    if (imagem && imagem.preview) {
        URL.revokeObjectURL(imagem.preview)
    }
    
    modalProgresso.value.imagens.splice(index, 1)
}

// Função para criar preview seguro das imagens
const getImagePreview = (arquivo) => {
    if (arquivo.preview) {
        return arquivo.preview
    }
    
    try {
        arquivo.preview = URL.createObjectURL(arquivo)
        return arquivo.preview
    } catch (error) {
        console.error('Erro ao criar preview da imagem:', error)
        return null
    }
}

// Função para tratar erro de carregamento de imagens
const handleImageError = (index) => {
    console.error(`Erro ao carregar preview da imagem ${index}`)
    const imagem = modalProgresso.value.imagens[index]
    if (imagem && imagem.preview) {
        URL.revokeObjectURL(imagem.preview)
        imagem.preview = null
    }
}

// Funções para drag and drop
const handleDragOver = (event) => {
    event.preventDefault()
    dragActive.value = true
}

const handleDragLeave = (event) => {
    event.preventDefault()
    // Só desativar se o cursor saiu da área de drop
    if (!event.currentTarget.contains(event.relatedTarget)) {
        dragActive.value = false
    }
}

const handleDrop = (event) => {
    event.preventDefault()
    dragActive.value = false
    
    const files = Array.from(event.dataTransfer.files)
    processarArquivos(files)
}

// Função unificada para processar arquivos (tanto do input quanto do drop)
const processarArquivos = (files) => {
    const arquivosValidos = []
    
    for (const arquivo of files) {
        // Verificar tipo de arquivo
        if (!arquivo.type.startsWith('image/')) {
            error(`O arquivo "${arquivo.name}" não é uma imagem válida.`)
            continue
        }
        
        // Verificar tamanho (10MB max)
        if (arquivo.size > 10 * 1024 * 1024) {
            error(`O arquivo "${arquivo.name}" é muito grande. Tamanho máximo: 10MB`)
            continue
        }
        
        arquivosValidos.push(arquivo)
    }
    
    if (arquivosValidos.length > 0) {
        modalProgresso.value.imagens.push(...arquivosValidos)
        success(`${arquivosValidos.length} imagem(ns) adicionada(s) com sucesso!`)
    }
}

const atualizarProgresso = async () => {
    if (!modalProgresso.value.descricao.trim()) {
        alert('Descrição é obrigatória')
        return
    }

    if (modalProgresso.value.progresso < props.relatorio.progresso) {
        alert('O progresso não pode ser menor que o atual')
        return
    }

    modalProgresso.value.loading = true
    try {
        const formData = new FormData()
        formData.append('relatorio_id', props.relatorio.id)
        formData.append('progresso_novo', modalProgresso.value.progresso)
        formData.append('descricao', modalProgresso.value.descricao)
        // status_novo: se progresso 100, "Concluída", senão "Em Andamento"
        const statusNovo = (modalProgresso.value.progresso >= 100) ? 'Concluída' : 'Em Andamento'
        formData.append('status_novo', statusNovo)
        modalProgresso.value.imagens.forEach((imagem, index) => {
            formData.append(`imagens[${index}]`, imagem)
        })

        await axios.post(`/relatorios/${props.relatorio.id}/atualizar-progresso`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        
        success('Progresso atualizado com sucesso!')
        fecharModalProgresso()
        // Recarregar a página para atualizar os dados
        window.location.reload()
    } catch (error) {
        error('Erro ao atualizar progresso')
    } finally {
        modalProgresso.value.loading = false
    }
}



// Funções para estilização baseadas no formulário
const getProgressBarClass = (progresso) => {
    if (progresso === 0) return 'bg-red-400 dark:bg-red-500'
    if (progresso >= 1 && progresso <= 99) return 'bg-gradient-to-r from-red-400 via-yellow-400 to-green-400'
    if (progresso === 100) return 'bg-green-400 dark:bg-green-500'
    return 'bg-gray-400 dark:bg-gray-500'
}

// Funções para timeline
const getTimelineIconClass = (progresso) => {
    if (progresso === 100) {
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

const getStatusBadgeClass = (status) => {
    const classes = {
        'Aberta': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        'Em Andamento': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        'Concluída': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        'Cancelada': 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
    }
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}

const getStatusInputClass = (status) => {
    const classes = {
        'Aberta': 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-300',
        'Em Andamento': 'border-yellow-300 dark:border-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-900 dark:text-yellow-300',
        'Concluída': 'border-green-300 dark:border-green-600 bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-300',
        'Cancelada': 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/20 text-gray-900 dark:text-gray-300'
    }
    return classes[status] || 'border-gray-300 dark:border-gray-600'
}

const getStatusIndicatorClass = (status) => {
    const classes = {
        'Aberta': 'bg-red-500',
        'Em Andamento': 'bg-yellow-500',
        'Concluída': 'bg-green-500',
        'Cancelada': 'bg-gray-500'
    }
    return classes[status] || 'bg-gray-500'
}

// Função para confirmar exclusão
const confirmarExclusao = async () => {
    const confirmed = await confirmDelete(
        'Tem certeza que deseja excluir este relatório?',
        'Esta ação não pode ser desfeita. Todos os dados do relatório, incluindo imagens, serão permanentemente removidos.'
    )
    
    if (confirmed) {
        router.delete(route('relatorios.destroy', props.relatorio.id), {
            onSuccess: () => {
                success('Relatório excluído com sucesso!')
            },
            onError: () => {
                error('Erro ao excluir relatório. Tente novamente.')
            }
        })
    }
}
</script> 
