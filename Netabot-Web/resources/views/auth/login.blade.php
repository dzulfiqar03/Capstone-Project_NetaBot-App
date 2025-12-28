<x-guest-layout>
    <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-16 rounded-xl shadow-lg w-96 text-center">
            <h1 class="text-3xl font-bold mb-6">Signin</h1>
            <p class="mb-4 text-gray-500">Silahkan masukkan akun yang sudah terdaftar</p>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false, showConfirm: false }">
                @csrf
                <input id="email" type="email" name="email" placeholder="Masukkan email kamu"
                    value="{{ old('email') }}"
                    class="mb-2 block w-full rounded-xl border border-gray-300 p-3 focus:outline-none focus:ring-2 focus:ring-cyan-400" />

                <!-- Password -->
                <div class="relative mb-3">
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                        value="{{ old('password') }}" placeholder="Masukkan password"
                        class="block w-full rounded-xl border border-gray-300 p-3 pr-10 focus:outline-none focus:ring-2 focus:ring-cyan-400" />
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.562-4.115m1.566-1.33A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.152 5.19M3 3l18 18" />
                        </svg>
                    </button>
                </div>

                <button type="submit"
                    class="rounded-xl bg-gradient-to-r from-cyan-400 to-blue-500 py-3 px-8 text-white font-semibold text-lg w-full hover:opacity-80 transition">
                    Login
                </button>
            </form>



            <div class="mt-6">
                <a href="{{ route('register') }}" class="text-sm text-gray-500 hover:underline">
                    Belum punya akun? <span class="font-bold">Daftar sekarang</span>
                </a>
            </div>
        </div>
</x-guest-layout>
