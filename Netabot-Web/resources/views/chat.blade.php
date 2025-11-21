<x-app-layout>
<style>
    /* Background Gradient */
    body {
        background: linear-gradient(135deg, #dff8ff, #f8fbff);
    }

    /* Chat Bubbles */
    .bubble {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 18px;
        margin-bottom: 12px;
        animation: fadeInUp 0.35s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .bubble-user {
        align-self: flex-end;
        background: #eef1f4;
        color: #333;
    }

    .bubble-bot {
        align-self: flex-start;
        background: #0fc2f8;
        background: linear-gradient(135deg, #06b6d4, #0ea5e9);
        color: white;
    }

    /* Smooth Animation */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
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
        0% { opacity: 0.3; transform: translateY(0px); }
        50% { opacity: 1; transform: translateY(-4px); }
        100% { opacity: 0.3; transform: translateY(0px); }
    }

    /* Floating Input Area */
    .floating-input {
        background: white;
        padding: 14px;
        border-radius: 50px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }

</style>

<div class="flex flex-col h-screen">

    <!-- HEADER -->
    <div class="p-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold flex justify-between items-center shadow">
        <div class="flex items-center gap-2">
            <img src="https://cdn-icons-png.flaticon.com/512/4712/4712100.png" class="w-8 h-8">
            Netabot
        </div>
        <a href="{{ route('dashboard') }}" class="underline hover:text-gray-200">Back</a>
    </div>

    <!-- CHAT AREA -->
    <div id="messages" class="flex flex-col flex-grow p-6 overflow-y-auto space-y-3">
        <div class="bubble bubble-bot flex items-center gap-3">
            <img src="https://cdn-icons-png.flaticon.com/512/4712/4712100.png" class="w-6 h-6">
            Halo! Silakan ketik pesan Anda untuk mencari produk Netafarm.
        </div>
    </div>

    <!-- INPUT -->
    <form id="chatForm" class="p-4">
        <div class="floating-input flex items-center gap-2">
            <input id="inputMessage" 
                   class="flex-grow border-none outline-none px-3"
                   placeholder="Ketik sesuatu..."
                   required>
            <button class="bg-cyan-500 hover:bg-cyan-600 text-white px-5 py-2 rounded-full shadow">
                Kirim
            </button>
        </div>
    </form>
</div>

<script>
    const chatForm = document.getElementById('chatForm');
    const messages = document.getElementById('messages');
    const inputMessage = document.getElementById('inputMessage');

    // Append Message (with bubble & animation)
    function appendMessage(text, isUser = true, isHTML = false) {
        const div = document.createElement('div');
        div.className = "bubble " + (isUser ? "bubble-user" : "bubble-bot");

        if (!isUser) {
            div.innerHTML = `<div class="flex items-start gap-2">
                <img src="https://cdn-icons-png.flaticon.com/512/4712/4712100.png" class="w-6 h-6 mt-1"> 
                <div>${isHTML ? text : text.replace(/\n/g, "<br>")}</div>
            </div>`;
        } else {
            div.textContent = text;
        }

        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    // Typing indicator
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

    // Handle Submit
    chatForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const text = inputMessage.value.trim();
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
            body: JSON.stringify({ message: text })
        });

        const data = await res.json();
        removeTyping();

        appendMessage(data.response, false, true);
    });
</script>

</x-app-layout>
