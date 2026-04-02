<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/logofood.ico">
    <title>Invoice</title>

    <!-- FontAwesome JS-->
    <script defer src="/plugins/fontawesome/js/all.min.js"></script>

    <!-- App CSS -->
    <link rel="stylesheet" href="/css/portal.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/fontawesome-free-6.2.1-web/css/all.css">

    <style>
        /* Styling layar HP agar memusatkan struk dan menyediakan tombol manual */
        body {
            background-color: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
        }
        .struk-container {
            width: 58mm; /* Standar printer thermal mobile */
            background: #fff;
            padding: 3mm;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .no-print {
            width: 58mm;
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }
        .btn-action {
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        @media print {
            @page {
                size: 58mm auto; /* Diubah ke ukuran standar mobile printer */
                margin: 0;
                padding: 0;
            }
            * {
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }
            body {
                width: 58mm;
                font-size: 11px;
                font-family: 'Courier New', Courier, monospace;
                color: #000;
                padding: 0;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
            .card, .card-body {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
                width: 100%;
            }
            p, h2, li {
                margin: 1px 0 !important;
                padding: 0 !important;
            }
            hr {
                margin: 3px 0 !important;
                border-top: 1px dashed #000;
            }
            .text-center {
                text-align: center;
            }
            .item-row {
                display: flex;
                justify-content: space-between;
                margin: 2px 0;
            }
            .item-name {
                flex: 1;
            }
            .item-price {
                text-align: right;
                white-space: nowrap;
            }
            .total-row {
                display: flex;
                justify-content: space-between;
                margin: 3px 0;
            }
            .total-label {
                flex: 1;
            }
            .total-value {
                text-align: right;
            }
            img {
                display: none;
            }
            ul {
                list-style: none;
                padding: 0;
                margin: 3px 0;
            }
            li {
                padding: 1px 0;
            }
            .row {
                display: block;
                margin: 0;
            }
            [class*="col-"] {
                display: block;
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol aksi untuk mobile -->
    <div class="no-print">
        <!-- RawBT app intent (sangat disarankan untuk Android) -->
        <a id="rawbt-print-btn" class="btn-action" style="background-color: #10b981;" href="#">🖨️ Cetak Struk</a>
        <button class="btn-action" style="background-color: #ef4444;" onclick="window.close()">❌ Tutup</button>
    </div>

    <div class="card struk-container">
        <div class="card-body p-0">
            <div class="container">
                <p class="text-center" style="font-size: 14px; font-weight: bold;">Ayam Penyet Sultan</p>
                <p class="text-center" style="font-size: 10px;">Jl. Setia Budi No. 10</p>
                <p class="text-center" style="font-size: 10px;">628228292</p>
                <hr>

                @foreach ($data as $key)
                    @php
                        $fullDateTime = \Carbon\Carbon::parse($key->created_at)->setTimezone('Asia/Jakarta');
                        $kembalian = $key->total_payment - $key->total_transaction;
                        // Normalize table display - show "Bungkus" for takeaway orders
                        $mejaDisplay = ($key->order_type === 'takeaway' || strtolower($key->no_table) === 'bungkus' || strtolower($key->no_table) === 'takeaway') ? 'Bungkus' : $key->no_table;
                    @endphp

                    <ul>
                        <li>Kasir : {{ $key->user->name }}</li>
                        <li>Meja : {{ $mejaDisplay }}</li>
                        <li>Tanggal : {{ $fullDateTime->format('d/m/Y H:i') }}</li>
                    </ul>
                    <hr>

                    @foreach ($key->transaction_details as $item)
                        <div class="item-row">
                            <div class="item-name">
                                {{ $item->menu->name }}<br>
                                <span style="font-size: 10px;">{{ $item->qty }} x {{ number_format($item->menu->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="item-price">{{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                    @endforeach

                    <hr>
                    <hr style="border: 1px solid black;">

                    <div class="total-row">
                        <span class="total-label">Total</span>
                        <span class="total-value total-display">{{ number_format($key->total_transaction, 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Metode</span>
                        <span class="total-value">@if($key->payment_method === 'qris') QRIS @else Cash @endif</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Bayar</span>
                        <span class="total-value payment-display">{{ number_format($key->total_payment, 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row kembalian-row" style="display: @if($kembalian > 0) flex !important @else none @endif;">
                        <span class="total-label">Kembalian</span>
                        <span class="total-value kembalian-display">{{ number_format($kembalian, 0, ',', '.') }}</span>
                    </div>
                    <hr style="border: 1px solid black;">
                @endforeach

                <div class="text-center" style="margin-top: 10px;">
                    <p>Terima kasih telah mampir</p>
                    <p>***</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function () {
            let totalPaymentElements = document.querySelectorAll('.payment-display');

            totalPaymentElements.forEach(function(element) {
                let currentText = element.innerText.replace(/\./g, '');
                if (currentText == '0') {
                    let paymentFromStorage = localStorage.getItem('payment');
                    if (paymentFromStorage) {
                        element.innerText = paymentFromStorage;

                        // Recalculate kembalian
                        let totalElement = element.closest('.card-body').querySelector('.total-display');
                        let kembalianRow = element.closest('.card-body').querySelector('.kembalian-row');
                        let kembalianDisplay = element.closest('.card-body').querySelector('.kembalian-display');

                        if (totalElement && kembalianRow && kembalianDisplay) {
                            let totalValue = parseInt(totalElement.innerText.replace(/\./g, ''));
                            let paymentValue = parseInt(paymentFromStorage.replace(/\./g, ''));
                            let kembalian = paymentValue - totalValue;

                            if (kembalian > 0) {
                                kembalianDisplay.innerText = kembalian.toLocaleString('id-ID');
                                kembalianRow.style.setProperty('display', 'flex', 'important');
                            }
                        }
                    }
                }
            });

            localStorage.clear();
            
            // Jeda sedikit agar CSS render sempurna di layar HP sebelum print dijalankan otomatis
            setTimeout(function() {
                // window.print(); // Printing is now handled by RawBT button
            }, 500);

            // --- Start of new code for RawBT ---
            function generateRawBtText() {
                let text = '';
                const struk = document.querySelector('.struk-container');
                const receiptWidth = 32; // Approx characters for 58mm thermal paper

                // Helper functions for alignment
                const alignRight = (text, width) => text.padStart(width, ' ');
                const alignCenter = (text, width) => {
                    if (text.length >= width) return text;
                    const padding = Math.floor((width - text.length) / 2);
                    return ' '.repeat(padding) + text;
                };

                // Header
                text += alignCenter(struk.querySelector('p.text-center[style*="bold"]').innerText.trim(), receiptWidth) + '\n';
                text += alignCenter(struk.querySelectorAll('p.text-center')[1].innerText.trim(), receiptWidth) + '\n';
                text += alignCenter(struk.querySelectorAll('p.text-center')[2].innerText.trim(), receiptWidth) + '\n';
                text += '-'.repeat(receiptWidth) + '\n';

                // Transaction Info
                struk.querySelectorAll('ul li').forEach(li => {
                    const parts = li.innerText.split(':');
                    const label = parts[0].trim();
                    const value = parts.slice(1).join(':').trim();
                    text += `${label.padEnd(10, ' ')}: ${value}\n`;
                });
                text += '-'.repeat(receiptWidth) + '\n';

                // Items
                struk.querySelectorAll('.item-row').forEach(item => {
                    const name = item.querySelector('.item-name').childNodes[0].nodeValue.trim();
                    const qtyPrice = item.querySelector('.item-name span').innerText.trim();
                    const totalPrice = item.querySelector('.item-price').innerText.trim();
                    
                    text += name + '\n';
                    const line = `${qtyPrice}${alignRight(totalPrice, receiptWidth - qtyPrice.length)}`;
                    text += line + '\n';
                });

                text += '-'.repeat(receiptWidth) + '\n';

                // Totals
                struk.querySelectorAll('.total-row').forEach(row => {
                    // Check if the row is visible
                    if (row.style.display === 'none') return;

                    const label = row.querySelector('.total-label').innerText.trim();
                    const value = row.querySelector('.total-value').innerText.trim();
                    const line = `${label}${alignRight(value, receiptWidth - label.length)}`;
                    text += line + '\n';
                });
                text += '-'.repeat(receiptWidth) + '\n\n';

                // Footer
                const footerElements = struk.querySelectorAll('.text-center p');
                text += alignCenter(footerElements[footerElements.length - 2].innerText.trim(), receiptWidth) + '\n';
                text += alignCenter(footerElements[footerElements.length - 1].innerText.trim(), receiptWidth) + '\n';
                
                return text;
            }

            try {
                const rawBtText = generateRawBtText();
                const encodedText = encodeURIComponent(rawBtText);
                const rawBtBtn = document.getElementById('rawbt-print-btn');
                rawBtBtn.href = `intent:${encodedText}#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;`;
            } catch (e) {
                console.error("Failed to generate RawBT link:", e);
            }
            // --- End of new code for RawBT ---

        }
    </script>
</body>
</html>
