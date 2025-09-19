<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gerenciar Motores
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Cabeçalho da página -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Motores
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Gerencie os motores da empresa
                        </p>
                    </div>
                    <Link
                        :href="route('motores.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Motor
                    </Link>
                </div>

                <!-- Filtros Básicos -->
                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <form @submit.prevent="aplicarFiltros" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Busca Geral -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Busca Geral
                            </label>
                            <input
                                v-model="filtros.busca"
                                type="text"
                                placeholder="Tag, equipamento, fabricante..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            />
                        </div>
                        
                        <!-- Local -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Local
                            </label>
                            <input
                                v-model="filtros.local"
                                type="text"
                                placeholder="Digite o local..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            />
                        </div>
                        
                        <!-- Reserva no Almoxarifado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Reserva no Almoxarifado
                            </label>
                            <input
                                v-model="filtros.reserva_almox"
                                type="text"
                                placeholder="Digite a reserva..."
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            />
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select
                                v-model="filtros.ativo"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            >
                                <option value="">Todos</option>
                                <option value="true">Ativo</option>
                                <option value="false">Inativo</option>
                            </select>
                        </div>
                        
                        <!-- Status de Armazenamento -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status de Armazenamento
                            </label>
                            <select
                                v-model="filtros.armazenamento"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            >
                                <option value="">Todos os status</option>
                                <option value="Almoxarifado">Almoxarifado</option>
                                <option value="Instalado">Instalado</option>
                                <option value="Manutenção">Manutenção</option>
                                <option value="Reserva">Reserva</option>
                                <option value="Descartado">Descartado</option>
                            </select>
                        </div>
                        
                        <!-- Tipo de Equipamento/Modelo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tipo de Equipamento/Modelo
                            </label>
                            <select
                                v-model="filtros.tipo_equipamento_modelo"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            >
                                <option value="">Todos os tipos</option>
                                <option v-for="tipo in tipos" :key="tipo" :value="tipo">
                                    {{ tipo }}
                                </option>
                            </select>
                        </div>
                        
                        <!-- Fabricante -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Fabricante
                            </label>
                            <select
                                v-model="filtros.fabricante"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            >
                                <option value="">Todos os fabricantes</option>
                                <option v-for="fabricante in fabricantes" :key="fabricante" :value="fabricante">
                                    {{ fabricante }}
                                </option>
                            </select>
                        </div>
                        
                        <!-- Botões de Ação -->
                        <div class="flex gap-2 items-end sm:col-span-2 lg:col-span-1">
                            <button
                                type="submit"
                                class="flex-1 inline-flex items-center justify-center px-3 sm:px-4 py-2.5 sm:py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="hidden sm:inline">Filtrar</span>
                                <span class="sm:hidden">Filtrar</span>
                            </button>
                            <button
                                type="button"
                                @click="limparFiltros"
                                :class="temFiltrosAtivos ? 'text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                class="px-3 py-2.5 sm:py-2 rounded-md transition-colors"
                                :title="temFiltrosAtivos ? 'Limpar filtros ativos' : 'Limpar filtros'"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Filtros Avançados -->
                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Filtros Avançados</h4>
                        <button
                            @click="mostrarFiltrosAvancados = !mostrarFiltrosAvancados"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                        >
                            {{ mostrarFiltrosAvancados ? 'Ocultar' : 'Mostrar' }}
                        </button>
                    </div>
                    
                    <div v-if="mostrarFiltrosAvancados" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                        <!-- Potência (kW) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Potência (kW)
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input
                                    v-model="filtros.potencia_min"
                                    type="number"
                                    step="0.01"
                                    placeholder="Mín"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                                <input
                                    v-model="filtros.potencia_max"
                                    type="number"
                                    step="0.01"
                                    placeholder="Máx"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                            </div>
                        </div>
                        
                        <!-- Potência (CV) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Potência (CV)
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input
                                    v-model="filtros.potencia_cv_min"
                                    type="number"
                                    step="0.01"
                                    placeholder="Mín"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                                <input
                                    v-model="filtros.potencia_cv_max"
                                    type="number"
                                    step="0.01"
                                    placeholder="Máx"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                            </div>
                        </div>
                        
                        <!-- Rotação (RPM) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Rotação (RPM)
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input
                                    v-model="filtros.rotacao_min"
                                    type="number"
                                    placeholder="Mín"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                                <input
                                    v-model="filtros.rotacao_max"
                                    type="number"
                                    placeholder="Máx"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                            </div>
                        </div>
                        
                        <!-- Corrente de Placa (A) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Corrente de Placa (A)
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input
                                    v-model="filtros.corrente_placa_min"
                                    type="number"
                                    step="0.01"
                                    placeholder="Mín"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                                <input
                                    v-model="filtros.corrente_placa_max"
                                    type="number"
                                    step="0.01"
                                    placeholder="Máx"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                            </div>
                        </div>
                        
                        <!-- Corrente Configurada (A) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Corrente Configurada (A)
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <input
                                    v-model="filtros.corrente_configurada_min"
                                    type="number"
                                    step="0.01"
                                    placeholder="Mín"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                                <input
                                    v-model="filtros.corrente_configurada_max"
                                    type="number"
                                    step="0.01"
                                    placeholder="Máx"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                                />
                            </div>
                        </div>
                        
                        <!-- Carcaça (Fabricante) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Carcaça (Fabricante)
                            </label>
                            <select
                                v-model="filtros.carcaca_fabricante"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm text-sm"
                            >
                                <option value="">Todas as carcaças</option>
                                <option v-for="carcaca in carcacas" :key="carcaca" :value="carcaca">
                                    {{ carcaca }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contador de Resultados -->
                <div class="mb-4 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                            {{ motores.total }} motor{{ motores.total !== 1 ? 'es' : '' }} encontrado{{ motores.total !== 1 ? 's' : '' }}
                            <span class="hidden sm:inline">{{ motores.data.length < motores.total ? `(mostrando ${motores.data.length} de ${motores.total})` : '' }}</span>
                            <span class="sm:hidden">{{ motores.data.length < motores.total ? `(${motores.data.length}/${motores.total})` : '' }}</span>
                        </p>
                        
                        <!-- Indicador de Filtros Ativos -->
                        <div v-if="temFiltrosAtivos" class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                                </svg>
                                Filtros ativos
                            </span>
                            <button
                                @click="limparFiltros"
                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20"
                                title="Limpar todos os filtros"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grid de Motores -->
                <div v-if="motores.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <div
                        v-for="motor in motores.data"
                        :key="motor.id"
                        class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-200"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ motor.tag }}
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                    {{ motor.equipamento }}
                                </p>
                            </div>
                            <span
                                :class="getReservaAlmoxClass(motor.reserva_almox)"
                                class="px-2 py-1 text-xs rounded-full whitespace-nowrap"
                            >
                                {{ motor.reserva_almox || 'Não Reservado' }}
                            </span>
                        </div>

                        <div class="space-y-1.5 sm:space-y-2 mb-4">
                            <div v-if="motor.local" class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ motor.local }}
                            </div>
                            <div v-if="motor.tipo_equipamento_modelo" class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                {{ motor.tipo_equipamento_modelo }}
                            </div>
                            <div v-if="motor.fabricante" class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ motor.fabricante }}
                            </div>
                            <div v-if="motor.potencia_kw || motor.potencia_cv" class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                {{ motor.potencia_kw ? `${motor.potencia_kw} kW` : `${motor.potencia_cv} CV` }}
                            </div>
                            <div v-if="motor.rotacao" class="flex items-center text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                {{ motor.rotacao }} RPM
                            </div>

                        </div>

                        <div class="flex justify-end space-x-1 sm:space-x-2">
                            <Link
                                :href="route('motores.show', motor.id)"
                                class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                title="Visualizar"
                            >
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </Link>
                            <Link
                                :href="route('motores.edit', motor.id)"
                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 p-1 rounded hover:bg-yellow-50 dark:hover:bg-yellow-900/20"
                                title="Editar"
                            >
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </Link>
                            <button
                                @click="confirmarExclusao(motor)"
                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20"
                                title="Excluir"
                            >
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Estado vazio -->
                <div v-else class="text-center py-8 sm:py-12">
                    <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <h3 class="mt-3 sm:mt-4 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum motor encontrado</h3>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        {{ Object.values(filtros).some(f => f) ? 'Tente ajustar os filtros ou' : 'Comece' }} criando um novo motor.
                    </p>
                    <div class="mt-4 sm:mt-6">
                        <Link
                            :href="route('motores.create')"
                            class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                        >
                            <svg class="w-4 h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Novo Motor
                        </Link>
                    </div>
                </div>

                <!-- Paginação -->
                <div v-if="motores.data.length > 0" class="mt-4 sm:mt-6">
                    <Pagination :links="motores" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { useNotifications } from '@/composables/useNotifications'
import { useConfirm } from '@/composables/useConfirm'

const { success, error } = useNotifications()
const { confirmDelete } = useConfirm()

const props = defineProps({
    motores: Object,
    filtros: Object,
    locais: Array,
    tipos: Array,
    fabricantes: Array,
    carcacas: Array,
})

// Função para salvar filtros no localStorage
const salvarFiltros = (filtrosData) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem('motores_filtros', JSON.stringify(filtrosData))
    }
}

// Função para carregar filtros do localStorage
const carregarFiltros = () => {
    if (typeof window !== 'undefined') {
        const filtrosSalvos = localStorage.getItem('motores_filtros')
        if (filtrosSalvos) {
            return JSON.parse(filtrosSalvos)
        }
    }
    return {}
}

// Função para limpar filtros do localStorage
const limparFiltrosStorage = () => {
    if (typeof window !== 'undefined') {
        localStorage.removeItem('motores_filtros')
    }
}

// Inicializar filtros com dados do servidor ou localStorage
const filtrosIniciais = Object.keys(props.filtros).length > 0 ? props.filtros : carregarFiltros()
const filtros = ref(filtrosIniciais)
const mostrarFiltrosAvancados = ref(false)

// Verificar se há filtros ativos
const temFiltrosAtivos = computed(() => {
    return Object.values(filtros.value).some(valor => 
        valor !== null && valor !== undefined && valor !== ''
    )
})

const aplicarFiltros = () => {
    // Salvar filtros no localStorage antes de aplicar
    salvarFiltros(filtros.value)
    
    router.get(route('motores.index'), filtros.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const limparFiltros = () => {
    filtros.value = {}
    // Limpar filtros do localStorage
    limparFiltrosStorage()
    
    router.get(route('motores.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}

const confirmarExclusao = async (motor) => {
    const confirmed = await confirmDelete(
        'Excluir Motor',
        `Tem certeza que deseja excluir o motor "${motor.tag}"? Esta ação não pode ser desfeita.`
    )

    if (confirmed) {
        router.delete(route('motores.destroy', motor.id), {
            onSuccess: () => {
                success('Motor excluído com sucesso!')
            },
            onError: (errors) => {
                if (errors.delete) {
                    error(errors.delete)
                } else {
                    error('Erro ao excluir motor')
                }
            }
        })
    }
}

const getReservaAlmoxClass = (reservaAlmox) => {
    return reservaAlmox && reservaAlmox.trim() !== ''
        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
        : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400'
}



onMounted(() => {
    // Debounce para busca
    let timeout
    const buscaInput = document.querySelector('input[placeholder="Buscar motores..."]')
    if (buscaInput) {
        buscaInput.addEventListener('input', (e) => {
            clearTimeout(timeout)
            timeout = setTimeout(() => {
                filtros.value.busca = e.target.value
                aplicarFiltros()
            }, 500)
        })
    }
    
    // Aplicar filtros salvos automaticamente se não vierem do servidor
    if (Object.keys(props.filtros).length === 0 && temFiltrosAtivos.value) {
        aplicarFiltros()
    }
})
</script> 