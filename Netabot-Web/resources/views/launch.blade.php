<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Netabot</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex justify-center items-center h-screen">
    <div class="bg-white drop-shadow-lg p-16 rounded-xl w-[450px] text-center">
        <h1 class="font-bold text-3xl mb-1">Netabot</h1>
        <p class="text-gray-600 mb-8">Selamat datang di chatbot kami</p>
        <div class="space-x-4">
            <a href="{{ route('login') }}"
                class="px-8 py-2 rounded-xl text-white text-sm bg-gradient-to-r from-cyan-400 to-blue-500 hover:opacity-80 transition">
                Login
            </a>
            <a href="{{ route('register') }}"
                class="px-8 py-2 rounded-xl text-sm border border-gray-300 hover:border-gray-500 transition">Sign up</a>
        </div>
    </div>
</body>
</html>