<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function sendMessage(Request $request)
    {
        $keyword = $request->input('message');

        $products = Product::where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->limit(10)
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'response' => 'Maaf, produk tidak ditemukan.'
            ]);
        }

        $response = "Berikut produk yang tersedia:<br>";

        foreach ($products as $p) {

            $image = $p->url_images; //  kolom baru yang valid
            $link  = $p->link;       //  kolom baru yang valid

            $response .= "<div class='flex gap-2 items-center mb-2'>";

            if (!empty($image)) {
                $response .= "
                    <img src='{$image}' 
                         alt='{$p->name}' 
                         class='w-12 h-12 object-cover rounded'>
                ";
            }

            $response .= "<div>";

            // LINK PRODUK
            if (!empty($link)) {
                $response .= "
                    <a href='{$link}' 
                       target='_blank'
                       class='text-blue-600 underline font-semibold'>
                        {$p->name}
                    </a><br>
                ";
            } else {
                $response .= "<strong>{$p->name}</strong><br>";
            }

            if (!empty($p->description)) {
                $response .= "{$p->description}<br>";
            }

            $response .= "Rp " . number_format($p->price, 0, ',', '.');

            $response .= "</div></div>";
        }

        return response()->json([
            'response' => $response
        ]);
    }
}
