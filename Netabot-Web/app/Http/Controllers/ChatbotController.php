<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->user_detail->id;

        $chats = UserChat::where('id_user', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        $sessions = $chats->groupBy('session_key');

        return view('chat', compact('sessions'));
    }

    public function sendMessage(Request $request)
    {
        $input = $request->input('message');
        $id = $request->input('id');

        $words = explode(' ', $input);

        $products = Product::select('name', 'description')->get();

        $databaseWords = [];
        foreach ($products as $p) {
            $text = $p->name . ' ' . $p->description;
            foreach ($words as $word) {
                if (stripos($text, $word) !== false) {
                    $databaseWords[] = $word;
                }
            }
        }
        $databaseWords = array_unique($databaseWords);

        $highlightedInput = $input;
        foreach ($databaseWords as $word) {
            $highlightedInput = preg_replace("/\b($word)\b/i", "<mark>$1</mark>", $highlightedInput);
        }

        $query = Product::query();

        foreach ($databaseWords as $word) {
            $query->where(function ($q) use ($word) {
                $q->where('name', 'LIKE', "%{$word}%")
                    ->orWhere('description', 'LIKE', "%{$word}%");
            });
        }

        $matchedProducts = $query->limit(10)->get();

        if ($matchedProducts->isEmpty()) {
            $botResponse = 'Maaf, produk tidak ditemukan.';
        } else {
            $botResponse = "Berikut produk yang tersedia:<br>";

            foreach ($matchedProducts as $p) {
                $image = $p->url_images ?? null;
                $link  = $p->link ?? null;

                $botResponse .= "<div class='flex gap-2 items-center mb-2'>";

                if (!empty($image)) {
                    $botResponse .= "<img src='{$image}' alt='{$p->name}' class='w-12 h-12 object-cover rounded'>";
                }

                $botResponse .= "<div>";

                if (!empty($link)) {
                    $botResponse .= "<a href='{$link}' target='_blank' 
                        class='text-blue-600 underline font-semibold'>{$p->name}</a><br>";
                } else {
                    $botResponse .= "<strong>{$p->name}</strong><br>";
                }

                if (!empty($p->description)) {
                    $botResponse .= "{$p->description}<br>";
                }

                // ➕ Tambahkan SOLD & RATING
                $botResponse .= "Rating: ⭐ {$p->rating}<br>";
                $botResponse .= "Terjual: {$p->sold}<br>";

                $botResponse .= "Rp " . number_format($p->price ?? 0, 0, ',', '.');

                $botResponse .= "</div></div>";
            }
        }

        // Response untuk frontend
        $response = "Berikut produk yang tersedia:<br>";

        foreach ($matchedProducts as $p) {
            $image = $p->url_images ?? null;
            $link  = $p->link ?? null;

            $response .= "<div class='flex gap-2 items-center mb-2'>";

            if (!empty($image)) {
                $response .= "<img src='{$image}' alt='{$p->name}' class='w-12 h-12 object-cover rounded'>";
            }

            $response .= "<div>";

            if (!empty($link)) {
                $response .= "<a href='{$link}' target='_blank' 
                    class='text-blue-600 underline font-semibold'>{$p->name}</a><br>";
            } else {
                $response .= "<strong>{$p->name}</strong><br>";
            }

            if (!empty($p->description)) {
                $response .= "{$p->description}<br>";
            }

            // ➕ Tambahkan SOLD & RATING
            $response .= "Rating: ⭐ {$p->rating}<br>";
            $response .= "Terjual: {$p->sold}<br>";

            $response .= "Rp " . number_format($p->price ?? 0, 0, ',', '.');

            $response .= "</div></div>";
        }

        if ($id !== null) {
            UserChat::create([
                'id_user' => $id,
                'chat' => $input,
                'bot_response' => $botResponse,
                'session_key' => now()->format('Y-m-d'),
            ]);
        }

        return response()->json([
            'highlighted' => $highlightedInput,
            'response' => $response
        ]);
    }
}
