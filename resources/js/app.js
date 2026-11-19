const sidebar = document.getElementById('app-sidebar');
const sidebarBackdrop = document.getElementById('sidebar-backdrop');
const chatWidget = document.getElementById('chat-widget');

// How often an open chat checks for new messages, in milliseconds.
const CHAT_REFRESH_INTERVAL = 5000;

const chatTimers = new WeakMap();
const renderedChats = new WeakMap();

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

function openChat(message) {
    if (!chatWidget) {
        return;
    }

    chatWidget.open = true;

    const textarea = chatWidget.querySelector('[data-chat-form] textarea');

    if (textarea && message) {
        textarea.value = message;
        textarea.setSelectionRange(message.length, message.length);
    }

    (textarea ?? chatWidget.querySelector('summary'))?.focus();
}

async function refreshChat(chat, { scrollToEnd = false } = {}) {
    if (document.hidden && !scrollToEnd) {
        return;
    }

    const messages = chat.querySelector('[data-chat-messages]');

    try {
        const response = await fetch(chat.dataset.chatUrl, {
            headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!response.ok) {
            return;
        }

        const html = await response.text();
        const isNearBottom = messages.scrollHeight - messages.scrollTop - messages.clientHeight < 48;

        if (renderedChats.get(chat) !== html) {
            messages.innerHTML = html;
            renderedChats.set(chat, html);
        }

        if (scrollToEnd || isNearBottom) {
            messages.scrollTop = messages.scrollHeight;
        }
    } catch {
        // A dropped connection is retried on the next refresh.
    }
}

function startChat(chat) {
    if (chatTimers.has(chat)) {
        return;
    }

    const messages = chat.querySelector('[data-chat-messages]');
    messages.scrollTop = messages.scrollHeight;

    refreshChat(chat, { scrollToEnd: true });
    chatTimers.set(chat, window.setInterval(() => refreshChat(chat), CHAT_REFRESH_INTERVAL));
}

function stopChat(chat) {
    window.clearInterval(chatTimers.get(chat));
    chatTimers.delete(chat);
}

function showChatError(form, message) {
    const error = form.querySelector('[data-chat-error]');

    error.textContent = message;
    error.classList.toggle('hidden', !message);
}

async function sendChatMessage(form) {
    const chat = form.closest('[data-chat]');
    const button = form.querySelector('[type="submit"]');
    const textarea = form.querySelector('textarea');

    button.disabled = true;
    showChatError(form, '');

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form),
        });

        if (response.status === 422) {
            const { errors } = await response.json();
            showChatError(form, errors?.body?.[0] ?? 'Your message could not be sent.');

            return;
        }

        if (!response.ok) {
            const messagesByStatus = {
                419: 'Your session expired. Refresh the page and try again.',
                429: "You're sending messages too quickly. Please wait a moment.",
            };

            showChatError(form, messagesByStatus[response.status] ?? 'Your message could not be sent. Please try again.');

            return;
        }

        textarea.value = '';
        await refreshChat(chat, { scrollToEnd: true });
    } catch {
        showChatError(form, 'You appear to be offline. Please try again.');
    } finally {
        button.disabled = false;
        textarea.focus();
    }
}

function showPayoutInstructions(payoutMethod) {
    const destination = document.getElementById('destination');
    const destinationHint = document.getElementById('destination-hint');

    if (destination) {
        destination.placeholder = payoutMethod.dataset.placeholder;
    }

    if (destinationHint) {
        destinationHint.textContent = payoutMethod.dataset.instructions;
    }
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

    const chatTrigger = event.target.closest('[data-open-chat]');

    if (chatTrigger) {
        openChat(chatTrigger.dataset.chatMessage);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && sidebar?.hasAttribute('data-open')) {
        setSidebarOpen(false);
    }

    // Enter sends a chat message; Shift+Enter starts a new line.
    const chatTextarea = event.target.closest?.('[data-chat-form] textarea');

    if (chatTextarea && event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
        event.preventDefault();
        chatTextarea.form.requestSubmit();
    }
});

document.addEventListener('change', (event) => {
    const payoutMethod = event.target.closest('[data-payout-method]');

    if (payoutMethod) {
        showPayoutInstructions(payoutMethod);
    }
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (form.matches('[data-chat-form]')) {
        event.preventDefault();
        sendChatMessage(form);

        return;
    }

    // Forms with a data-confirm message ask before submitting, e.g. before moving money.
    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});

chatWidget?.addEventListener('toggle', () => {
    const chat = chatWidget.querySelector('[data-chat]');

    if (!chat) {
        return;
    }

    if (chatWidget.open) {
        chatWidget.querySelector('[data-chat-unread]')?.remove();
        startChat(chat);
    } else {
        stopChat(chat);
    }
});

document.querySelectorAll('[data-chat][data-chat-autostart]').forEach((chat) => startChat(chat));
