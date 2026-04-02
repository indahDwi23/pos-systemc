@extends('layouts.main')

@section('container')
<div class="col-auto">
    <h1 class="app-page-title mb-0">Statistik Penjualan</h1>
</div>
<div class="col-10">
    <div class="page-utilities">
        <div class="row g-2 justify-content-between align-items-center">
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('statistics.index', ['filter' => 'today']) }}"
                       class="btn btn-sm {{ $filter === 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('statistics.index', ['filter' => '7days']) }}"
                       class="btn btn-sm {{ $filter === '7days' ? 'btn-primary' : 'btn-outline-primary' }}">
                        7 Hari
                    </a>
                    <a href="{{ route('statistics.index', ['filter' => '30days']) }}"
                       class="btn btn-sm {{ $filter === '30days' ? 'btn-primary' : 'btn-outline-primary' }}">
                        30 Hari
                    </a>
                    <a href="{{ route('statistics.index', ['filter' => '90days']) }}"
                       class="btn btn-sm {{ $filter === '90days' ? 'btn-primary' : 'btn-outline-primary' }}">
                        90 Hari
                    </a>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ url()->previous() ?: '/' }}" class="btn app-btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('section')
<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="modern-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="modern-stat-icon" style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="modern-stat-label text-white">Total Penjualan</div>
            <div class="modern-stat-value text-white">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</div>
            <small class="text-white opacity-75">{{ $filter === 'today' ? 'Hari Ini' : ($filter === '7days' ? '7 Hari Terakhir' : ($filter === '30days' ? '30 Hari Terakhir' : '90 Hari Terakhir')) }}</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="modern-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="modern-stat-icon" style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="modern-stat-label text-white">Total Pesanan</div>
            <div class="modern-stat-value text-white">{{ $totalOrders ?? 0 }}</div>
            <small class="text-white opacity-75">{{ $filter === 'today' ? 'Hari Ini' : ($filter === '7days' ? '7 Hari Terakhir' : ($filter === '30days' ? '30 Hari Terakhir' : '90 Hari Terakhir')) }}</small>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Daily Sales Chart -->
    <div class="col-lg-8">
        <div class="app-card app-card-shadow h-100">
            <div class="app-card-header p-3">
                <h4 class="app-card-title mb-0">
                    Penjualan {{ $filter === 'today' ? 'Hari Ini' : ($filter === '7days' ? '7 Hari Terakhir' : ($filter === '30days' ? '30 Hari Terakhir' : '90 Hari Terakhir')) }}
                </h4>
            </div>
            <div class="app-card-body p-3">
                @if(empty($chartData) || count(array_column($chartData, 'total')) == 0)
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>Belum ada data penjualan</p>
                    </div>
                @else
                    <canvas id="dailySalesChart" height="120"></canvas>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Method Chart -->
    <div class="col-lg-4">
        <div class="app-card app-card-shadow h-100">
            <div class="app-card-header p-3">
                <h4 class="app-card-title mb-0">Metode Pembayaran</h4>
            </div>
            <div class="app-card-body p-3">
                @if($salesByPaymentMethod->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-chart-pie fa-3x mb-3"></i>
                        <p>Belum ada data pembayaran</p>
                    </div>
                @else
                    <canvas id="paymentMethodChart" height="200"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Menu Items -->
<div class="row g-4">
    <div class="col-12">
        <div class="app-card app-card-shadow">
            <div class="app-card-header p-3">
                <h4 class="app-card-title mb-0">Menu Terlaris (Top 5)</h4>
            </div>
            <div class="app-card-body p-3">
                @if($topMenuItems->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-utensils fa-3x mb-3"></i>
                        <p>Belum ada data penjualan menu</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Menu</th>
                                    <th class="text-end">Total Terjual</th>
                                    <th class="text-end">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalQty = $topMenuItems->sum('total_qty');
                                @endphp
                                @foreach($topMenuItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->menu->name ?? '-' }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $item->total_qty }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width: 100px; height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $totalQty > 0 ? ($item->total_qty / $totalQty * 100) : 0 }}%">
                                                </div>
                                            </div>
                                            <span class="text-muted small">{{ number_format($totalQty > 0 ? ($item->total_qty / $totalQty * 100) : 0, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .modern-stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .modern-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .modern-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .modern-stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }

    .modern-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
    }

    .app-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .app-card-shadow {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }

    .app-card-header {
        border-bottom: 1px solid #e5e7eb;
    }

    .app-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1f2937;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Sales Chart
        @if(!empty($chartData) && count(array_column($chartData, 'total')) > 0)
        const dailySalesCtx = document.getElementById('dailySalesChart');
        if (dailySalesCtx) {
            new Chart(dailySalesCtx, {
                type: 'line',
                data: {
                    labels: {{ \Illuminate\Support\Js::from(array_column($chartData, 'date')) }},
                    datasets: [{
                        label: 'Penjualan',
                        data: {{ \Illuminate\Support\Js::from(array_column($chartData, 'total')) }},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }
        @endif

        // Payment Method Chart
        @if(!$salesByPaymentMethod->isEmpty())
        const paymentMethodCtx = document.getElementById('paymentMethodChart');
        if (paymentMethodCtx) {
            const paymentData = {{ \Illuminate\Support\Js::from($salesByPaymentMethod->pluck('total', 'payment_method')->toArray()) }};
            const paymentLabels = {
                cash: 'Cash',
                qris: 'QRIS'
            };

            // Convert values to numbers
            const numericPaymentData = Object.entries(paymentData).map(([key, value]) => [key, parseInt(value) || 0]);
            const paymentLabelsArray = numericPaymentData.map(([key]) => paymentLabels[key] || key);
            const paymentValues = numericPaymentData.map(([, value]) => value);

            new Chart(paymentMethodCtx, {
                type: 'doughnut',
                data: {
                    labels: paymentLabelsArray,
                    datasets: [{
                        data: paymentValues,
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return label + ': Rp ' + value.toLocaleString('id-ID') + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
        @endif
    });
</script>
@endpush
