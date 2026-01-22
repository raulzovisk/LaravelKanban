{{-- resources/views/auth/verify-email.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Email - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            {{-- Logo --}}
            <div class="text-center">
                <h1 class="text-4xl font-bold text-indigo-600">📋 Kanban</h1>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Verifique seu email
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enviamos um link de verificação para seu email.
                </p>
            </div>
            
            {{-- Mensagem de Sucesso --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            
            {{-- Card --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="text-center mb-6">
                    <svg class="mx-auto h-16 w-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                
                <p class="text-gray-600 text-center mb-6">
                    Clique no link que enviamos para <strong>{{ auth()->user()->email }}</strong> para verificar sua conta.
                </p>
                
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Reenviar Email de Verificação
                    </button>
                </form>
                
                <div class="mt-4 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
            
            {{-- Link para Dashboard --}}
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Ir para o Dashboard →
                </a>
            </div>
        </div>
    </div>
</body>
</html>
