const sidebar = document.getElementById('app-sidebar');
const sidebarBackdrop = document.getElementById('sidebar-backdrop');

function setSidebarOpen(isOpen) {
    if (!sidebar) {
        return;
    }

    sidebar.toggleAttribute('data-open', isOpen);
    sidebarBackdrop?.toggleAttribute('data-open', isOpen);

    document
        .querySelectorAll('[data-sidebar-toggle][aria-controls]')
        .forEach((toggle) => toggle.setAttribute('aria-expanded', String(isOpen)));
}

function togglePasswordVisibility(toggle) {
    const input = toggle.parentElement.querySelector('input');
    const isHidden = input.type === 'password';

    input.type = isHidden ? 'text' : 'password';
    toggle.setAttribute('aria-pressed', String(isHidden));
    toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    toggle.querySelector('[data-show-icon]').classList.toggle('hidden', isHidden);
    toggle.querySelector('[data-hide-icon]').classList.toggle('hidden', !isHidden);
}

function openChat() {
    const chat = document.getElementById('chat-widget');

    if (!chat) {
        return;
    }

    chat.open = true;
    chat.querySelector('summary')?.focus();
}

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-sidebar-toggle]')) {
        setSidebarOpen(!sidebar?.hasAttribute('data-open'));

        return;
    }

    const passwordToggle = event.target.closest('[data-password-toggle]');

    if (passwordToggle) {
        togglePasswordVisibility(passwordToggle);

        return;
    }

    if (event.target.closest('[data-open-chat]')) {
        openChat();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && sidebar?.hasAttribute('data-open')) {
        setSidebarOpen(false);
    }
});
