<x-app-layout>

    <!-- Content -->
    <div class="p-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between w-full">
                <div class="">
                     <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Daftar Produk</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Temukan produk terbaik pilihan kami!</p>
                </div>

                <div class="">
                                <button id="btnScrape" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                Jalankan Scraping
            </button>

            <div id="scrapeStatus" class="mt-3 text-sm text-gray-700"></div>

            <script>
                document.getElementById('btnScrape').addEventListener('click', function() {
                    let btn = this;
                    btn.disabled = true;
                    btn.innerText = 'Menjalankan...';

                    fetch('{{ route('scrape.run') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            btn.disabled = false;
                            btn.innerText = 'Jalankan Scraping';

                            document.getElementById('scrapeStatus').innerHTML =
                                data.message ?
                                `<span class="text-green-600">${data.message}</span>` :
                                `<span class="text-red-600">${data.error}</span>`;
                        })
                        .catch(err => {
                            btn.disabled = false;
                            btn.innerText = 'Jalankan Scraping';
                            document.getElementById('scrapeStatus').innerHTML =
                                `<span class="text-red-600">${err}</span>`;
                        });
                });
            </script>
                </div>
               
            </div>

            <div class="p-6 overflow-x-auto">
                <table id="tblProduct" class="min-w-full text-sm text-left text-gray-700 dark:text-gray-100">
                    <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Foto Produk</th>
                            <th scope="col" class="px-6 py-3">Nama Produk</th>
                            <th scope="col" class="px-6 py-3">Harga</th>
                            <th scope="col" class="px-6 py-3">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($product as $index => $items)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $index + 1 }}
                                </td>
                                <td class="px-6 py-3">
                                    <img src="{{ $items['url_images'] }}" alt="{{ $items['name'] }}"
                                        class="w-16 h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                </td>
                                <td class="px-6 py-3 font-semibold">{{ $items['name'] }}</td>
                                <td class="px-6 py-3 text-blue-600 dark:text-blue-400 font-semibold">
                                    Rp{{ number_format($items['price'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                    {{ Str::limit($items['description'], 80, '...') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                $('#tblProduct').DataTable({
                    pageLength: 5,
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari produk...",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                        paginate: {
                            next: "›",
                            previous: "‹"
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
