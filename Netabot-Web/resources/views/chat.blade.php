<x-app-layout>
    <x-slot name="title">{{ $title ?? session('title') }}</x-slot>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        /* Reset Layout: Pastikan tidak ada margin/padding dari layout bawaan */
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background-color: #f8fafc; }
        
        /* Container Utama */
        .chat-container { 
            display: flex; 
            height: 100vh; 
            width: 100%; 
            position: fixed; 
            top: 0; 
            left: 0; 
        }

        /* --- SIDEBAR --- */
        .chat-sidebar { 
            width: 300px; 
            background: white; 
            border-right: 1px solid #eef2f6; 
            display: flex; 
            flex-direction: column; 
            transition: transform 0.3s ease, margin-left 0.3s ease;
            flex-shrink: 0;
        }
        
        /* State Sidebar Tertutup */
        .sidebar-closed .chat-sidebar { margin-left: -300px; }

        .sidebar-header { padding: 24px; border-bottom: 1px solid #f1f5f9; }
        .sidebar-content { flex: 1; overflow-y: auto; padding: 12px; }

        .session-item { width: 100%; text-align: left; padding: 12px; border-radius: 12px; margin-bottom: 6px; display: flex; gap: 12px; transition: 0.2s; cursor: pointer; border: 1px solid transparent; }
        .session-item:hover { background: #f1f5f9; }
        .session-item.active { background: #eff6ff; border-color: #bfdbfe; }
        
        .session-icon { background: #f1f5f9; padding: 8px; border-radius: 10px; color: #64748b; }
        .session-item.active .session-icon { background: #dbeafe; color: #2563eb; }
        .session-title { font-size: 0.875rem; font-weight: 600; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .session-date { font-size: 0.75rem; color: #94a3b8; }

        /* --- CHAT AREA (NAV & CONTENT GABUNG) --- */
        .chat-main { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            min-width: 0; 
            background: #f8fafc;
        }


        #messages { 
            flex: 1; 
            overflow-y: auto; 
            padding: 2rem; 
            display: flex; 
            flex-direction: column; 
            gap: 1.5rem; 
            scroll-behavior: smooth;
        }

        /* --- STYLING RESPONS --- */
        .bubble { max-width: 80%; padding: 16px 20px; border-radius: 20px; font-size: 0.95rem; line-height: 1.6; }
        .bubble-user { align-self: flex-end; background: #2563eb; color: white; border-bottom-right-radius: 4px; }
        .bubble-bot { align-self: flex-start; background: white; border: 1px solid #eef2f6; border-bottom-left-radius: 4px; color: #1e293b; }

        .product-content h3 { font-weight: 800; color: #1e40af; border-left: 4px solid #2563eb; padding-left: 12px; margin-bottom: 12px; }
        .product-content p:first-of-type { background: #f1f5f9; padding: 12px; border-radius: 10px; border-right: 4px solid #3b82f6; margin-bottom: 10px; }
        .product-content li { margin-bottom: 6px; padding: 10px 14px; background: white; border: 1px solid #e2e8f0; border-radius: 10px; }
        .product-content li strong { color: #2563eb; display: block; font-size: 0.7rem; text-transform: uppercase; }

        .price-badge { background: #10b981; color: white; padding: 8px 20px; border-radius: 50px; font-weight: 800; margin-top: 10px; display: inline-block; }

        /* --- INPUT --- */
        .input-area { padding: 1.5rem; background: white; border-top: 1px solid #eef2f6; }
        .input-box { max-width: 800px; margin: 0 auto; display: flex; gap: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 10px 16px; }

        /* Scrollbar Styling */
        #messages::-webkit-scrollbar { width: 6px; }
        #messages::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        /* Default Mobile: Sidebar tertutup */
@media (max-width: 768px) {
    .chat-sidebar {
        position: absolute;
        z-index: 50;
        height: 100%;
        box-shadow: 10px 0 15px -3px rgba(0, 0, 0, 0.1);
        margin-left: -300px; /* Sembunyikan */
    }

    /* Saat aktif (dibuka) di mobile */
    .sidebar-open .chat-sidebar {
        margin-left: 0 !important;
    }
    
    /* Overlay untuk menutup sidebar saat klik di luar (opsional) */
    .sidebar-open .chat-main::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.3);
        z-index: 40;
    }
}

    </style>

    <div id="chatApp" class="chat-container sidebar-closed">
        <aside class="chat-sidebar">
            <div class="sidebar-header">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Riwayat Percakapan</span>
            </div>
            <div class="sidebar-content" id="session-container">
                @foreach($sessions as $key => $sessionChats)
                    <button onclick="loadSession('{{ $key }}', this)" class="session-item group">
                        <div class="session-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" stroke-width="2"></path></svg>
                        </div>
                        <div class="session-info">
                            <p class="session-title">{{ $sessionChats->first()->chat ?? 'Percakapan baru' }}</p>
                            <p class="session-date">{{ \Carbon\Carbon::parse($sessionChats->first()->created_at)->diffForHumans() }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        </aside>

        <main class="chat-main">
<nav class="chat-navbar shrink-0 w-full">
    <div class="bg-gradient-to-r from-cyan-500 to-blue-700 shadow-lg">
        <div class="flex justify-between h-16 items-center px-4 lg:px-10">
            
            <div class="flex items-center">
        <button onclick="toggleSidebar()" class="mr-4 p-2 hover:bg-white/20 rounded-xl text-white transition-all duration-300">
    <div id="icon-container">
        <svg id="icon-hamburger" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round"></path>
        </svg>
        
        <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
    </div>
</button>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"></path>
                        </svg>
                    </div>
                    <span class="font-bold text-white text-lg tracking-wide">Netabot AI</span>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="text-white border-white/50 hover:text-cyan-100 transition">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

        </div>
    </div>
</nav>
            <div id="messages">
                <div class="reveal-1 m-auto items-center text-center max-w-sm welcome-screen">
                   <div class="flex items-center justify-center w-full py-4">
    
    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-cyan-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <style>


        @keyframes blink {
            0%, 20%, 24%, 100% { transform: scaleY(1); } 
            22% { transform: scaleY(0.1); }
        }

        .robot-eye {
            transform-origin: center center;
            animation: blink 4s ease-in-out infinite; 
        }
        

        .robot-eye-right {
            animation-delay: 0.2s; 
        }
    </style>

    <rect x="3" y="6" width="18" height="13" rx="3"></rect>
    
    <path d="M5 6V3"></path>
    <circle cx="5" cy="3" r="1" fill="currentColor"></circle>
    
    <path d="M19 6V3"></path>
    <circle cx="19" cy="3" r="1" fill="currentColor"></circle>

    <circle cx="8" cy="12" r="1" fill="currentColor" class="robot-eye"></circle>
    
    <circle cx="16" cy="12" r="1" fill="currentColor" class="robot-eye robot-eye-right"></circle>

    <path d="M9 16c1 1 5 1 6 0"></path>
</svg>
</div>
                    <h1 class="text-xl font-bold text-gray-800">Ada yang bisa dibantu?</h1>
                    <p class="text-gray-500 text-sm mt-2">Cari tahu spesifikasi produk atau dosis pupuk terbaik untuk tanaman Anda.</p>
                </div>
            </div>

            <div class="input-area">
                <form id="chatForm" class="input-box">
                    <input type="hidden" id="ID" value="{{ Auth::user()->user_detail->id }}">
                    <input type="text" id="inputMessage" class="flex-grow bg-transparent border-none focus:ring-0 text-sm" placeholder="Ketik pesan di sini..." autocomplete="off" required>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-blue-700 transition active:scale-95">Kirim</button>
                </form>
            </div>
        </main>
    </div>

    <script>
const chatForm = document.getElementById('chatForm');
const messages = document.getElementById('messages');
const inputMessage = document.getElementById('inputMessage');
const ID = document.getElementById('ID');


function toggleSidebar() {
    const chatApp = document.getElementById('chatApp');
    const iconHamburger = document.getElementById('icon-hamburger');
    const iconClose = document.getElementById('icon-close');

    // Toggle class untuk membuka/tutup
    chatApp.classList.toggle('sidebar-open');
    chatApp.classList.toggle('sidebar-closed');

    // Update Ikon
    if (chatApp.classList.contains('sidebar-open')) {
        iconHamburger.classList.add('hidden');
        iconClose.classList.remove('hidden');
    } else {
        iconHamburger.classList.remove('hidden');
        iconClose.classList.add('hidden');
    }
}

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
        
        if (window.innerWidth <= 768) {
        toggleSidebar();
    }
}

</script>
</x-app-layout>