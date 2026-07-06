const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

function appendMsg(text, role) {
    const area = document.getElementById('chat-messages');
    if (!area) return;
    const now = new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
    const div = document.createElement('div');
    div.className = `Msg ${role}`;
    div.innerHTML = `<div class="Msg-Bubble">${text}</div><p class="Msg-Time">${now}</p>`;
    area.appendChild(div);
    area.scrollTop = area.scrollHeight;
}

function showTyping() {
    const area = document.getElementById('chat-messages');
    if (!area) return;
    const d = document.createElement('div');
    d.className = 'Typing-Indicator';
    d.id = 'typing';
    d.innerHTML = '<span class="Typing-Dot"></span><span class="Typing-Dot"></span><span class="Typing-Dot"></span>';
    area.appendChild(d);
    area.scrollTop = area.scrollHeight;
}

function removeTyping() {
    document.getElementById('typing')?.remove();
}

async function sendChat() {
    const inp = document.getElementById('chat-input');
    if (!inp) return;
    const msg = inp.value.trim();
    if (!msg) return;

    document.getElementById('quick-replies')?.remove();
    appendMsg(msg, 'User');
    inp.value = '';
    showTyping();

    try {
        const res = await fetch('/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ message: msg })
        });

        const data = await res.json();
        removeTyping();
        appendMsg(data.reply ?? 'Maaf, terjadi kesalahan.', 'Bot');
    } catch (err) {
        removeTyping();
        appendMsg('Maaf, tidak dapat terhubung ke AI. Cek koneksi internet.', 'Bot');
    }
}

async function sendQuick(msg) {
    document.getElementById('quick-replies')?.remove();
    appendMsg(msg, 'User');
    showTyping();

    try {
        const res = await fetch('/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ message: msg })
        });

        const data = await res.json();
        removeTyping();
        appendMsg(data.reply ?? 'Maaf, terjadi kesalahan.', 'Bot');
    } catch (err) {
        removeTyping();
        appendMsg('Maaf, tidak dapat terhubung ke AI.', 'Bot');
    }
}