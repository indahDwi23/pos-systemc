@extends('layouts.order')

@section('container')
<div class="row justify-content-center align-items-center">
    <div class="col-xl-12">
            <div class="row justify-content-around">
                
                <div class="col-md-5">
                    <div class="card border-0 ">
                        <div class="card-header card-2">
                            <p class="card-text text-muted mt-md-4  mb-2 space">DAFTAR PESANAN</p>
                            <hr class="my-2">
                        </div>
                        <div class="card-body pt-0">
                            <div style="max-height: 350px; overflow-y: auto; overflow-x: hidden;">
                            @foreach ($data as $key)
                                @foreach ($key->transaction_details as $item)
                                <div class="row  justify-content-between mb-3 pe-2">
                                    <div class="col-auto col-md-7">
                                        <div class="media flex-column flex-sm-row">
                                            <img class=" img-fluid" src="{{ asset('storage/'.$item->menu->picture) }}" width="62" height="62">
                                            <div class="media-body  my-auto">
                                                <div class="row ">
                                                    <div class="col-auto"><p class="mb-0"><b>{{ $item->menu->name }}</b></p><small class="text-muted">{{ $item->menu->category == 'food' ? 'Makanan' : ($item->menu->category == 'drink' ? 'Minuman' : 'Makanan Lain') }}</small></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" pl-0 flex-sm-col col-auto  my-auto"> <p class="boxed-1">{{ $item->qty }}</p></div>
                                    <div class=" pl-0 flex-sm-col col-auto  my-auto "><p><b>{{ number_format($item->price, 0, ',', '.') }}</b></p></div>
                                </div>
                                @endforeach
                            @endforeach
                            </div>
                            <hr class="my-2">
                            @foreach ($data as $key)
                            <div class="row">
                                <div class="col">
                                    <div class="row justify-content-between mb-2">
                                        <div class="col-4"><p class="mb-1"><b>Total</b></p></div>
                                        <div class="flex-sm-col col-auto">
                                            <p  class="mb-1"><b>Rp {{ number_format($key->total_transaction, 0, ',', '.') }}</b></p> </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card border-0">
                        <div class="card-header pb-0">
                            <h2 class="card-title space ">Pembayaran</h2>
                            <p class="card-text text-muted mt-4  space">DETAIL PEMBAYARAN</p>
                            <hr class="my-0">
                        </div>
                        @foreach($data as $key)
                        @if($key->status == 'paid')
                        <div class="card-body text-center">
                            <input type="hidden" name="id" id="id_transaction" value="{{ $key->id }}">

                            <!-- Success Icon -->
                            <div class="mb-4">
                                <div class="success-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </div>
                            </div>

                            <!-- Success Message -->
                            <h3 class="text-success mb-4">Pembayaran Berhasil!</h3>

                            <!-- Transaction Details -->
                            <div class="payment-details bg-light bg-opacity-75 rounded p-4 mb-4">
                                @php
                                    // Normalize table display - show "Bungkus" for takeaway
                                    $mejaDisplay = ($key->order_type === 'takeaway' || strtolower($key->no_table) === 'bungkus' || strtolower($key->no_table) === 'takeaway') ? 'Bungkus' : $key->no_table;
                                    $kembalian = $key->total_payment - $key->total_transaction;
                                @endphp
                                <div class="row mb-3">
                                    <div class="col-6 text-end text-muted">No. Meja</div>
                                    <div class="col-6 text-start text-dark fw-bold">{{ $mejaDisplay }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6 text-end text-muted">Total Transaksi</div>
                                    <div class="col-6 text-start text-dark fw-bold">Rp {{ number_format($key->total_transaction ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6 text-end text-muted">Total Pembayaran</div>
                                    <div class="col-6 text-start text-success fw-bold">Rp {{ number_format($key->total_payment ?? 0, 0, ',', '.') }}</div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6 text-end text-muted">Kembalian</div>
                                    <div class="col-6 text-start text-warning fw-bold">Rp {{ number_format($kembalian, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="/invoice/{{ $key->id }}" target="_blank" class="btn btn-outline-light w-100 py-2">
                                        <i class="fas fa-print me-2"></i>Cetak Struk
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="/invoice/{{ $key->id }}" target="_blank" class="btn btn-success w-100 py-2">
                                        <i class="fas fa-eye me-2"></i>Lihat Struk
                                    </a>
                                </div>
                            </div>
                        </div>

                        @else
                        <form action="/transaction/{{ $key->id }}" method="POST" class="card-body">
                            @csrf
                            @method('put')
                            <input type="hidden" name="id" id="id_transaction" value="{{ $key->id }}">
                            <div class="form-group mb-3">
                                <label for="name_cashier" class="small text-muted fw-semibold mb-1">NAME ON CASHIER</label>
                                <input type="text" class="form-control form-control-sm" disabled name="name_cashier" id="name_cashier" value="{{ $key->user ? $key->user->name : '-' }}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_table" class="small text-muted fw-semibold mb-1">NO TABLE</label>
                                <input type="text" class="form-control form-control-sm" disabled name="no_table" id="no_table" value="{{ $key->no_table }}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="total_transaction" class="small text-muted fw-semibold mb-1">TOTAL TRANSACTION</label>
                                <input type="text" class="form-control form-control-sm" disabled name="total_transaction" id="total_transaction" value="{{ number_format($key->total_transaction, 0, ',', '.') }}">
                                <input type="hidden" id="total" name="total_transaction" value="{{ $key->total_transaction }}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="total_payment" class="small text-muted fw-semibold mb-1">TOTAL PAYMENT</label>
                                <input type="text" class="form-control form-control-sm total_payment @error('total_payment') is-invalid @enderror" id="price">
                                <input type="hidden" name="total_payment" id="total_payment">
                                @error('total_payment')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label class="small text-muted fw-semibold mb-1">METODE PEMBAYARAN</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" checked>
                                        <label class="form-check-label" for="payment_cash">
                                            <i class="fas fa-money-bill-wave me-1"></i> Cash
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_qris" value="qris">
                                        <label class="form-check-label" for="payment_qris">
                                            <i class="fas fa-qrcode me-1"></i> QRIS
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- Quick Amount Buttons -->
                            <div class="form-group mb-3">
                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    <button type="button" class="btn btn-sm btn-success exact-amount-btn" data-total="{{ $key->total_transaction }}">
                                        <i class="fas fa-check-circle me-1"></i>UANG PAS
                                    </button>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-amount="20000">20K</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-amount="30000">30K</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-amount="40000">40K</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-amount="50000">50K</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-amount="100000">100K</button>
                                </div>
                            </div>
                            <div class="row mb-md-5 mt-4">
                                <div class="col">
                                    <button type="submit" class="text-white btn btn-primary w-100 btn-block">BAYAR {{ number_format($key->total_transaction, 0, ',', '.') }}</button>
                                </div>
                            </div>   
                        </form>
                        @endif
                        @endforeach 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const total_paymentFormat = document.querySelector('.total_payment');
    const total_paymentInput = document.getElementById('total_payment');

    total_paymentFormat.addEventListener('keyup', function() {
        total_paymentInput.value = parseInt(this.value.replace('.', ''));
    });

    // Quick amount buttons handler
    document.querySelectorAll('.quick-amount').forEach(function(button) {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            total_paymentFormat.value = formatRupiah(amount);
            total_paymentInput.value = amount;
        });
    });

    // Exact amount button handler
    document.querySelectorAll('.exact-amount-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const totalAmount = this.getAttribute('data-total');
            total_paymentFormat.value = formatRupiah(totalAmount);
            total_paymentInput.value = totalAmount;
        });
    });

    function show_my_receipt1() {
        var page = '/invoice/'+ document.getElementById('id_transaction').value;
        var total_payment = document.getElementById("price");
        if (!total_payment.value) {
            return false;
        } else {
            localStorage.setItem('payment', total_payment.value)
            var myWindow = window.open(page, "_blank");
            myWindow.focus();
        }
    }

    function show_my_receipt2() {
        var page = '/invoice/'+ document.getElementById('id_transaction').value;
        var total_payment = parseInt(document.getElementById("price").value.replace('.',''));
        var total_transaction = parseInt(document.getElementById("total").value);
        if (total_payment < total_transaction) {
            return false;
        } else {
            localStorage.setItem('payment', document.getElementById("price").value)
            var myWindow = window.open(page, "_blank");
            myWindow.focus();
        }
    }
</script>
<script src="/js/formatmoney.js"></script>
@endsection