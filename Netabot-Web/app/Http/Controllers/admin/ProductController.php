<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        try {
            
            $response = Http::get(env('SCRAPER_URL') . '/scrape');

            if ($response->successful()) {
                $data = $response->json();
                return back()->with('success', $data['message']);
            } else {
                return back()->with('error', 'Gagal memanggil Flask API.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
