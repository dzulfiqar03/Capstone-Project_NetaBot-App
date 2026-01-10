<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Netabot' }}</title>

<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2306b6d4%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><rect x=%223%22 y=%228%22 width=%2218%22 height=%2212%22 rx=%223%22/><path d=%22M12 8V5M9 5h6M7 13h.01M17 13h.01M9 17h6%22/></svg>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.2/css/dataTables.tailwind.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.tailwindcss.min.css">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
    
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }


         .reveal-1 { animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0; }
        .reveal-2 { animation: fadeInUp 0.8s ease-out 0.4s forwards; opacity: 0; }
        .reveal-3 { animation: fadeInUp 0.8s ease-out 0.6s forwards; opacity: 0; }
        .reveal-4 { animation: fadeInUp 0.8s ease-out 0.8s forwards; opacity: 0; }
        
         .reveal-5 { animation: fadeInUp 0.8s ease-out 0.10s forwards; opacity: 0; }
         
          .reveal-6 { animation: fadeInUp 0.8s ease-out 0.12s forwards; opacity: 0; }


    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- TailwindCSS + Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.tailwindcss.js"></script>


    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js"></script>

    <!-- Export dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(document).ready(function () {

    let table = new DataTable('#tblProduct', {
        pageLength: 5,
        responsive: true,
        lengthMenu: [5, 10, 25, 50],
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa-solid fa-file-csv mr-2"></i> CSV',
                        className: 'export-btn bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa-solid fa-file-excel mr-2"></i> Excel',
                        className: 'export-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-sm shadow-sm'
                    },
                                        {
                        extend: 'print',
                        text: '<i class="fa-solid fa-print mr-2"></i> Print',
                        className: 'export-btn bg-gray-700 hover:bg-gray-800 text-white px-3 py-1.5 rounded-md text-sm shadow-sm',
                        title: '', // kosongin biar gak dobel judul default
                        customize: function (win) {
                            // Tambahkan logo dan heading di atas
                            $(win.document.body)
                                .css('font-family', 'Poppins, sans-serif')
                                .prepend(`
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="https://netafarmid.com/wp-content/uploads/2024/05/logo_ntfrm_nw-removebg-preview.png" alt="Logo" style="width: 60px; height: auto;">
                        <h1 style="font-size: 20px; margin: 0;">Netafarm Indolestari</h1>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 14px; margin: 0;">Daftar Produk Netafarm</p>
                        <p style="font-size: 12px; margin: 0;">Dicetak pada: ${new Date().toLocaleDateString()}</p>
                    </div>
                </div>
                <hr style="border: 1px solid #ccc; margin-bottom: 20px;">
            `);

                            // Styling tambahan (opsional)
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css({
                                    'font-size': '12px',
                                    'width': '100%',
                                    'border-collapse': 'collapse'
                                });

                            $(win.document.body).find('table th')
                                .css({
                                    'background-color': '#f1f1f1',
                                    'color': '#333',
                                    'padding': '6px',
                                    'border': '1px solid #ddd'
                                });

                            $(win.document.body).find('table td')
                                .css({
                                    'padding': '6px',
                                    'border': '1px solid #ddd'
                                });
                        }
                    }
                ]
            },
            topEnd: function () {
                return $(`
<div class="lg:flex justify-end hidden items-center gap-4">
    <!-- Pencarian -->
    <div class="flex items-center gap-2">
        <label for="tableSearch" class="text-sm text-gray-700 dark:text-gray-300">Cari:</label>
        <input id="tableSearch" type="text" 
            class="border dark:border-gray-600 rounded-md px-2 py-1 text-sm 
                   bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100"
            placeholder="Ketik untuk mencari...">
    </div>

    <!-- Filter kategori -->
    <div class="flex items-center gap-2 relative">
        <label for="kategoriFilter" class="text-sm text-gray-700 dark:text-gray-300">Kategori:</label>
        <select id="kategoriFilter"
            class="appearance-none border dark:border-gray-600 rounded-md pl-2 pr-6 py-1 text-sm
                   bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100">
            <option value="">Semua</option>
            <option value="Pupuk">Pupuk</option>
            <option value="Plastik">Benih</option>
            <option value="Herbisida">Herbisida</option>
            <option value="Selang">Selang</option>
        </select>
       
    </div>

    <!-- Page Length -->
    <div class="flex items-center gap-2 relative">
        <label for="pageLength" class="text-sm text-gray-700 dark:text-gray-300">Tampilkan:</label>
        <select id="pageLength"
            class="appearance-none border dark:border-gray-600 rounded-md pl-2 pr-6 py-1 text-sm
                   bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
        
    </div>
</div>


                `)[0];
            },
            bottomStart: 'info',
            bottomEnd: 'paging'
        },
        language: {
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                previous: "← Sebelumnya",
                next: "Berikutnya →"
            },
            emptyTable: "Tidak ada data tersedia"
        }
    });


    // 🔍 Search custom
    $('#tableSearch').on('keyup', function () {
        table.search(this.value).draw();
    }); $('#pageLength').on('change', function () {
        table.page.len(parseInt(this.value)).draw();
    });
    // Filter kategori
    $('#kategoriFilter').on('change', function () {
        table.column(4).search(this.value).draw(); // kolom kategori index 4
    });

    // === Conditional formatting: highlight baris dengan harga tertinggi ===
    function highlightMaxRow() {
        let max = 0, maxIndex = -1;
        table.rows({ search: 'applied' }).every(function (rowIdx, tableLoop, rowLoop) {
            const harga = parseFloat(this.data()[3]);
            if (harga > max) {
                max = harga;
                maxIndex = rowIdx;
            }
        });
        // Hapus highlight sebelumnya
        $('#userTable tbody tr').removeClass('highlight-max');
        if (maxIndex >= 0) {
            $(table.row(maxIndex).node()).addClass('highlight-max');
        }
    }

    // Jalankan pertama kali & setiap update
    highlightMaxRow();
    table.on('draw', highlightMaxRow);


});


function filterTable(category) {
    const rows = document.querySelectorAll("#table-body tr");
    rows.forEach(row => {
        const rowCategory = row.getAttribute("data-kategori");
        if (category === "Semua" || rowCategory === category) {
            row.style.display = ""; // Tampilkan baris
        } else {
            row.style.display = "none"; // Sembunyikan baris
        }
    });

    // Update tombol aktif
    const buttons = document.querySelectorAll("button");
    buttons.forEach(button => button.classList.remove("btn-success"));
    buttons.forEach(button => button.classList.add("btn-outline-secondary"));

    const activeButton = [...buttons].find(btn => btn.textContent === category);
    if (activeButton) {
        activeButton.classList.remove("btn-outline-secondary");
        activeButton.classList.add("btn-success");
    }
}

const items = json($items);

// Prepare data for Pie Chart
const labels = items.map(item => item.item);
const data = items.map(item => item.item_sold);

const ctx = document.getElementById('pieChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: labels,
        datasets: [{
            label: 'Item Sold',
            data: data,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function (tooltipItem) {
                        const value = tooltipItem.raw;
                        return `${tooltipItem.label}: ${value} items`;
                    }
                }
            }
        }
    }
});

    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</body>

</html>
