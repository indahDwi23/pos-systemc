@extends('layouts.order')

@section('container')
    @php
        $tables = json_encode($tables);
        echo "
            <script>
                var tables = $tables;
            </script>
        ";
    @endphp
    <div class="col-md-8 p-0 h-100 flex flex-column justify-content-between">
        <div class="hd-menu d-flex align-items-center justify-content-between shadow bg-white">
            <div class="col-sm-5 d-flex align-items-center">
                <a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img">
                        <path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"
                            d="M4 7h22M4 15h22M4 23h22"></path>
                    </svg>
                </a>
                <h5 class="fs-5 fw-bold text-black ms-4">Semua Menu</h5>
            </div>
            {{-- <div class="col-sm-7 d-flex align-items-center search-container-tr">
                <div class="search-mobile-trigger search-icon-transaction">
                    <i class="search-mobile-trigger-icon fas fa-search"></i>
                </div>
                <div class="app-search-box sb-tr">
                    <form class="app-search-form">
                        <input type="text" placeholder="Search..." name="search" class="form-control search-input">
                        <button type="submit" class="btn search-btn btn-primary" value="Search"><i
                                class="fas fa-search"></i></button>
                    </form>
                </div>
            </div> --}}
        </div>
        <div class="wp-menu d-flex flex-column">
            <div class="menu-tr mt-3 mb-3">
                <ul class="nav nav-tabs d-flex justify-content-center" data-aos="fade-up" data-aos-delay="200">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#food">
                            <h4>Makanan</h4>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#drink">
                            <h4>Minuman</h4>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content menu-tab overflow-auto" style="height: 85%" data-aos="fade-up" data-aos-delay="300">
                <div class="tab-pane fade active show" id="food">
                    <div class="menu-content pe-4 ps-4 d-flex flex-wrap justify-content-between">
                        @foreach ($foods as $food)
                            <div class="menu-item-cart rounded shadow d-flex align-items-center justify-content-around"
                                data-id="{{ $food->id }}" style="margin-bottom: 7%;">
                                <img src="{{ asset('storage/' . $food->picture) }}" alt=""
                                    style="width: 150px; height: 100px !important; object-fit: cover !important; border-radius: 8px;">
                                <div class="d-flex justify-content-center flex-column">
                                    <div class="product">
                                        <h5 style="font-size: 16px; width: 100px;" class="text-break">{{ $food->name }}</h5>
                                        <h6 style="font-size: 13px;">{{ number_format($food->price, 0, ',', '.') }}</h6>
                                    </div>
                                    <div class="qty d-flex mt-3">
                                        <button class="border-0 rounded bg-transparent RemovetoCart"><i
                                                class="fa-solid fa-minus" style="font-size: 12px;"></i></button>
                                        <div class="qty-numbers me-3 ms-3">
                                            0
                                        </div>
                                        <button class="border-0 rounded bg-transparent AddtoCart"><i
                                                class="fa-solid fa-plus" style="font-size: 12px;"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="drink">
                    <div class="menu-content pe-4 ps-4 d-flex flex-wrap justify-content-between">
                        @foreach ($drinks as $drink)
                            <div class="menu-item-cart rounded shadow d-flex align-items-center justify-content-around"
                                data-id="{{ $drink->id }}" style="margin-bottom: 7%;">
                                <img class="img-fluid" src="{{ asset('storage/' . $drink->picture) }}" alt=""
                                    srcset="" width="150">
                                <div class="d-flex justify-content-center flex-column">
                                    <div class="product">
                                        <h5 style="font-size: 16px; width: 100px;" class="text-break">{{ $drink->name }}</h5>
                                        <h6 style="font-size: 13px;">{{ number_format($drink->price, 0, ',', '.') }}</h6>
                                    </div>
                                    <div class="qty d-flex mt-3">
                                        <button class="border-0 rounded bg-transparent RemovetoCart"><i
                                                class="fa-solid fa-minus" style="font-size: 12px;"></i></button>
                                        <div class="qty-numbers me-3 ms-3">
                                            0
                                        </div>
                                        <button class="border-0 rounded bg-transparent AddtoCart"><i
                                                class="fa-solid fa-plus" style="font-size: 12px;"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 h-100 p-0 d-flex flex-column">
        <div class="cart-title d-flex justify-content-between align-items-center p-4 shadow-sm">
            <h5 class="text-white">Pesanan Saat Ini</h5>
            <button class="fas fa-broom text-white" onclick="deleteOrder()" role="button"></button>
        </div>
        <div class="cart-body d-flex flex-column justify-content-between" style="height: 780px;">
            <div class="d-flex justify-content-between p-3 align-items-center">
                <h6 class="fw-semibold text-white ms-2 tables-selected">Meja</h6>
                <h6 class="fw-semibold text-white me-2" style="font-size: 13px;">{{ now()->format('Y-m-d') }}</h6>
            </div>

            <!-- Dine-in / Takeaway Options -->
            <div class="d-flex justify-content-center gap-3 mb-3 px-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="order_type_option" id="dine_in" value="dine_in" checked onchange="handleOrderTypeChange('dine_in')">
                    <label class="form-check-label text-white" for="dine_in">
                        Makan di Tempat
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="order_type_option" id="takeaway" value="takeaway" onchange="handleOrderTypeChange('takeaway')">
                    <label class="form-check-label text-white" for="takeaway">
                        Bungkus
                    </label>
                </div>
            </div>

            <div class="list-order align-self-center rounded p-4 mb-4">
                <div class="menu-order">

                </div>
            </div>
            <form action="/transaction" method="POST" class="align-self-center p-0 m-0" style="width: 90%;" id="orderForm" onsubmit="return validateOrder()">
                @csrf
                <input type="hidden" name="menu_id" id="menu_id">
                <input type="hidden" name="order_type" id="order_type" value="dine_in">
                <input type="hidden" name="no_table" id="table_selected">
                <div class="cart-payment p-2 d-flex flex-column rounded">
                    <div class="section-transaction d-flex justify-content-between align-items-center mt-3 p-2">
                        <h6 class="text-white">Total</h6>
                        <h6 class="total-transaction text-white">Rp 0</h6>
                        <input type="hidden" name="total_transaction">
                    </div>
                    <div class="section-pay d-flex justify-content-between align-items-center p-2" id="table-selection">
                        <h6 class="text-white">Pilih Meja</h6>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#table">Pilih</button>
                    </div>
                </div>
                <button type="submit"
                    class="w-100 cart-order p-3 mt-3 mb-3 rounded text-center border-0 text-dark bg-white">
                    Buat Pesanan
                </button>
            </form>
        </div>
    </div>
    <div class="modal fade" id="table" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl">
            <div class="modal-content shadow" style="background-color: #181818fd">
                <div class="modal-header" id="staticBackdropLabel">
                    <h1 class="modal-title fs-5 text-white" id="exampleModalLabel">Pilih Meja</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="background-color: #fff"></button>
                </div>
                <div class="modal-body p-5" style="background-color: #2c2c2c;">
                    <div class="row g-4 justify-content-center">
                        @for($i = 1; $i <= 10; $i++)
                            <div class="col-3">
                                <div class="tab-container position-relative d-flex justify-content-center">
                                    <img class="table-item" src="/images/table/meja4.png" width="120"
                                        srcset="" data-table="not-selected" data-number="{{ $i }}">
                                    <p class="position-absolute top-50 start-50 translate-middle fw-bold text-tables">
                                        {{ $i }}</p>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary text-white" data-bs-dismiss="modal">Pilih</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function handleOrderTypeChange(type) {
            const tableSelection = document.getElementById('table-selection');
            const orderType = document.getElementById('order_type');
            const tablesSelected = document.querySelector('.tables-selected');
            const tableSelected = document.getElementById('table_selected');

            if (type === 'dine_in') {
                // Show table selection
                tableSelection.style.display = 'flex';
                tablesSelected.innerText = 'Meja';
                orderType.value = 'dine_in';
            } else {
                // Hide table selection and clear selected table
                tableSelection.style.display = 'none';
                tablesSelected.innerText = 'Bungkus';
                tableSelected.value = 'Bungkus';
                orderType.value = 'takeaway';

                // Clear table selection
                const containers = document.querySelectorAll('.tab-container');
                for (let i = 0; i < containers.length; i++) {
                    let tbl_img = containers[i].children.item(0);
                    let ifselected = tbl_img.getAttribute('data-table');
                    let text = containers[i].children.item(1);

                    if (ifselected == 'selected') {
                        tbl_img.src = '/images/table/meja4.png';
                        tbl_img.setAttribute('data-table', 'not-selected');
                        text.style.color = '#000';
                    }
                }
            }
        }

        function validateOrder() {
            const orderType = document.getElementById('order_type').value;
            const tableSelected = document.getElementById('table_selected').value;
            const menuId = document.getElementById('menu_id').value;

            // Check if cart is empty
            if (!menuId || menuId === '[]') {
                alert('Keranjang masih kosong! Silakan pilih menu terlebih dahulu.');
                return false;
            }

            // Check if dine-in and no table selected
            if (orderType === 'dine_in' && (!tableSelected || tableSelected === ' ' || tableSelected === '')) {
                alert('Silakan pilih meja terlebih dahulu!');
                return false;
            }

            return true;
        }
    </script>
    <script src="/js/order.js"></script>
    <script src="/js/formatmoney.js"></script>
@endsection
