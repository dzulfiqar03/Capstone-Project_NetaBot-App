<x-app-layout>
<style>
    body {
        background: linear-gradient(135deg, #dff8ff, #f8fbff);
    }

    /* Chat Bubbles */
    .bubble {
        max-width: 75%;
        padding: 14px 18px;
        border-radius: 18px;
        margin-bottom: 14px;
        animation: fadeInUp 0.35s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .bubble-user {
        align-self: flex-end;
        background: #eef1f4;
        color: #333;
    }

    .bubble-bot {
        align-self: flex-start;
        background: linear-gradient(135deg, #06b6d4, #0ea5e9);
        color: white;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Bot bubble content fix */
    .bubble-bot .content {
        overflow-wrap: break-word;
        word-break: break-word;
    }

    /* Typing Indicator */
    .typing {
        display: inline-flex;
        gap: 4px;
    }

    .typing div {
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: blink 1.4s infinite ease-in-out;
    }

    .typing div:nth-child(2) { animation-delay: 0.2s; }
    .typing div:nth-child(3) { animation-delay: 0.4s; }

    @keyframes blink {
        0% { opacity: 0.3; transform: translateY(0); }
        50% { opacity: 1; transform: translateY(-4px); }
        100% { opacity: 0.3; transform: translateY(0); }
    }

    /* Input Area */
    .floating-input {
        background: white;
        padding: 14px;
        border-radius: 50px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

</style>

<div class="flex h-screen">

    <div class="w-64 bg-gray-100 p-4 overflow-y-auto">
        <h2 class="font-bold mb-4">Chat History</h2>
        @foreach($sessions as $key => $sessionChats)
            <button onclick="loadSession('{{ $key }}')" 
                    class="w-full text-left mb-2 p-2 rounded hover:bg-gray-200 bg-white shadow">
                {{ \Carbon\Carbon::parse($sessionChats->first()->created_at)->format('d M Y') }}
            </button>
        @endforeach
    </div>

    <!-- CHAT AREA -->
    <div class="flex-1 flex flex-col">
        <div id="messages" class="flex flex-col flex-grow p-6 overflow-y-auto space-y-3">
            <!-- Chat akan dimuat disini -->
        </div>

        <form id="chatForm" class="p-4">
            <div class="floating-input flex items-center gap-2">
                <input type="hidden" name="id" id="ID" value="{{ Auth::user()->user_detail->id }}">
                <input name="chat" id="inputMessage" 
                    class="flex-grow border-none outline-none px-3"
                    placeholder="Ketik sesuatu..." required>
                <button class="bg-cyan-500 hover:bg-cyan-600 text-white px-5 py-2 rounded-full shadow">
                    Kirim
                </button>
            </div>
        </form>
    </div>
</div>


<script>
const chatForm = document.getElementById('chatForm');
const messages = document.getElementById('messages');
const inputMessage = document.getElementById('inputMessage');
const ID = document.getElementById('ID');

// Add Message Bubble
function appendMessage(text, isUser = true, isHTML = false) {
    const div = document.createElement('div');
    div.className = "bubble " + (isUser ? "bubble-user" : "bubble-bot");

    if (!isUser) {
        div.innerHTML = `
            <div class="flex items-start gap-3">
                <img src="https://cdn-icons-png.flaticon.com/512/4712/4712100.png" 
                     class="w-6 h-6 mt-1">

                <div class="content space-y-2 text-sm leading-relaxed">
                    ${isHTML ? text : text.replace(/\n/g, "<br>")}
                </div>
            </div>
        `;
    } else {
        div.textContent = text;
    }

    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}

function showTyping() {
    const div = document.createElement("div");
    div.id = "typingIndicator";
    div.className = "bubble bubble-bot flex items-center gap-3";

    div.innerHTML = `
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712100.png" class="w-6 h-6">
        <div class="typing">
            <div></div><div></div><div></div>
        </div>
    `;

    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
}

function removeTyping() {
    const el = document.getElementById("typingIndicator");
    if (el) el.remove();
}


chatForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const text = inputMessage.value.trim();
    const id = ID.value;
    if (!text) return;

    appendMessage(text, true);
    inputMessage.value = "";

    showTyping();

    const res = await fetch("/chat/send", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ message: text, id: id })
    });

    const data = await res.json();
    removeTyping();

    appendMessage(data.response, false, true);
});

let currentSession = null;

function loadSession(sessionKey) {
    currentSession = sessionKey;
    const messages = document.getElementById('messages');
    messages.innerHTML = '';

    fetch(`/chat/session/${sessionKey}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(chat => {
                // User
                appendMessage(chat.chat, true);
                // Bot
                appendMessage(chat.bot_response, false, true);
            });
        });
}

</script>

</x-app-layout>
