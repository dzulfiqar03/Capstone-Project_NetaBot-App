<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\UserChat;
use App\Models\UserDetail;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserChat>
 */
class UserChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = UserChat::class;

    public function definition(): array
    {
        $normalQuestions = [
            "Produk ini original?",
            "Berapa harga produk ini?",
            "Apakah ada diskon hari ini?",
            "Stok masih tersedia?",
            "Berapa lama pengiriman?",
            "Produk ini bergaransi?",
            "Apakah bisa COD?",
            "Cocok untuk pemula?"
        ];

        $informalQuestions = [
            "Ini ori gak?",
            "Harganya berapa ya?",
            "Ada promo gak?",
            "Ready kah?",
            "Kirimnya lama gak?",
            "Garansi brp?",
            "Bisa cod?",
        ];

        $typoQuestions = [
            "brpa hrg prduk ni?",
            "ini orii gk?",
            "stok msih ad?",
            "kirmn brp hr?",
            "gransi brp?",
        ];

        $ngawurQuestions = [
            "asdasd qwerty",
            "123123 abc xyz",
            "lorem ipsum dolor sit",
            "!!! ??? ###",
            "hehehehe",
            "zzzz zzz zzz",
            "kok gitu ya wkwkwk",
            "saya mau tapi nggatau mau apa"
        ];

        $spamQuestions = [
            "promo murah klik link",
            "buy now limited offer",
            "diskon besar besaran!!!",
            "chat ini di auto ya?",
            "test test test"
        ];

        $allQuestions = array_merge(
            $normalQuestions,
            $informalQuestions,
            $typoQuestions,
            $ngawurQuestions,
            $spamQuestions
        );

        $botResponses = [
            "Produk ini original dan bergaransi resmi.",
            "Harga produk dapat dilihat di halaman produk.",
            "Saat ini tersedia promo terbatas.",
            "Produk ready stock dan siap dikirim.",
            "Estimasi pengiriman 2–4 hari kerja.",
            "Silakan jelaskan pertanyaan Anda lebih detail.",
            "Maaf, saya belum memahami maksud Anda.",
            "Mohon ketikkan pertanyaan yang lebih jelas.",
            "Silakan cek deskripsi produk untuk informasi lengkap.",
            "Terima kasih telah menghubungi kami."
        ];

        return [
            'id_user' => UserDetail::inRandomOrder()->value('id'),

            'session_key' => Carbon::now()
                ->subDays(rand(0, 30))
                ->format('Y-m-d'),

            'chat' => $this->faker->randomElement($allQuestions),
            'bot_response' => $this->faker->randomElement($botResponses),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
