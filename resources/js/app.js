import './bootstrap';

// Função para mostrar toast de notificação
window.showToast = function (message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };

    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };

    const toast = document.createElement('div');
    toast.className = `${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 transform transition-all duration-300 translate-x-0 opacity-100 animate-slide-in`;
    toast.innerHTML = `
        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${icons[type]}
        </svg>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;

    container.appendChild(toast);

    // Remove automaticamente após 5 segundos
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
};

// Função para toggle dark mode
window.toggleDarkMode = async function () {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');

    // Salva no localStorage
    localStorage.setItem('darkMode', isDark);

    // Salva preferência no servidor se estiver autenticado
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        try {
            await fetch('/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                },
                body: JSON.stringify({
                    dark_mode: isDark
                })
            });
        } catch (error) {
            console.error('Erro ao salvar preferência:', error);
        }
    }
};

// Dark mode é carregado síncronamente no head do layout (app.blade.php)
// para evitar flash branco ao trocar de páginas

// Função para marcar notificação como lida
window.markNotificationAsRead = async function (notificationId) {
    try {
        await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    } catch (error) {
        console.error('Erro ao marcar notificação:', error);
    }
};

// Confirmação antes de deletar
window.confirmDelete = function (message = 'Tem certeza que deseja deletar?') {
    return confirm(message);
};

// Fecha modais ao pressionar ESC
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        // Fecha todos os modais visíveis
        document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
            modal.classList.add('hidden');
        });
    }
});

// Listener para notificações em tempo real via WebSocket
document.addEventListener('DOMContentLoaded', function () {
    // Pega o ID do usuário a partir de um elemento no DOM
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    if (!userIdMeta) {
        console.log('Meta tag user-id não encontrada - notificações em tempo real desabilitadas');
        return;
    }

    const userId = userIdMeta.content;
    if (!userId || !window.Echo) {
        console.log('Echo ou userId não disponível');
        return;
    }

    console.log('Conectando ao canal de notificações do usuário:', userId);

    // Escuta notificações no canal privado do usuário
    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            console.log('Nova notificação recebida:', notification);

            // Mostra toast de notificação
            if (notification.message) {
                showToast(notification.message, 'info');
            }

            // Atualiza o badge de notificações (adiciona ponto vermelho)
            const notificationBadge = document.querySelector('[data-notification-badge]');
            if (notificationBadge) {
                notificationBadge.classList.remove('hidden');
            }

            // Adiciona a notificação dinamicamente à lista (se existir)
            const notificationList = document.querySelector('[data-notification-list]');
            if (notificationList) {
                const emptyMessage = notificationList.querySelector('[data-empty-message]');
                if (emptyMessage) {
                    emptyMessage.remove();
                }

                const notificationItem = document.createElement('a');
                notificationItem.href = notification.board_id ? `/boards/${notification.board_id}` : '#';
                notificationItem.className = 'block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700';
                notificationItem.innerHTML = `
                    <p class="text-sm text-gray-900 dark:text-white">${notification.message}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Agora mesmo</p>
                `;
                notificationList.insertBefore(notificationItem, notificationList.firstChild);
            }
        })
        .error((error) => {
            console.error('Erro no canal de notificações:', error);
        });
});
