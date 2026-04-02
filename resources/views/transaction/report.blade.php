<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/logofood.ico">
    <title>Laporan Transaksi</title>

    <!-- FontAwesome JS-->
    <script defer src="/plugins/fontawesome/js/all.min.js"></script>

    <!-- App CSS -->
    <link rel="stylesheet" href="/css/portal.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/fontawesome-free-6.2.1-web/css/all.css">
</head>
<body>
<div class="container">
    <div class="row my-5 d-flex flex-column justify-content-between">
        <div class="col d-flex flex-column align-items-center">
            <img src="/images/logofood.png" class="mb-3" alt="" srcset="" width="60">
            <h2>Ayam Penyet Sultan</h2>
            <p class="mb-1">Jalan setia budi, No. 9</p>
            <p class="mb-1">62827272892</p>
        </div>
        <hr class="mt-3" style="border: 2px solid black;">
        <div class="col my-5">
            <h3 class="text-center">Laporan Transaksi</h3>
            <p class="mb-0 text-center">
                @php
                    $periodText = '';
                    if(isset($_GET['month']) && isset($_GET['year'])) {
                        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $periodText = 'transaksi ' . $months[$_GET['month'] - 1] . ' ' . $_GET['year'];
                    } elseif(isset($_GET['month'])) {
                        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $periodText = 'transaksi ' . $months[$_GET['month'] - 1];
                    } elseif(isset($_GET['year'])) {
                        $periodText = 'transaksi tahun ' . $_GET['year'];
                    } elseif(isset($_GET["data"]) && $_GET["data"] == 'all') {
                        $periodText = 'semua transaksi';
                    } elseif(isset($_GET["data"]) && $_GET["data"] == 'today') {
                        $periodText = 'transaksi hari ini';
                    } elseif(isset($_GET["data"]) && $_GET["data"] == 'thisMonth') {
                        $periodText = 'transaksi bulan ini';
                    } else {
                        $periodText = 'transaksi';
                    }
                @endphp
                {{ $periodText }}
            </p>
        </div>
    </div>
    <table class="table my-5">
        <thead>
        <tr>
            <th scope="col">Tanggal</th>
            <th scope="col">Pesanan</th>
            <th scope="col">No Meja</th>
            <th scope="col">Total</th>
            @can('owner')
            <th scope="col">Modal</th>
            <th scope="col">Profit</th>
            @endcan
        </tr>
        </thead>
        <tbody>
        @php
            $totalModal = 0;
            $totalProfit = 0;
            $totalTransaction = 0;
            $totalPayment = 0;
        @endphp
        @foreach ($data as $item)
        @php
            $modalItems = 0;
            $profitItems = 0;

            foreach ($item->transaction_details as $detail) {
                $modalItems += $detail->menu->modal * $detail->qty;
                $profitItems += ($detail->menu->price - $detail->menu->modal) * $detail->qty;
            }

            $totalModal += $modalItems;
            $totalProfit += $profitItems;
            $totalTransaction += $item->total_transaction;
            $totalPayment += $item->total_payment;

            // Normalize table display
            $mejaDisplay = ($item->order_type === 'takeaway' || strtolower($item->no_table) === 'bungkus' || strtolower($item->no_table) === 'takeaway') ? 'Bungkus' : $item->no_table;

            // Indonesian date format
            $indonesianDate = \Carbon\Carbon::parse($item->created_at)->locale('id')->translatedFormat('d M Y');
        @endphp
        <tr>
            <th scope="row">{{ $indonesianDate }}</th>
            <td class="w-50">
                @foreach ($item->transaction_details as $el)
                {{ $el->menu->name.' ('.$el->qty.'), ' }}
                @endforeach
            </td>
            <td>{{ $mejaDisplay }}</td>
            <td>Rp. {{ number_format($item->total_transaction, 0, ',', '.') }}</td>
            @can('owner')
            <td>Rp. {{ number_format($modalItems, 0, ',', '.') }}</td>
            <td>Rp. {{ number_format($profitItems, 0, ',', '.') }}</td>
            @endcan
        </tr>
        @endforeach
        </tbody>
        </table>
    </table>

    @can('owner')
    <div class="row my-5">
        <div class="col-8 offset-4">
            <table class="table table-bordered">
                <tr>
                    <td><strong>Total Penjualan</strong></td>
                    <td class="text-end">Rp. {{ number_format($totalTransaction, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Modal</strong></td>
                    <td class="text-end">Rp. {{ number_format($totalModal, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f0f0f0;">
                    <td><strong>Laba Bersih</strong></td>
                    <td class="text-end"><strong>Rp. {{ number_format($totalProfit, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
    </div>
    @endcan
</div>
<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
