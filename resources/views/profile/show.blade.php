{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Cabeçalho --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Meu Perfil</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Gerencie suas informações pessoais e preferências
                </p>
            </div>

            {{-- Avatar e Informações Básicas --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                <div
                    class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 rounded-t-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Avatar</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            @if ($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                    class="h-24 w-24 rounded-full object-cover">
                            @else
                                <div
                                    class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data"
                                class="flex items-center space-x-3">
                                @csrf
                                <input type="file" name="avatar" id="avatar" accept="image/*"
                                    class="block text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-lg file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-indigo-50 file:text-indigo-700
                                          dark:file:bg-indigo-900/30 dark:file:text-indigo-400
                                          hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50">
                                <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                    Atualizar
                                </button>
                            </form>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">JPG, PNG ou GIF. Máximo 2MB.</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informações do Perfil --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
                <div
                    class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 rounded-t-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informações Pessoais</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Nome --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nome
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Timezone --}}
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Fuso Horário
                            </label>
                            <select id="timezone" name="timezone"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="America/Sao_Paulo"
                                    {{ ($user->timezone ?? 'America/Sao_Paulo') === 'America/Sao_Paulo' ? 'selected' : '' }}>
                                    São Paulo (UTC-3)
                                </option>
                                <option value="America/Manaus"
                                    {{ $user->timezone === 'America/Manaus' ? 'selected' : '' }}>
                                    Manaus (UTC-4)
                                </option>
                                <option value="America/Recife"
                                    {{ $user->timezone === 'America/Recife' ? 'selected' : '' }}>
                                    Recife (UTC-3)
                                </option>
                                <option value="America/Fortaleza"
                                    {{ $user->timezone === 'America/Fortaleza' ? 'selected' : '' }}>
                                    Fortaleza (UTC-3)
                                </option>
                                <option value="UTC" {{ $user->timezone === 'UTC' ? 'selected' : '' }}>
                                    UTC
                                </option>
                            </select>
                        </div>

                        {{-- Dark Mode --}}
                        <div class="flex items-center justify-between py-3 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Modo Escuro
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Preferência salva no servidor para uso em múltiplos dispositivos
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="dark_mode" value="1" class="sr-only peer"
                                    {{ $user->dark_mode ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600">
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Alterar Senha --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div
                    class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/50 rounded-t-lg">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Alterar Senha</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        {{-- Senha Atual --}}
                        <div>
                            <label for="current_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Senha Atual
                            </label>
                            <input type="password" id="current_password" name="current_password" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nova Senha --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nova Senha
                            </label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmar Senha --}}
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Confirmar Nova Senha
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                Alterar Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Informações da Conta --}}
            <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>Conta criada em {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
@endsection
