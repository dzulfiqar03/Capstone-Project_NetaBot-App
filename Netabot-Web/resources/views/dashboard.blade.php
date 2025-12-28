<x-app-layout>
    <div class="min-h-screen  flex flex-col">
        <!-- Header -->
        <!-- Sticky Bottom Button -->
        <div class="fixed bottom-4 right-4 z-50">
            <a href="{{ route('chat') }}"
                class="bg-blue-600 text-white px-5 py-3 rounded-full font-semibold shadow-lg hover:opacity-90 transition-all">
                Chatbot
            </a>
        </div>


        <!-- Banner Promo -->
        <div class="bg-white/20 backdrop-blur-lg rounded-xl p-6 mt-6 mx-auto max-w-4xl text-center shadow-lg">
            <h3 class="text-xl font-bold text-blue-700 mb-2">Fitur Baru!</h3>
            <p class="text-blue-700/80">Cek chatbot produk terbaru kami dengan rekomendasi pintar dan cepat!</p>
        </div>


        <!-- Tips Singkat -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-5xl mx-auto">
            <div class="bg-white/20 backdrop-blur-md rounded-xl p-4 text-center shadow-md">
                <p class="text-blue-700 font-semibold">Tip 1: Gunakan kata kunci produk untuk hasil lebih relevan.</p>
            </div>
            <div class="bg-white/20 backdrop-blur-md rounded-xl p-4 text-center shadow-md">
                <p class="text-blue-700 font-semibold">Tip 2: Klik produk untuk melihat detail lengkap dan harga.</p>
            </div>
            <div class="bg-white/20 backdrop-blur-md rounded-xl p-4 text-center shadow-md">
                <p class="text-blue-700 font-semibold">Tip 3: Chatbot selalu menampilkan produk terbaru.</p>
            </div>
        </div>

        <!-- Grid Cards -->
        <div class="p-8 grid grid-cols-2 md:grid-cols-2 gap-6 flex-grow">
            <!-- Chat Produk -->
            <a href="{{ route('chat') }}"
                class="bg-gradient-to-b from-cyan-500 h-40 to-blue-600 rounded-xl flex flex-col items-center justify-center p-6 text-white shadow-lg transform transition-all hover:scale-105 hover:shadow-2xl hover:shadow-cyan-400/50 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-14 w-14 mb-4 transform transition-transform duration-300 hover:rotate-12" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 10h.01M12 14h.01M16 10h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>
                <span class="font-semibold text-lg">Chat Produk</span>
            </a>



            <!-- Settings -->
            <div
                class="bg-gradient-to-b from-cyan-500 h-40 to-blue-600 rounded-xl flex flex-col items-center justify-center p-6  text-white shadow-lg transform transition-all hover:scale-105 hover:shadow-2xl hover:shadow-cyan-400/50 cursor-default">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-14 w-14 mb-4 transform transition-transform duration-300 hover:rotate-12" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a2 2 0 012-2h3.28a2 2 0 011.822 1.17l1.3 2.607a1 1 0 00.89.552H17a2 2 0 012 2v6a2 2 0 01-2 2H8" />
                </svg>
                <span class="font-semibold text-lg">Settings</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 text-blue-700 text-center mt-8 opacity-80">
            &copy; {{ date('Y') }} Netabot. All rights reserved.
        </div>
    </div>
</x-app-layout>
