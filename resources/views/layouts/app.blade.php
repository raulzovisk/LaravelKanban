{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>@yield('title', config('app.name'))</title>

    {{-- Aplica dark mode ANTES do CSS carregar para evitar flash branco --}}
    <script>
        (function() {
                // Primeiro verifica localStorage (preferência do usuário)
                const storedDarkMode = localStorage.getItem('darkMode');
                if (storedDarkMode === 'true') {
                    document.documentElement.classList.add('dark');
                } else if (storedDarkMode === null) {
                    // Se não tiver preferência salva, usa o valor do servidor (usuário autenticado)
                    @auth
                    @if (auth()->user()->dark_mode)
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('darkMode', 'true');
                    @endif
                @endauth
            }
        })();
    </script>

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Scripts adicionais --}}
    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200">

    {{-- Navegação --}}
    @include('layouts.navigation')

    {{-- Conteúdo Principal --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Toast de Notificações --}}
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    {{-- Modal Container --}}
    <div id="modal-container"></div>

    {{-- Scripts --}}
    @stack('scripts')

    {{-- Feedback de Sessão --}}
    @if (session('success'))
        <script>
            showToast('{{ session('success') }}', 'success');
        </script>
    @endif

    @if (session('error'))
        <script>
            showToast('{{ session('error') }}', 'error');
        </script>
    @endif
</body>

</html>
