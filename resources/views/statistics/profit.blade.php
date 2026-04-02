@extends('layouts.main')

@section('container')
<div class="col-auto">
    <h1 class="app-page-title mb-0">Statistik Keuntungan</h1>
</div>
<div class="col-10">
    <div class="page-utilities">
        <div class="row g-2 justify-content-between align-items-center">
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('statistics.profit', ['filter' => 'today']) }}"
                       class="btn btn-sm {{ $filter === 'today' ? 'btn-success' : 'btn-outline-success' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('statistics.profit', ['filter' => '7days']) }}"
                       class="btn btn-sm {{ $filter === '7days' ? 'btn-success' : 'btn-outline-success' }}">
                        7 Hari
                    </a>
                    <a href="{{ route('statistics.profit', ['filter' => '30days']) }}"
                       class="btn btn-sm {{ $filter === '30days' ? 'btn-success' : 'btn-outline-success' }}">
                        30 Hari
                    </a>
                    <a href="{{ route('statistics.profit', ['filter' => '90days']) }}"
                       class="btn btn-sm {{ $filter === '90days' ? 'btn-success' : 'btn-outline-success' }}">
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
    <div class="col-md-4">
        <div class="modern-stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="modern-stat-icon" style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="modern-stat-label text-white">Total Keuntungan</div>
            <div class="modern-stat-value text-white">Rp {{ number_format($totalProfit ?? 0, 0, ',', '.') }}</div>
            <small class="text-white opacity-75">{{ $filter === 'today' ? 'Hari Ini' : ($filter === '7days' ? '7 Hari Terakhir' : ($filter === '30days' ? '30 Hari Terakhir' : '90 Hari Terakhir')) }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="modern-stat-card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <div class="modern-stat-icon" style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="modern-stat-label text-white">Keuntungan Cash</div>
            <div class="modern-stat-value text-white">Rp {{ number_format($cashProfit ?? 0, 0, ',', '.') }}</div>
            <small class="text-white opacity-75">Pembayaran Tunai</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="modern-stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <div class="modern-stat-icon" style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="modern-stat-label text-white">Keuntungan QRIS</div>
            <div class="modern-stat-value text-white">Rp {{ number_format($qrisProfit ?? 0, 0, ',', '.') }}</div>
            <small class="text-white opacity-75">Pembayaran QRIS</small>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Daily Profit Chart -->
    <div class="col-lg-8">
        <div class="app-card app-card-shadow h-100">
            <div class="app-card-header p-3">
                <h4 class="app-card-title mb-0">
                    Keuntungan {{ $filter === 'today' ? 'Hari Ini' : ($filter === '7days' ? '7 Hari Terakhir' : ($filter === '30days' ? '30 Hari Terakhir' : '90 Hari Terakhir')) }}
                </h4>
            </div>
            <div class="app-card-body p-3">
                @if(empty($chartData) || collect($chartData)->sum('total') == 0)
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-wallet fa-3x mb-3"></i>
                        <p>Belum ada data keuntungan</p>
                    </div>
                @else
                    <canvas id="profitChart" height="120"></canvas>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Method Profit Chart -->
    <div class="col-lg-4">
        <div class="app-card app-card-shadow h-100">
            <div class="app-card-header p-3">
                <h4 class="app-card-title mb-0">Keuntungan per Metode</h4>
            </div>
            <div class="app-card-body p-3">
                @if(($cashProfit ?? 0) == 0 && ($qrisProfit ?? 0) == 0)
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

<!-- Top Profitable Menu Items -->
<div class="row g-4">
    <div class="col-12">
        <div class="app-card app-card-shadow">
            <div class="app-card-header p-3">
                <h4 class="app-card-title mb-0">Menu Paling Menguntungkan (Top 5)</h4>
            </div>
            <div class="app-card-body p-3">
                @if($topProfitableItems->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-utensils fa-3x mb-3"></i>
                        <p>Belum ada data keuntungan menu</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Menu</th>
                                    <th class="text-end">Total Terjual</th>
                                    <th class="text-end">Keuntungan</th>
                                    <th class="text-end">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalProfitItems = $topProfitableItems->sum('total_profit');
                                @endphp
                                @foreach($topProfitableItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->menu->name ?? '-' }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $item->total_qty }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success">Rp {{ number_format($item->total_profit, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width: 100px; height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $totalProfitItems > 0 ? ($item->total_profit / $totalProfitItems * 100) : 0 }}%">
                                                </div>
                                            </div>
                                            <span class="text-muted small">{{ number_format($totalProfitItems > 0 ? ($item->total_profit / $totalProfitItems * 100) : 0, 1) }}%</span>
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
        // Profit Chart
        @if(!empty($chartData) && collect($chartData)->sum('total') > 0)
        (function() {
            const ctx = document.getElementById('profitChart');
            if (!ctx) return;

            const labels = {{ \Illuminate\Support\Js::from(array_column($chartData, 'date')) }};
            const data = {{ \Illuminate\Support\Js::from(array_column($chartData, 'total')) }};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Keuntungan',
                        data: data,
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
        })();
        @endif

        // Payment Method Profit Chart
        @if(($cashProfit ?? 0) > 0 || ($qrisProfit ?? 0) > 0)
        (function() {
            const ctx = document.getElementById('paymentMethodChart');
            if (!ctx) return;

            const cashProfit = {{ $cashProfit ?? 0 }};
            const qrisProfit = {{ $qrisProfit ?? 0 }};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Cash', 'QRIS'],
                    datasets: [{
                        data: [cashProfit, qrisProfit],
                        backgroundColor: ['#3b82f6', '#8b5cf6'],
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
        })();
        @endif
    });
</script>
@endpush
