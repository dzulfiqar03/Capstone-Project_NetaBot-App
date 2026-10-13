<?php

use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProfileController;
use App\Models\UserChat;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/', '/launch');

Route::get('/launch', function () {
    return view('launch');
});


// Dashboard Member
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'roles:member'])
    ->name('dashboard');

// Dashboard Admin
Route::get('/admin/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'roles:admin'])
    ->name('admin.dashboard');
Route::post('/scrape-products', [ProductController::class, 'scrape'])
    ->name('scrape.products')
    ->middleware(['auth', 'verified', 'roles:admin']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat/session/{key}', function($key){
    $userId = auth()->user()->user_detail->id;
    $chats = UserChat::where('id_user', $userId)
                     ->where('session_key', $key)
                     ->orderBy('created_at')
                     ->get();
    return response()->json($chats);
});

});

require __DIR__ . '/auth.php';

Route::get('/chat', [ChatbotController::class, 'index'])->name('chat');
Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);


Route::post('/scrape', function () {
    try {
        $response = Http::timeout(20)
                        ->get(env('SCRAPER_URL') . '/scrape');

        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'message' => $data['message'] ?? 'Scraping berhasil',
                'status' => $data['status'] ?? 'success'
            ]);
        } else {
            return response()->json([
                'error' => 'Gagal memanggil scraper',
                'http_status' => $response->status(),
                'body' => $response->body()
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
})->name('scrape.run');
