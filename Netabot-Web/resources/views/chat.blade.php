<x-app-layout>
    <div class="bg-gradient-to-r from-cyan-400 to-blue-600 p-4 font-bold text-white flex justify-between items-center">
        <div>Netabot</div>
        <a href="{{ route('dashboard') }}" class="hover:underline">Back</a>
    </div>
    <div class="flex flex-col h-[calc(100vh-64px)] p-4 bg-white">
        <div id="messages" class="flex flex-col flex-grow space-y-4 overflow-y-auto p-2">
            <div class="text-gray-600">Halo! Silakan ketik pesan Anda untuk mencari produk Netafarm.</div>
        </div>

        <form id="chatForm" class="flex gap-2 pt-3 border-t border-gray-300">
            <input id="inputMessage" type="text" placeholder="Masukkan Pesan Anda"
                class="flex-grow rounded-lg border border-gray-300 p-3 focus:outline-none focus:ring-2 focus:ring-cyan-400" required />
            <button type="submit"
                class="rounded-full bg-cyan-400 hover:bg-cyan-600 p-3 text-white font-semibold flex justify-center items-center">
                Kirim
            </button>
        </form>
    </div>

    <script>
        const chatForm = document.getElementById('chatForm');
        const messages = document.getElementById('messages');
        const inputMessage = document.getElementById('inputMessage');

        // Fungsi untuk menambahkan pesan dengan animasi
        function appendMessage(text, isUser = true, isHTML = false) {
            const div = document.createElement('div');
            div.className = (isUser 
                ? 'self-end bg-gray-300 text-gray-900 rounded-xl p-3 max-w-xs' 
                : 'self-start bg-cyan-400 text-white rounded-xl p-3 max-w-xs');
            
            // Tambahkan animasi
            div.style.opacity = 0;
            div.style.transform = 'translateY(20px)';
            div.style.transition = 'all 0.3s ease';

            if (isHTML) {
                div.innerHTML = text;
            } else {
                div.innerText = text;
            }

            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;

            // Trigger animasi
            requestAnimationFrame(() => {
                div.style.opacity = 1;
                div.style.transform = 'translateY(0)';
            });
        }

        // Fungsi untuk menampilkan typing indicator
        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'self-start bg-cyan-400 text-white rounded-xl p-3 max-w-xs flex items-center gap-2';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = '<span class="animate-pulse">Bot sedang mengetik...</span>';
            messages.appendChild(typingDiv);
            messages.scrollTop = messages.scrollHeight;
        }

        function removeTypingIndicator() {
            const typingDiv = document.getElementById('typingIndicator');
            if (typingDiv) typingDiv.remove();
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const msg = inputMessage.value.trim();
            if (!msg) return;

            // Tampilkan pesan user dengan animasi
            appendMessage(msg, true);

            inputMessage.value = '';

            // Tampilkan typing indicator
            showTypingIndicator();

            // Request ke backend
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: msg })
            });
            const data = await response.json();

            // Hapus typing indicator
            removeTypingIndicator();

            // Tampilkan balasan bot dengan animasi dan HTML
            appendMessage(data.response, false, true);
        });
    </script>
</x-app-layout>
