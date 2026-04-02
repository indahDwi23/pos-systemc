@php
    $indonesianDate = \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y');
@endphp

<div class="col-lg-8 mb-4">
    <div class="modern-welcome-banner" style="background: linear-gradient(135deg, #5cb377 0%, #15a362 100%);">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2 class="mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="mb-0">Dashboard Kasir - {{ $indonesianDate }}</p>
            </div>
            <div class="col-md-5 text-center">
                <div style="font-size: 5rem;">💰</div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-4 order-1 order-lg-2">
    <div class="modern-stats-grid">
        <!-- Paid Orders Card -->
        <div class="modern-stat-card">
            <div class="modern-stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="modern-stat-label">Pesanan Dibayar</div>
            <div class="modern-stat-value">{{ $total_paid[0]->total_paid }}</div>
            <div class="modern-stat-change positive">
                <i class="fas fa-check"></i>
                <span>Selesai</span>
            </div>
        </div>

        <!-- Unpaid Orders Card -->
        <div class="modern-stat-card">
            <div class="modern-stat-icon danger">
                <i class="fas fa-clock"></i>
            </div>
            <div class="modern-stat-label">Pesanan Belum Dibayar</div>
            <div class="modern-stat-value">{{ $total_unpaid[0]->total_unpaid }}</div>
            <div class="modern-stat-change negative">
                <i class="fas fa-hourglass-half"></i>
                <span>Tertunda</span>
            </div>
        </div>

        <!-- Available Tables Card -->
        <div class="modern-stat-card">
            <div class="modern-stat-icon info">
                <i class="fas fa-chair"></i>
            </div>
            <div class="modern-stat-label">Meja Tersedia</div>
            <div class="modern-stat-value">{{ 20 - (int)$tables[0]->tables }}</div>
            <div class="modern-stat-change positive">
                <i class="fas fa-door-open"></i>
                <span>Kosong</span>
            </div>
        </div>

        <!-- Sales Card - Clickable -->
        <a href="{{ route('statistics.index') }}" class="modern-stat-card clickable-stat-card">
            <div class="modern-stat-icon primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="modern-stat-label">Penjualan Hari Ini</div>
            <div class="modern-stat-value">Rp {{ number_format($total_sales[0]->total_sales, 0, ',', '.') }}</div>
            <div class="modern-stat-change positive">
                <i class="fas fa-chart-bar"></i>
                <span>Lihat Statistik</span>
            </div>
        </a>
    </div>
</div>
