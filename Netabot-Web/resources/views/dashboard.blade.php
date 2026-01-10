<x-app-layout>
    <x-slot name="title">
        {{ $title ?? session('title') }}
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .floating-btn {
            animation: bounce-slow 3s infinite;
        }

        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>

    <div class="min-h-screen flex flex-col relative overflow-x-hidden">
        
        <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-blue-100/50 to-transparent -z-10"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-cyan-100/30 rounded-full blur-3xl -z-10"></div>

        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('chat') }}"
                class="floating-btn flex items-center gap-2 bg-blue-600 text-white px-6 py-4 rounded-full font-bold shadow-2xl hover:bg-blue-700 transition-all active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 14h.01M16 10h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>
                <span>Tanya Netabot</span>
            </a>
        </div>

        <div class="flex-grow flex flex-col items-center justify-center px-6 pt-6 pb-12">
            
            <div class="reveal-1 glass-card rounded-3xl p-8 mb-10 w-full max-w-4xl text-center shadow-xl shadow-blue-900/5">
                <span class="bg-blue-100 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Update Terbaru</span>
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mt-4 mb-2">Selamat Datang di Netabot</h1>
                <p class="text-slate-600 max-w-xl mx-auto">Asisten pintar Anda untuk manajemen produk Netafarm. Gunakan chatbot untuk konsultasi cepat dan akurat.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-5xl mb-10">
                <div class="reveal-2 glass-card p-5 rounded-2xl flex items-start gap-4 hover:shadow-md transition-shadow">
                    <div class="bg-cyan-500/10 p-2 rounded-lg text-cyan-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-700 text-sm leading-relaxed"><span class="font-bold block text-slate-800">Tip 1</span> Cari produk berdasarkan kategori atau nama.</p>
                </div>
                
                <div class="reveal-3 glass-card p-5 rounded-2xl flex items-start gap-4 hover:shadow-md transition-shadow">
                    <div class="bg-blue-500/10 p-2 rounded-lg text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-700 text-sm leading-relaxed"><span class="font-bold block text-slate-800">Tip 2</span> Chatbot aktif 24/7 untuk bantuan teknis.</p>
                </div>

                <div class="reveal-4 glass-card p-5 rounded-2xl flex items-start gap-4 hover:shadow-md transition-shadow">
                    <div class="bg-emerald-500/10 p-2 rounded-lg text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-700 text-sm leading-relaxed"><span class="font-bold block text-slate-800">Tip 3</span> Cek profil untuk riwayat pencarian Anda.</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 w-full max-w-4xl">
                <a href="{{ route('chat') }}"
                    class="reveal-5 group relative flex-1 bg-gradient-to-br from-blue-600 to-cyan-500 rounded-3xl p-8 text-white shadow-2xl shadow-blue-500/20 overflow-hidden transform transition-all hover:-translate-y-2">
                    <div class="absolute -right-8 -bottom-8 opacity-20 transform group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M8 10h.01M12 14h.01M16 10h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Chat Produk</h2>
                    <p class="text-blue-50 text-sm opacity-90 mb-6">Konsultasi cerdas mengenai pupuk, benih, dan alat tani.</p>
                    <div class="inline-flex items-center bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl text-sm font-semibold border border-white/30">
                        Mulai Chat &rarr;
                    </div>
                </a>

                <a href="{{ route('profile.edit') }}"
                    class="reveal-6 group relative flex-1 bg-slate-800 rounded-3xl p-8 text-white shadow-2xl shadow-slate-900/20 overflow-hidden transform transition-all hover:-translate-y-2">
                    <div class="absolute -right-8 -bottom-8 opacity-10 transform group-hover:rotate-45 transition-transform duration-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Pengaturan</h2>
                    <p class="text-slate-400 text-sm mb-6">Kelola akun, preferensi notifikasi, dan keamanan profil.</p>
                    <div class="inline-flex items-center bg-white/10 px-4 py-2 rounded-xl text-sm font-semibold border border-white/10 group-hover:bg-white/20 transition-colors">
                        Buka Settings
                    </div>
                </a>
            </div>
        </div>

        <footer class="p-6 text-slate-400 text-xs text-center">
            <div class="w-12 h-1 bg-slate-200 mx-auto mb-4 rounded-full"></div>
            &copy; {{ date('Y') }} <span class="text-blue-600 font-semibold">Netabot</span> oleh Netafarm Indolestari.
        </footer>
    </div>
</x-app-layout>