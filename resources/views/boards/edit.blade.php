@extends('layouts.app')

@section('title', 'Editar Quadro')

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Cabeçalho --}}
            <div class="mb-6">
                <div class="flex items-center space-x-4 mb-4">
                    <a href="{{ route('boards.show', $board) }}"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Editar Quadro
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Atualize as configurações do quadro "{{ $board->name }}"
                        </p>
                    </div>
                </div>
            </div>

            {{-- Formulário de Edição (SEPARADO DO DELETE) --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <form method="POST" action="{{ route('boards.update', $board) }}" class="p-6 space-y-6" id="edit-form">
                    @csrf
                    @method('PUT')

                    {{-- Erros de Validação --}}
                    @if ($errors->any())
                        <div
                            class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="font-semibold mb-1">Corrija os seguintes erros:</p>
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Nome do Quadro --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome do Quadro *
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $board->name) }}" required
                            placeholder="Ex: Projeto Website, Tarefas da Semana..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descrição --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descrição
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-normal">(opcional)</span>
                        </label>
                        <textarea id="description" name="description" rows="4" placeholder="Descreva o propósito deste quadro..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $board->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cor do Quadro COM FEEDBACK VISUAL --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Cor do Quadro
                        </label>

                        {{-- Preview da cor selecionada --}}
                        <div id="color-preview"
                            class="mb-4 p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 flex items-center space-x-3">
                            <div id="preview-circle"
                                class="w-8 h-8 rounded-full ring-2 ring-white dark:ring-gray-800 shadow-lg"
                                style="background-color: {{ old('color', $board->color) }};"></div>
                            <span id="preview-text" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                @php
                                    $colorNames = [
                                        '#3B82F6' => 'Azul',
                                        '#10B981' => 'Verde',
                                        '#F59E0B' => 'Laranja',
                                        '#EF4444' => 'Vermelho',
                                        '#8B5CF6' => 'Roxo',
                                        '#EC4899' => 'Rosa',
                                        '#06B6D4' => 'Ciano',
                                        '#6B7280' => 'Cinza',
                                    ];
                                    $currentColor = old('color', $board->color);
                                    echo ($colorNames[$currentColor] ?? 'Cor') . ' selecionado';
                                @endphp
                            </span>
                        </div>

                        <div class="grid grid-cols-4 sm:grid-cols-8 gap-3">
                            @php
                                $colors = [
                                    '#3B82F6' => 'Azul',
                                    '#10B981' => 'Verde',
                                    '#F59E0B' => 'Laranja',
                                    '#EF4444' => 'Vermelho',
                                    '#8B5CF6' => 'Roxo',
                                    '#EC4899' => 'Rosa',
                                    '#06B6D4' => 'Ciano',
                                    '#6B7280' => 'Cinza',
                                ];
                            @endphp
                            @foreach ($colors as $hex => $name)
                                <label class="cursor-pointer" title="{{ $name }}">
                                    <input type="radio" name="color" value="{{ $hex }}"
                                        class="hidden color-radio" data-color-name="{{ $name }}"
                                        {{ old('color', $board->color) === $hex ? 'checked' : '' }}>
                                    <div class="color-option w-12 h-12 rounded-lg transition-all hover:scale-110 hover:shadow-lg flex items-center justify-center relative {{ old('color', $board->color) === $hex ? 'selected' : '' }}"
                                        style="background-color: {{ $hex }};">
                                        <svg class="checkmark w-6 h-6 text-white opacity-0 transition-opacity duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('color')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Visibilidade --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Visibilidade
                        </label>
                        <div class="space-y-3">
                            <label
                                class="flex items-start p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <input type="radio" name="is_public" value="0"
                                    class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                    {{ old('is_public', $board->is_public ? '1' : '0') === '0' ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span class="font-medium text-gray-900 dark:text-white">Privado</span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Apenas você e membros convidados podem ver este quadro
                                    </p>
                                </div>
                            </label>

                            <label
                                class="flex items-start p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <input type="radio" name="is_public" value="1"
                                    class="mt-1 text-indigo-600 focus:ring-indigo-500"
                                    {{ old('is_public', $board->is_public ? '1' : '0') === '1' ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-medium text-gray-900 dark:text-white">Público</span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Qualquer pessoa com o link pode visualizar (somente leitura)
                                    </p>
                                </div>
                            </label>
                        </div>
                        @error('is_public')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Informações Adicionais --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Informações</p>
                                <ul class="space-y-1 text-xs">
                                    <li>• Criado em: {{ $board->created_at->format('d/m/Y H:i') }}</li>
                                    <li>• Última atualização: {{ $board->updated_at->diffForHumans() }}</li>
                                    <li>• Total de tarefas: {{ $board->tasks()->count() }}</li>
                                    <li>• Membros: {{ $board->users()->count() + 1 }} (você +
                                        {{ $board->users()->count() }} convidados)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Botões de Ação (SEPARADOS) --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        {{-- Botão Deletar (FORA DO FORM DE EDIÇÃO) --}}
                        <button type="button" onclick="confirmDelete()"
                            class="px-4 py-2 border border-red-300 dark:border-red-700 rounded-lg text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Deletar Quadro
                        </button>

                        <div class="flex items-center space-x-3">
                            <a href="{{ route('boards.show', $board) }}"
                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Form de Delete SEPARADO (invisível) --}}
            <form id="delete-form" method="POST" action="{{ route('boards.destroy', $board) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Atualiza seleção visual das cores COM FEEDBACK
        document.querySelectorAll('.color-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove seleção de todos
                document.querySelectorAll('.color-option').forEach(opt => {
                    opt.classList.remove('selected', 'ring-4', 'ring-white', 'dark:ring-gray-800',
                        'scale-110');
                    opt.querySelector('.checkmark').style.opacity = '0';
                });

                // Adiciona seleção ao atual
                if (this.checked) {
                    const colorOption = this.nextElementSibling;
                    colorOption.classList.add('selected', 'ring-4', 'ring-white', 'dark:ring-gray-800',
                        'scale-110');
                    colorOption.querySelector('.checkmark').style.opacity = '1';

                    // Atualiza o preview
                    const colorValue = this.value;
                    const colorName = this.dataset.colorName;
                    document.getElementById('preview-circle').style.backgroundColor = colorValue;
                    document.getElementById('preview-text').textContent = colorName + ' selecionado';
                }
            });
        });

        // Inicializa a seleção ao carregar a página
        document.addEventListener('DOMContentLoaded', () => {
            const selectedRadio = document.querySelector('.color-radio:checked');
            if (selectedRadio) {
                const colorOption = selectedRadio.nextElementSibling;
                colorOption.classList.add('selected', 'ring-4', 'ring-white', 'dark:ring-gray-800', 'scale-110');
                colorOption.querySelector('.checkmark').style.opacity = '1';
            }
        });

        // Confirmação de delete SEPARADA
        function confirmDelete() {
            const boardName = '{{ $board->name }}';
            const confirmed = confirm(
                '⚠️ ATENÇÃO!\n\n' +
                'Você está prestes a deletar o quadro "' + boardName + '" e TODAS as suas tarefas.\n\n' +
                'Esta ação NÃO PODE ser desfeita!\n\n' +
                'Tem certeza que deseja continuar?'
            );

            if (confirmed) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .color-option {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .color-option.selected {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush
