{{-- resources/views/boards/show.blade.php --}}
@extends('layouts.app')

@section('title', $board->name)

@section('content')
    <div class="py-6" id="kanban-board" data-board-id="{{ $board->id }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Cabeçalho do Quadro --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-4 h-4 rounded-full" style="background-color: {{ $board->color }}"></div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $board->name }}</h1>
                                @if ($board->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $board->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            {{-- Filtros e Busca --}}
                            <button onclick="openSearchModal()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Buscar
                            </button>

                            {{-- Exportar --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Exportar
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-50">
                                    <a href="{{ route('export.excel', $board) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        Excel (.xlsx)
                                    </a>
                                    <a href="{{ route('export.csv', $board) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        CSV
                                    </a>
                                    <a href="{{ route('export.pdf', $board) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        PDF
                                    </a>
                                </div>
                            </div>

                            {{-- Gerenciar Usuários --}}
                            @if ($board->isAdmin(auth()->user()))
                                <button onclick="openInviteModal()"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Convidar
                                </button>
                            @endif

                            @if ($board->isAdmin(auth()->user()))
                                <button onclick="openMembersModal()"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Gerenciar Membros
                                </button>
                            @endif


                            {{-- Adicionar Coluna --}}
                            @if ($board->canEdit(auth()->user()))
                                <button onclick="openColumnModal()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Nova Coluna
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Membros do Quadro --}}
                    <div class="mt-4 flex items-center space-x-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Membros:</span>
                        <div class="flex -space-x-2">
                            {{-- Dono --}}
                            @if ($board->owner->avatar)
                                <img src="{{ Storage::url($board->owner->avatar) }}" alt="{{ $board->owner->name }}"
                                    title="{{ $board->owner->name }} (Dono)"
                                    class="h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-800">
                            @else
                                <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-semibold ring-2 ring-white dark:ring-gray-800"
                                    title="{{ $board->owner->name }} (Dono)">
                                    {{ substr($board->owner->name, 0, 1) }}
                                </div>
                            @endif

                            {{-- Membros --}}
                            @foreach ($board->users->take(5) as $user)
                                @if ($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                        title="{{ $user->name }}"
                                        class="h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-800">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center text-white text-xs font-semibold ring-2 ring-white dark:ring-gray-800"
                                        title="{{ $user->name }}">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            @endforeach

                            @if ($board->users->count() > 5)
                                <div
                                    class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 text-xs font-semibold ring-2 ring-white dark:ring-gray-800">
                                    +{{ $board->users->count() - 5 }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quadro Kanban --}}
            <div class="overflow-x-auto pb-4">
                <div class="inline-flex space-x-4 min-h-screen" id="columns-container">
                    @foreach ($board->columns as $column)
                        <div class="column-wrapper flex-shrink-0 w-80" data-column-id="{{ $column->id }}">
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                                {{-- Cabeçalho da Coluna --}}
                                <div
                                    class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 rounded-t-lg">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 rounded-full"
                                                style="background-color: {{ $column->color }}">
                                            </div>
                                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $column->name }}
                                            </h3>
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                                {{ $column->tasks->count() }}
                                                @if ($column->limit)
                                                    / {{ $column->limit }}
                                                @endif
                                            </span>
                                        </div>

                                        @if ($board->canEdit(auth()->user()))
                                            <div class="flex items-center space-x-1">
                                                <button onclick="openTaskModal({{ $column->id }})"
                                                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded"
                                                    title="Adicionar tarefa">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                                <button
                                                    onclick="editColumn({{ $column->id }}, '{{ $column->name }}', '{{ $column->color }}', {{ $column->limit ?? 'null' }})"
                                                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded"
                                                    title="Editar coluna">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Lista de Tarefas --}}
                                <div class="p-4 space-y-3 min-h-[200px] tasks-container"
                                    data-column-id="{{ $column->id }}"
                                    data-column-name="{{ strtolower($column->name) }}">
                                    @foreach ($column->tasks as $task)
                                        @include('tasks.card', ['task' => $task])
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Tarefa (Criar / Editar / Visualizar) --}}
    <div id="task-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div
            class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white dark:bg-gray-800 mb-20">
            <div class="flex items-center justify-between mb-4">
                <h3 id="task-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">Nova Tarefa</h3>
                <button onclick="closeTaskModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col md:flex-row gap-6">
                {{-- Coluna Principal: Detalhes da Tarefa --}}
                <div class="flex-1">
                    <form id="task-form" onsubmit="submitTask(event)">
                        <input type="hidden" id="task-column-id" name="column_id">
                        <input type="hidden" id="task-id" name="task_id">

                        <div class="space-y-4">
                            {{-- Título --}}
                            <div>
                                <label for="task-title"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Título *
                                </label>
                                <input type="text" id="task-title" name="title" required
                                    class="task-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            {{-- Descrição --}}
                            <div>
                                <label for="task-description"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Descrição
                                </label>
                                <textarea id="task-description" name="description" rows="3"
                                    class="task-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                            </div>

                            {{-- Prioridade e Data --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="task-priority"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Prioridade
                                    </label>
                                    <select id="task-priority" name="priority"
                                        class="task-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                        <option value="low">Baixa</option>
                                        <option value="medium" selected>Média</option>
                                        <option value="high">Alta</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="task-due-date"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Data de Vencimento
                                    </label>
                                    <input type="date" id="task-due-date" name="due_date"
                                        class="task-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>

                            {{-- Tags --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tags
                                </label>
                                <div id="tags-container" class="flex flex-wrap gap-2">
                                    {{-- Tags serão carregadas via JavaScript --}}
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between items-center">
                            <button type="button" id="delete-task-btn" onclick="deleteTask()"
                                class="hidden text-red-600 hover:text-red-700 text-sm font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Excluir
                            </button>

                            <div class="flex space-x-3 ml-auto">
                                <button type="button" onclick="closeTaskModal()"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Cancelar
                                </button>
                                <button type="submit" id="task-submit-btn"
                                    class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                    Criar Tarefa
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Coluna Lateral: Comentários (Aparece apenas na edição) --}}
                <div id="comments-section"
                    class="hidden w-full md:w-1/3 border-t md:border-t-0 md:border-l border-gray-200 dark:border-gray-700 pt-4 md:pt-0 md:pl-6">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Comentários</h4>

                    {{-- Lista de Comentários --}}
                    <div id="comments-list" class="space-y-4 max-h-[400px] overflow-y-auto mb-4 pr-1">
                        {{-- Carregado via JS --}}
                    </div>

                    {{-- Form de Novo Comentário --}}
                    <div>
                        <textarea id="new-comment" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Escreva um comentário..."></textarea>
                        <button onclick="submitComment()"
                            class="mt-2 w-full px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                            Comentar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Convite com Busca de Usuários --}}
    <div id="invite-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Convidar Usuário</h3>
                <button onclick="closeInviteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="invite-form" onsubmit="submitInvite(event)">
                <div class="space-y-4">
                    {{-- Campo de Busca de Usuário --}}
                    <div class="relative">
                        <label for="user-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Buscar Usuário *
                        </label>
                        <input type="text" id="user-search" autocomplete="off"
                            placeholder="Digite o nome ou email..." oninput="searchUsersForInvite(this.value)"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">

                        {{-- Lista de resultados --}}
                        <div id="user-search-results"
                            class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        </div>

                        {{-- Usuário selecionado --}}
                        <div id="selected-user"
                            class="hidden mt-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div id="selected-user-avatar"
                                        class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                    </div>
                                    <div>
                                        <p id="selected-user-name" class="font-medium text-gray-900 dark:text-white"></p>
                                        <p id="selected-user-email" class="text-sm text-gray-500 dark:text-gray-400"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="clearSelectedUser()"
                                    class="text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="invite-email" name="email" required>
                    </div>

                    {{-- Permissão --}}
                    <div>
                        <label for="invite-role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Permissão *
                        </label>
                        <select id="invite-role" name="role" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="viewer">👁️ Visualizador - Apenas leitura</option>
                            <option value="editor">✏️ Editor - Pode editar tarefas</option>
                            <option value="admin">⚙️ Administrador - Controle total</option>
                        </select>
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                O usuário receberá uma notificação e terá acesso imediato ao quadro.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeInviteModal()"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" id="invite-submit-btn" disabled
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Enviar Convite
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- Modal de Gerenciamento de Membros --}}
    <div id="members-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Gerenciar Membros</h3>
                <button onclick="closeMembersModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Botão de Convidar --}}
            <div class="mb-4">
                <button onclick="closeMembersModal(); openInviteModal();"
                    class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:border-indigo-500 dark:hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Convidar Novo Membro
                </button>
            </div>

            {{-- Loading --}}
            <div id="members-loading" class="text-center py-8">
                <svg class="animate-spin h-8 w-8 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Carregando membros...</p>
            </div>

            {{-- Lista de Membros --}}
            <div id="members-list" class="hidden space-y-2 max-h-96 overflow-y-auto">
                {{-- Será preenchido via JavaScript --}}
            </div>
        </div>
    </div>

    {{-- Modal de Criação/Edição de Coluna --}}
    <div id="column-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 id="column-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">Criar Nova Coluna
                </h3>
                <button onclick="closeColumnModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Visualização das colunas atuais --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Colunas Atuais
                </label>
                <div id="current-columns-preview" class="flex space-x-2 overflow-x-auto pb-2">
                    @foreach ($board->columns->sortBy('order') as $col)
                        <div class="flex-shrink-0 px-3 py-2 rounded-lg text-xs font-medium text-white"
                            style="background-color: {{ $col->color }}">
                            {{ $col->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            <form id="column-form" onsubmit="submitColumn(event)">
                <input type="hidden" id="column-id" name="column_id">

                <div class="space-y-4">
                    {{-- Nome da Coluna --}}
                    <div>
                        <label for="column-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nome da Coluna *
                        </label>
                        <input type="text" id="column-name" name="name" required
                            placeholder="Ex: Em Revisão, Aguardando..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    {{-- Posição --}}
                    <div id="column-position-container">
                        <label for="column-position"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Inserir Após *
                        </label>
                        <select id="column-position" name="position"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="0">📍 No início (primeira coluna)</option>
                            @foreach ($board->columns->sortBy('order') as $col)
                                <option value="{{ $col->order }}">Após "{{ $col->name }}"</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Cor da Coluna --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cor da Coluna
                        </label>
                        <div class="grid grid-cols-8 gap-2">
                            @php
                                $columnColors = [
                                    '#EF4444' => 'Vermelho',
                                    '#F59E0B' => 'Laranja',
                                    '#10B981' => 'Verde',
                                    '#3B82F6' => 'Azul',
                                    '#8B5CF6' => 'Roxo',
                                    '#EC4899' => 'Rosa',
                                    '#06B6D4' => 'Ciano',
                                    '#6B7280' => 'Cinza',
                                ];
                            @endphp
                            @foreach ($columnColors as $hex => $name)
                                <label class="cursor-pointer" title="{{ $name }}">
                                    <input type="radio" name="color" value="{{ $hex }}"
                                        class="hidden column-color-radio" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="w-8 h-8 rounded-lg transition-all hover:scale-110 flex items-center justify-center column-color-option {{ $loop->first ? 'ring-2 ring-offset-2 ring-gray-900 dark:ring-white' : '' }}"
                                        style="background-color: {{ $hex }};">
                                        <svg class="checkmark w-4 h-4 text-white {{ $loop->first ? '' : 'hidden' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Limite de tarefas (WIP) --}}
                    <div>
                        <label for="column-limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Limite de Tarefas (WIP)
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <input type="number" id="column-limit" name="limit" min="0" placeholder="Sem limite"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Deixe vazio ou 0 para sem limite
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-between items-center">
                    <button type="button" id="delete-column-btn" onclick="deleteColumn()"
                        class="hidden text-red-600 hover:text-red-700 text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Excluir
                    </button>

                    <div class="flex space-x-3 ml-auto">
                        <button type="button" onclick="closeColumnModal()"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancelar
                        </button>
                        <button type="submit" id="submit-column-btn"
                            class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            Criar Coluna
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal de Comentário para Conclusão --}}
    <div id="completion-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Concluir Tarefa</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Para concluir esta tarefa, é necessário adicionar um comentário explicando a resolução.
            </p>

            <form id="completion-form" onsubmit="submitCompletion(event)">
                <input type="hidden" id="completion-task-id">
                <input type="hidden" id="completion-column-id">
                <input type="hidden" id="completion-order">

                <div class="mb-4">
                    <label for="completion-comment"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Comentário Obrigatório *
                    </label>
                    <textarea id="completion-comment" name="comment" rows="3" required placeholder="Descreva o que foi feito..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompletionModal()"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Concluir Tarefa
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script type="module">
        // O Echo já está configurado no bootstrap.js]



        // Conecta ao canal do quadro
        const boardId = {{ $board->id }};
        const isBoardAdmin = {{ $board->isAdmin(auth()->user()) ? 'true' : 'false' }};
        const currentUserId = {{ auth()->id() }};

        Echo.join(`board.${boardId}`)
            .here((users) => {
                console.log('Usuários online:', users);
                updateOnlineUsers(users);
            })
            .joining((user) => {
                console.log(user.name + ' entrou no quadro');
                showToast(user.name + ' entrou no quadro', 'info');
            })
            .leaving((user) => {
                console.log(user.name + ' saiu do quadro');
                showToast(user.name + ' saiu do quadro', 'info');
            })
            .listen('TaskCreated', (e) => {
                console.log('Nova tarefa criada:', e.task);
                addTaskToColumn(e.task);
                showToast('Nova tarefa criada', 'success');
            })
            .listen('TaskUpdated', (e) => {
                console.log('Tarefa atualizada:', e.task);
                updateTaskCard(e.task);
                showToast('Tarefa atualizada', 'info');
            })
            .listen('TaskMoved', (e) => {
                console.log('Tarefa movida:', e.task);
                moveTaskCard(e.task, e.old_column_id);
            })
            .listen('ColumnUpdated', (e) => {
                console.log('Colunas atualizadas');
                showToast('Colunas atualizadas', 'info');
                setTimeout(() => location.reload(), 1000);
            })
            .error((error) => {
                console.error('Erro no Echo:', error);
            });

        // Função para atualizar usuários online
        function updateOnlineUsers(users) {
            // Implementar visualização de usuários online
            console.log(`${users.length} usuários online`);
        }

        // Inicializa SortableJS para drag & drop
        document.addEventListener('DOMContentLoaded', function() {
            initializeSortable();
            loadTags();
        });

        let sortableInstances = [];

        function initializeSortable() {
            const tasksContainers = document.querySelectorAll('.tasks-container');

            tasksContainers.forEach(container => {
                const sortable = new Sortable(container, {
                    group: 'tasks',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    handle: '.task-card',
                    onEnd: function(evt) {
                        const itemEl = evt.item;
                        const taskId = itemEl.dataset.taskId;
                        const toColumn = evt.to;
                        const fromColumn = evt.from;

                        const newColumnId = toColumn.dataset.columnId;
                        const newColumnName = toColumn.dataset.columnName;
                        const newIndex = evt.newIndex;
                        const oldIndex = evt.oldIndex;

                        // Verifica se está movendo para Concluído
                        const isCompleting = ['concluido', 'concluído', 'done', 'completed',
                            'finalizado'
                        ].includes(newColumnName);

                        // Se for para concluir e veio de outra coluna
                        if (isCompleting && toColumn !== fromColumn) {
                            // Reverte visualmente por enquanto para evitar estado inconsistente se cancelar
                            // Vamos abrir o modal e só mover de verdade se confirmar
                            // Mas o Sortable JÁ moveu no DOM. Então, aceitamos o movimento visual PROVISÓRIO?
                            // Melhor: deixamos visualmente lá. Se cancelar, movemos de volta.

                            openCompletionModal(taskId, newColumnId, newIndex, fromColumn, oldIndex,
                                itemEl);
                        } else {
                            // Movimento normal
                            moveTask(taskId, newColumnId, newIndex);
                        }
                    }
                });
                sortableInstances.push(sortable);
            });
        }

        // Estado temporário para reversão
        let pendingCompletion = null;

        function openCompletionModal(taskId, columnId, order, fromColumn, oldIndex, itemEl) {
            pendingCompletion = {
                taskId,
                columnId,
                order,
                fromColumn,
                oldIndex,
                itemEl
            };

            document.getElementById('completion-task-id').value = taskId;
            document.getElementById('completion-column-id').value = columnId;
            document.getElementById('completion-order').value = order;
            document.getElementById('completion-comment').value = '';

            document.getElementById('completion-modal').classList.remove('hidden');
        }

        window.closeCompletionModal = function() {
            document.getElementById('completion-modal').classList.add('hidden');

            // Se fechou sem enviar (cancelou), reverte o movimento
            if (pendingCompletion) {
                const {
                    fromColumn,
                    itemEl,
                    oldIndex
                } = pendingCompletion;

                // Mover de volta para a posição original
                const children = Array.from(fromColumn.children);
                if (oldIndex >= children.length) {
                    fromColumn.appendChild(itemEl);
                } else {
                    fromColumn.insertBefore(itemEl, children[oldIndex]);
                }

                showToast('Ação cancelada: tarefa retornada para a coluna original.', 'info');
                pendingCompletion = null;
            }
        };

        window.submitCompletion = async function(event) {
            event.preventDefault();

            if (!pendingCompletion) return;

            const comment = document.getElementById('completion-comment').value;
            const {
                taskId,
                columnId,
                order
            } = pendingCompletion;

            // Limpa pendencia pois vamos processar (se falhar, o reload cuida)
            pendingCompletion = null;

            document.getElementById('completion-modal').classList.add('hidden');

            await moveTask(taskId, columnId, order, comment);
        };

        async function moveTask(taskId, columnId, order, comment = null) {
            try {
                const payload = {
                    column_id: columnId,
                    order: order
                };

                if (comment) {
                    payload.comment = comment;
                }

                const response = await fetch(`/tasks/${taskId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Tarefa movida com sucesso', 'success');
                } else {
                    // Se o servidor recusar (ex: erro de permissão ou falta de comentário validado lá), recarrega
                    showToast(data.message || 'Erro ao mover tarefa', 'error');
                    location.reload();
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro ao mover tarefa', 'error');
                location.reload();
            }
        }

        // Funções para modais de Tarefa (Visualização/Edição)
        window.openTaskModal = async function(columnId, taskId = null) {
            const modalTitle = document.getElementById('task-modal-title');
            const submitBtn = document.getElementById('task-submit-btn');
            const deleteBtn = document.getElementById('delete-task-btn');
            const commentsSection = document.getElementById('comments-section');
            const tagsContainer = document.getElementById('tags-container');
            const formInputs = document.querySelectorAll('.task-input, input[name="tags[]"]');

            // Reseta formulário
            document.getElementById('task-form').reset();
            document.querySelectorAll('input[name="tags[]"]').forEach(cb => cb.checked = false);
            document.getElementById('comments-list').innerHTML = ''; // Limpa comentários

            // Habilita campos por padrão
            formInputs.forEach(input => input.disabled = false);
            document.getElementById('new-comment').disabled = false;
            submitBtn.classList.remove('hidden');
            deleteBtn.classList.remove('hidden');

            if (taskId) {
                // MODO EDIÇÃO
                modalTitle.textContent = 'Detalhes da Tarefa';
                submitBtn.textContent = 'Salvar Alterações';
                commentsSection.classList.remove('hidden');
                document.getElementById('task-id').value = taskId;
                document.getElementById('task-column-id').value = '';

                // Carregar dados da tarefa
                try {
                    const response = await fetch(`/tasks/${taskId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const result = await response.json();

                    if (result.success) {
                        const task = result.task;

                        // Preenche campos
                        document.getElementById('task-title').value = task.title;
                        document.getElementById('task-description').value = task.description || '';
                        document.getElementById('task-priority').value = task.priority;
                        document.getElementById('task-due-date').value = task.due_date ? task.due_date.split('T')[
                            0] : '';

                        // Preenche tags
                        if (task.tags) {
                            task.tags.forEach(tag => {
                                const cb = document.querySelector(
                                `input[name="tags[]"][value="${tag.id}"]`);
                                if (cb) cb.checked = true;
                            });
                        }

                        // Verifica permissões (Tarefa Concluída e Não-Admin)
                        if (task.completed_at && !isBoardAdmin) {
                            modalTitle.textContent = 'Detalhes da Tarefa (Concluída)';
                            formInputs.forEach(input => input.disabled = true);
                            submitBtn.classList.add('hidden');
                            deleteBtn.classList.add('hidden');
                            // Se quiser bloquear comentários também:
                            // document.getElementById('new-comment').disabled = true;
                        }

                        // Renderiza Comentários
                        renderComments(task.comments || []);

                        document.getElementById('task-modal').classList.remove('hidden');
                    } else {
                        showToast('Erro ao carregar tarefa.', 'error');
                    }
                } catch (e) {
                    console.error("Erro ao carregar tarefa", e);
                    showToast('Erro ao carregar detalhes.', 'error');
                }
            } else {
                // MODO CRIAÇÃO
                modalTitle.textContent = 'Nova Tarefa';
                submitBtn.textContent = 'Criar Tarefa';
                deleteBtn.classList.add('hidden');
                commentsSection.classList.add('hidden');
                document.getElementById('task-column-id').value = columnId;
                document.getElementById('task-id').value = '';

                document.getElementById('task-modal').classList.remove('hidden');
            }
        };

        window.closeTaskModal = function() {
            document.getElementById('task-modal').classList.add('hidden');
            document.getElementById('task-form').reset();
        };

        window.submitTask = async function(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            const taskId = document.getElementById('task-id').value;
            const isEdit = !!taskId;

            // Adiciona tags selecionadas
            const selectedTags = Array.from(document.querySelectorAll('input[name="tags[]"]:checked'))
                .map(input => input.value);
            data.tags = selectedTags;

            // Button loading state
            const submitBtn = document.getElementById('task-submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = isEdit ? 'Salvando...' : 'Criando...';
            submitBtn.disabled = true;

            try {
                const url = isEdit ? `/tasks/${taskId}` : '/tasks';
                const method = isEdit ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showToast(isEdit ? 'Tarefa atualizada!' : 'Tarefa criada com sucesso', 'success');
                    closeTaskModal();

                    if (isEdit) {
                        updateTaskCard(result.task); // Atualiza no DOM sem reload
                    } else {
                        addTaskToColumn(result.task); // Adiciona no DOM sem reload
                    }
                } else {
                    showToast(result.message || 'Erro ao salvar tarefa', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                showToast('Erro ao processar requisição', 'error');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        };

        window.deleteTask = async function() {
            const taskId = document.getElementById('task-id').value;
            if (!taskId) return;

            if (!confirm('Tem certeza que deseja excluir esta tarefa permanentemente?')) return;

            const btn = document.getElementById('delete-task-btn');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.textContent = '...';

            try {
                const response = await fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Tarefa excluída.', 'success');
                    closeTaskModal();
                    // Remove do DOM
                    const el = document.querySelector(`[data-task-id="${taskId}"]`);
                    if (el) {
                        const colId = el.closest('.tasks-container').dataset.columnId;
                        el.remove();
                        updateColumnCount(colId);
                    }
                } else {
                    showToast(result.message || 'Erro ao excluir.', 'error');
                }
            } catch (e) {
                showToast('Erro ao excluir.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = original;
            }
        };

        // Comentários
        function renderComments(comments) {
            const container = document.getElementById('comments-list');

            if (!comments || comments.length === 0) {
                container.innerHTML =
                    '<p class="text-sm text-gray-500 italic text-center py-2">Nenhum comentário ainda.</p>';
                return;
            }

            container.innerHTML = comments.map(comment => `
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3 text-sm">
                    <div class="flex justify-between items-start mb-1">
                        <span class="font-semibold text-gray-900 dark:text-white">${comment.user ? comment.user.name : 'Usuário'}</span>
                        <span class="text-xs text-gray-500">${formatDate(comment.created_at)}</span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">${comment.content}</p>
                </div>
            `).join('');

            // Scroll to bottom
            container.scrollTop = container.scrollHeight;
        }

        window.submitComment = async function() {
            const taskId = document.getElementById('task-id').value;
            const content = document.getElementById('new-comment').value;

            if (!content.trim()) return;

            const btn = document.querySelector('#comments-section button');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Enviando...';

            try {
                // Supondo rota /tasks/{task}/comments definida em web.php:
                // Route::post('/tasks/{task}/comments', [CommentController::class, 'store']);
                const response = await fetch(`/tasks/${taskId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content
                    })
                });

                const result = await response.json();

                if (result.success) {
                    document.getElementById('new-comment').value = '';

                    const list = document.getElementById('comments-list');
                    if (list.querySelector('p.text-center')) list.innerHTML = ''; // Remove msg "nenhum comentário"

                    const comment = result.comment;
                    list.insertAdjacentHTML('beforeend', `
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded p-3 text-sm animate-fade-in">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-semibold text-gray-900 dark:text-white">${comment.user.name}</span>
                                <span class="text-xs text-gray-500">Agora</span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">${comment.content}</p>
                        </div>
                    `);
                    list.scrollTop = list.scrollHeight;

                    // Atualiza contador no card da tarefa (opcional, mas bom)
                    const card = document.querySelector(`[data-task-id="${taskId}"]`);
                    // ... (implementar atualização visual do contador de comentários seria um bonus, mas o loadComments na próxima abertura resolve)

                } else {
                    showToast('Erro ao enviar comentário.', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Erro ao enviar comentário.', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        };

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        }
    </script>
@endpush


@push('styles')
    <style>
        .sortable-ghost {
            opacity: 0.4;
        }

        .sortable-drag {
            opacity: 0.8;
            transform: rotate(2deg);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush
