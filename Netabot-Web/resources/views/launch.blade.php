<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Netabot</title>
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2306b6d4%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><rect x=%223%22 y=%228%22 width=%2218%22 height=%2212%22 rx=%223%22/><path d=%22M12 8V5M9 5h6M7 13h.01M17 13h.01M9 17h6%22/></svg>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Animasi Floating (Melayang) */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(12deg); }
            50% { transform: translateY(-15px) rotate(10deg); }
        }
        .animate-float { animation: float 5s ease-in-out infinite; }

        /* Animasi Muncul Berurutan */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reveal-1 { animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0; }
        .reveal-2 { animation: fadeInUp 0.8s ease-out 0.4s forwards; opacity: 0; }
        .reveal-3 { animation: fadeInUp 0.8s ease-out 0.6s forwards; opacity: 0; }
        .reveal-4 { animation: fadeInUp 0.8s ease-out 0.8s forwards; opacity: 0; }

        .bg-mesh {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(34, 211, 238, 0.2) 0, transparent 50%), 
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.2) 0, transparent 50%);
        }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-6 overflow-hidden">

    <div class="absolute top-10 left-10 w-40 h-40 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
    <div class="absolute bottom-10 right-10 w-40 h-40 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>

    <div class="relative w-full max-w-[450px]">
        
        <div class="flex justify-center mb-10 reveal-1">
            <div class="w-24 h-24 bg-gradient-to-tr from-cyan-500 to-blue-600 rounded-[2rem] shadow-2xl shadow-blue-500/40 flex items-center justify-center border-t border-white/40 animate-float">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="6" width="18" height="13" rx="3"></rect>
    
    <path d="M5 6V3"></path>
    <path d="M19 6V3"></path>
    <circle cx="5" cy="3" r="1" fill="currentColor"></circle>
    <circle cx="19" cy="3" r="1" fill="currentColor"></circle>

    <circle cx="8" cy="12" r="1" fill="currentColor"></circle>
    <circle cx="16" cy="12" r="1" fill="currentColor"></circle>

    <path d="M9 16c1 1 5 1 6 0"></path>
</svg>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-3xl p-10 rounded-[3rem] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.1)] border border-white/50 text-center relative overflow-hidden">
            
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl"></div>

            <div class="reveal-2">
                <h1 class="font-extrabold text-4xl text-slate-900 tracking-tight mb-3">
                    Netabot<span class="text-blue-600">.</span>
                </h1>
                <p class="text-slate-500 leading-relaxed mb-10 font-medium">
                    Tanyakan apa saja tentang produk kami. Asisten cerdas Anda siap membantu 24/7.
                </p>
            </div>
            
            <div class="flex flex-col gap-4 reveal-3">
                <a href="{{ route('login') }}"
                    class="group relative overflow-hidden px-8 py-4 rounded-2xl bg-slate-900 text-white font-bold transition-all hover:shadow-xl hover:shadow-blue-500/25 active:scale-[0.97]">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span class="relative flex items-center justify-center gap-2 uppercase tracking-wider text-xs">
                        Masuk Ke Akun
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </span>
                </a>
                
                <a href="{{ route('register') }}"
                    class="px-8 py-4 rounded-2xl text-slate-600 font-bold border-2 border-slate-100 hover:bg-white hover:border-slate-300 transition-all active:scale-[0.97] text-xs uppercase tracking-wider">
                    Daftar Baru
                </a>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-100 reveal-4">
                <p class="text-[10px] text-slate-400 font-bold tracking-[0.2em] uppercase">
                    Version 2.0 • Netafarm Official
                </p>
            </div>
        </div>

        <div class="flex justify-center gap-1.5 mt-8 reveal-4">
            <div class="w-8 h-1 bg-blue-600 rounded-full"></div>
            <div class="w-2 h-1 bg-slate-300 rounded-full"></div>
            <div class="w-2 h-1 bg-slate-300 rounded-full"></div>
        </div>
    </div>

</body>
</html>