<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserChat;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->user_detail->id;
        $chats = UserChat::where('id_user', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        $sessions = $chats->groupBy('session_key');
        $title = 'Netabot AI - Chat';
        return view('chat', compact('sessions', 'title'));
    }

    public function sendMessage(Request $request)
    {
        $input = $request->input('message');
        $id = $request->input('id');
        $words = explode(' ', strtolower($input));

       $query = Product::query();

$stopwords = ['cari', 'carikan', 'tampilkan', 'mau', 'beli', 'yang', 'ada', 'dong', 'tolong'];
$words = array_diff($words, $stopwords);


if (!empty($words)) {
    $query->where(function($q) use ($words) {
        foreach ($words as $word) {

            $q->orWhere('name', 'LIKE', "%{$word}%")
              ->orWhere('description', 'LIKE', "%{$word}%");
        }
    });
}

$matchedProducts = $query->orderBy('rating', 'desc')
                         ->limit(5)
                         ->get();

        if ($matchedProducts->isEmpty()) {
            $response = "Maaf, saya tidak dapat menemukan produk yang sesuai dengan **" . e($input) . "**. Cobalah gunakan kata kunci lain seperti 'Pupuk' atau 'Bibit'.";
        } else {
            $response = "<p class='mb-4 text-slate-600'>Berikut adalah rekomendasi produk terbaik untuk Anda:</p>";
            $response .= "<div class='grid gap-4'>";

            foreach ($matchedProducts as $p) {
                $image = $p->url_images ?? 'https://via.placeholder.com/150';
                $formattedPrice = "Rp " . number_format($p->price ?? 0, 0, ',', '.');
                $link = $p->link ?? '#';
                
                $response .= "
                <div class='flex flex-col sm:flex-row gap-4 p-4 bg-slate-50 border border-slate-200 rounded-2xl hover:border-blue-400 hover:bg-white transition-all duration-300 shadow-sm group'>
                    <div class='flex-1 flex flex-col justify-between'>
                        <div>
                            <div class='flex justify-between items-start gap-2'>
                                <h4 class='font-bold text-slate-800 text-sm leading-tight line-clamp-2'>{$p->name}</h4>
                                <span class='shrink-0 text-[13px] font-extrabold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg border border-blue-100'>{$formattedPrice}</span>
                            </div>
                            <p class='text-[11px] text-slate-500 mt-1 line-clamp-2'>{$p->description}</p>
                        </div>
                        
                        <div class='flex items-center justify-between mt-3'>
                            <div class='flex items-center gap-3 text-[10px] font-bold uppercase tracking-wider text-slate-400'>
                                <span class='flex items-center text-amber-500'><svg class='w-3 h-3 mr-1' fill='currentColor' viewBox='0 0 20 20'><path d='M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z'></path></svg> {$p->rating}</span>
                                <span>Terjual {$p->sold}</span>
                            </div>
                            <a href='{$link}' target='_blank' class='bg-blue-600 text-white text-[10px] px-3 py-1.5 rounded-lg font-bold hover:bg-blue-700 transition-colors'>Detail</a>
                        </div>
                    </div>
                </div>";
            }
            $response .= "</div>";
        }

        // Simpan ke Database
        if ($id) {
            UserChat::create([
                'id_user' => $id,
                'chat' => $input,
                'bot_response' => $response,
                'session_key' => now()->format('Y-m-d'),
            ]);
        }

        return response()->json(['response' => $response]);
    }
}