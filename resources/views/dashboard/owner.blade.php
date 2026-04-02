@php
    $indonesianDate = \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<div class="col-lg-8 mb-4">
    <div class="modern-welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2 class="mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="mb-0">Ringkasan Dashboard - {{ $indonesianDate }}</p>
            </div>
            <div class="col-md-5 text-center">
                <div style="font-size: 5rem;">🍗</div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-4 order-1 order-lg-2">
    <div class="modern-stats-grid">
        <!-- Sales Card -->
        <a href="{{ route('statistics.index') }}" class="modern-stat-card clickable-stat-card">
            <div class="modern-stat-icon primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="modern-stat-label">Total Penjualan</div>
            <div class="modern-stat-value">Rp {{ number_format($total_sales[0]->total_sales ?? 0, 0, ',', '.') }}</div>
            <div class="modern-stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <span>Pendapatan</span>
            </div>
        </a>

        <!-- Menu Card -->
        <div class="modern-stat-card">
            <div class="modern-stat-icon success">
                <i class="fas fa-utensils"></i>
            </div>
            <div class="modern-stat-label">Total Menu</div>
            <div class="modern-stat-value">{{ $total_menus }}</div>
            <div class="modern-stat-change positive">
                <i class="fas fa-check-circle"></i>
                <span>Item</span>
            </div>
        </div>

        <!-- Income Card -->
        <a href="{{ route('statistics.profit') }}" class="modern-stat-card clickable-stat-card-profit">
            <div class="modern-stat-icon info">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="modern-stat-label">Keuntungan</div>
            <div class="modern-stat-value">Rp {{ number_format($total_income[0]->total_income ?? 0, 0, ',', '.') }}</div>
            <div class="modern-stat-change positive">
                <i class="fas fa-coins"></i>
                <span>Laba</span>
            </div>
        </a>

        <!-- Invoice Card -->
        <div class="modern-stat-card">
            <div class="modern-stat-icon warning">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="modern-stat-label">Transaksi</div>
            <div class="modern-stat-value">{{ $invoice[0]->total_invoice ?? 0 }}</div>
            <div class="modern-stat-change positive">
                <i class="fas fa-shopping-cart"></i>
                <span>Pesanan</span>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .clickable-stat-card {
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .clickable-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.2);
    }

    .clickable-stat-card-profit {
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .clickable-stat-card-profit:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2);
    }
</style>
@endpush
