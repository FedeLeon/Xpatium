import './bootstrap';

const updateOnlineDots = (onlineIds) => {
    document.querySelectorAll('[data-online-user-id]').forEach((el) => {
        const userId = Number(el.dataset.onlineUserId);
        if (!Number.isNaN(userId) && onlineIds.has(userId)) {
            el.classList.add('is-online');
        } else {
            el.classList.remove('is-online');
        }
    });
};

const initPresence = () => {
    if (!window.Echo) {
        return;
    }
    const onlineIds = new Set();
    window.Echo.join('presence-online')
        .here((users) => {
            onlineIds.clear();
            users.forEach((user) => onlineIds.add(user.id));
            updateOnlineDots(onlineIds);
        })
        .joining((user) => {
            onlineIds.add(user.id);
            updateOnlineDots(onlineIds);
        })
        .leaving((user) => {
            onlineIds.delete(user.id);
            updateOnlineDots(onlineIds);
        });
};

const scrollMessagesToBottom = (messagesEl) => {
    if (!messagesEl) {
        return;
    }
    messagesEl.scrollTop = messagesEl.scrollHeight;
};

const buildMessageRow = ({ body, time, isMine, avatarUrl, initial }) => {
    const row = document.createElement('div');
    row.className = `message-row${isMine ? ' message-row--me' : ''}`;

    const avatar = document.createElement('span');
    avatar.className = 'message-avatar';
    if (avatarUrl) {
        const img = document.createElement('img');
        img.src = avatarUrl;
        img.alt = 'Avatar';
        avatar.appendChild(img);
    } else {
        avatar.textContent = initial || '?';
    }

    const bubble = document.createElement('div');
    bubble.className = `bubble ${isMine ? 'me' : 'other'}`;
    const text = document.createElement('span');
    text.textContent = body;
    const meta = document.createElement('small');
    meta.className = 'bubble-meta';
    meta.textContent = time;
    bubble.appendChild(text);
    bubble.appendChild(meta);

    row.appendChild(avatar);
    row.appendChild(bubble);
    return row;
};

const initChatLive = () => {
    if (!window.Echo) {
        return;
    }
    const messagesEl = document.querySelector('[data-chat-messages]');
    if (!messagesEl) {
        return;
    }
    const conversationId = messagesEl.dataset.conversationId;
    const currentUserId = Number(messagesEl.dataset.currentUser);
    const currentAvatar = messagesEl.dataset.currentAvatar || '';
    const currentInitial = messagesEl.dataset.currentInitial || '';
    if (!conversationId) {
        return;
    }
    scrollMessagesToBottom(messagesEl);
    window.Echo.private(`chat.${conversationId}`)
        .listen('.message.sent', (event) => {
            if (!event || !event.message) {
                return;
            }
            const senderId = Number(event.message.user && event.message.user.id);
            const isMine = senderId === currentUserId;
            if (isMine) {
                return;
            }
            const emptyState = messagesEl.querySelector('.subtle');
            if (emptyState) {
                emptyState.remove();
            }
            const time = new Date(event.message.created_at);
            const timeLabel = time.toLocaleTimeString('es-AR', {
                hour: '2-digit',
                minute: '2-digit',
            });
            const avatarUrl = event.message.user && event.message.user.avatar_url;
            const initial = event.message.user && event.message.user.initial;
            const row = buildMessageRow({
                body: event.message.body,
                time: timeLabel,
                isMine,
                avatarUrl: avatarUrl || '',
                initial: initial || '',
            });
            messagesEl.appendChild(row);
            scrollMessagesToBottom(messagesEl);
        });
};

document.addEventListener('DOMContentLoaded', () => {
    initPresence();
    initChatLive();
});
