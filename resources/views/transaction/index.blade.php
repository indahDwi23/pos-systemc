@extends('layouts.main')

@section('container')
<div class="col-auto">
    <h1 class="app-page-title mb-0">Transaksi</h1>
</div>
<div class="col-10">
    <div class="page-utilities">
        <div class="row g-2 justify-content-start justify-content-md-end align-items-center">
            <div class="col-auto">
                <form action="/transaction" method="GET" class="table-search-form row gx-1 align-items-center">
                    <div class="col-auto">						    
                        <select class="form-select w-auto" name="year">
                            @if (!request('year'))
                            <option value="0" selected disabled>Pilih Tahun</option>
                            @else
                            <option value="0" disabled>Pilih Tahun</option>
                            @endif
                            @php
                                $year = date('Y');
                                $min = $year - 8;
                                $max = $year;
                            @endphp
                            @for ( $i=$max; $i>=$min; $i-- )
                                echo '<option value="{{ $i }}" {{ (request('year') == $i) ? 'selected' : ' ' }}>{{ $i }}</option>';
                            @endfor 
                        </select>
                    </div>
                    <div class="col-auto">
                        <select class="form-select w-auto" name="month">
                            @if (!request('month'))
                            <option value="0" selected disabled>Pilih Bulan</option>
                            @else
                            <option value="0" disabled>Pilih Bulan</option>
                            @endif
                            <option value="01" {{ (request('month') == '01') ? 'selected' : ' ' }}>Januari</option>
                            <option value="02" {{ (request('month') == '02') ? 'selected' : ' ' }}>Februari</option>
                            <option value="03" {{ (request('month') == '03') ? 'selected' : ' ' }}>Maret</option>
                            <option value="04" {{ (request('month') == '04') ? 'selected' : ' ' }}>April</option>
                            <option value="05" {{ (request('month') == '05') ? 'selected' : ' ' }}>Mei</option>
                            <option value="06" {{ (request('month') == '06') ? 'selected' : ' ' }}>Juni</option>
                            <option value="07" {{ (request('month') == '07') ? 'selected' : ' ' }}>Juli</option>
                            <option value="08" {{ (request('month') == '08') ? 'selected' : ' ' }}>Agustus</option>
                            <option value="09" {{ (request('month') == '09') ? 'selected' : ' ' }}>September</option>
                            <option value="10" {{ (request('month') == '10') ? 'selected' : ' ' }}>Oktober</option>
                            <option value="11" {{ (request('month') == '11') ? 'selected' : ' ' }}>November</option>
                            <option value="12" {{ (request('month') == '12') ? 'selected' : ' ' }}>Desember</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn app-btn-secondary">Cari</button>
                    </div>
                </form>
            </div>
            <div class="col-auto">
                <a class="btn app-btn-primary" href="/report?{{ isset($_GET['month']) && isset($_GET['year']) ? 'month='.$_GET['month'].'&year='.$_GET['year'] : (isset($_GET['month']) ? 'month='.$_GET['month'] : (isset($_GET['year']) ? 'year= '.$_GET['year'] : 'data=all')) }}"
                id="print"><i class="fa-solid fa-print me-2"></i>Cetak</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('section')
<nav id="orders-table-tab" class="orders-table-tab app-nav-tabs nav shadow-sm flex-column flex-sm-row mb-4">
    <a class="flex-sm-fill text-sm-center nav-link {{ Request::has('month') || Request::has('year') ? ' ' : 'active' }}" id="orders-all-tab" data-bs-toggle="tab" href="{{ Request::has('month') || Request::has('year') ? ' ' : '#orders-all' }}" role="tab" aria-controls="orders-all" aria-selected="{{ Request::has('month') || Request::has('year') ? 'false' : 'true' }}">Semua</a>
    <a class="flex-sm-fill text-sm-center nav-link"  id="orders-paid-tab" data-bs-toggle="tab" href="{{ Request::has('month') || Request::has('year') ? ' ' : '#orders-paid' }}" role="tab" aria-controls="orders-paid" aria-selected="false">Hari Ini</a>
    <a class="flex-sm-fill text-sm-center nav-link" id="orders-pending-tab" data-bs-toggle="tab" href="{{ Request::has('month') || Request::has('year') ? ' ' : '#orders-pending' }}" role="tab" aria-controls="orders-pending" aria-selected="false">Bulan Ini</a>
</nav>

<div class="tab-content" id="orders-table-tab-content">
    <div class="tab-pane fade show active" id="orders-all" role="tabpanel" aria-labelledby="orders-all-tab">
        <div class="app-card app-card-orders-table shadow-sm mb-5">
            <div class="app-card-body">
                <div class="table-responsive">
                    <table class="table app-table-hover mb-0 text-left">
                        <thead>
                            <tr>
                                <th class="cell">No</th>
                                <th class="cell">Pesanan</th>
                                <th class="cell text-center">No Meja</th>
                                <th class="cell">Tanggal</th>
                                <th class="cell">Status</th>
                                <th class="cell">Total</th>
                                @can('cashier')
                                <th class="cell"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($all as $item)
                                @php
                                    $datetime = explode(' ', $item->created_at)[0];
                                    $date = \Carbon\Carbon::parse($datetime);

                                    $times_ex = explode(' ', $item->created_at)[1];
                                    $times = \Carbon\Carbon::parse($times_ex);
                                    $time = explode(' ', $times->toDayDateTimeString());

                                    // Normalize table display
                                    $mejaDisplay = ($item->order_type === 'takeaway' || strtolower($item->no_table) === 'bungkus' || strtolower($item->no_table) === 'takeaway') ? 'Bungkus' : $item->no_table;
                                @endphp
                                <tr>
                                    <td class="cell">{{ $loop->iteration }}</td>
                                    <td class="cell">
                                        <span class="truncate">
                                            @foreach ($item->transaction_details as $el)
                                                {{ $el->menu->name.',' }}
                                            @endforeach
                                        </span>
                                    </td>
                                    <td class="cell text-center">{{ $mejaDisplay }}</td>
                                    <td class="cell"><span>{{ $date->toFormattedDateString() }}</span><span class="note">{{ $time[4].' '.$time[5] }}</span></td>
                                    <td class="cell"><span class="badge {{ $item->status == 'paid' ? 'bg-success' : 'bg-danger'}}">{{ $item->status == 'paid' ? 'Lunas' : 'Belum Lunas' }}</span></td>
                                    <td class="cell">IDR. {{ number_format($item->total_transaction, 0, ',', '.') }}</td>
                                    @can('cashier')
                                    <td class="cell"><a class="btn-sm app-btn-secondary" href="/transaction/{{ $item->id }}">Lihat</a></td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>		
        </div>
        {{ $all->withQueryString()->links() }}
    </div>
    
    <div class="tab-pane fade" id="orders-paid" role="tabpanel" aria-labelledby="orders-paid-tab">
        <div class="app-card app-card-orders-table mb-5">
            <div class="app-card-body">
                <div class="table-responsive">
                    
                    <table class="table mb-0 text-left">
                        <thead>
                            <tr>
                                <th class="cell">No</th>
                                <th class="cell">Pesanan</th>
                                <th class="cell text-center">No Meja</th>
                                <th class="cell">Tanggal</th>
                                <th class="cell">Status</th>
                                <th class="cell">Total</th>
                                @can('cashier')
                                <th class="cell"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($today as $item)
                                @php
                                    $datetime = explode(' ', $item->created_at)[0];
                                    $date = \Carbon\Carbon::parse($datetime);

                                    $times_ex = explode(' ', $item->created_at)[1];
                                    $times = \Carbon\Carbon::parse($times_ex);
                                    $time = explode(' ', $times->toDayDateTimeString());

                                    // Normalize table display
                                    $mejaDisplay = ($item->order_type === 'takeaway' || strtolower($item->no_table) === 'bungkus' || strtolower($item->no_table) === 'takeaway') ? 'Bungkus' : $item->no_table;
                                @endphp
                                <tr>
                                    <td class="cell">{{ $loop->iteration }}</td>
                                    <td class="cell">
                                        <span class="truncate">
                                            @foreach ($item->transaction_details as $el)
                                                {{ $el->menu->name.',' }}
                                            @endforeach
                                        </span>
                                    </td>
                                    <td class="cell text-center">{{ $mejaDisplay }}</td>
                                    <td class="cell"><span>{{ $date->toFormattedDateString() }}</span><span class="note">{{ $time[4].' '.$time[5] }}</span></td>
                                    <td class="cell"><span class="badge {{ $item->status == 'paid' ? 'bg-success' : 'bg-danger'}}">{{ $item->status == 'paid' ? 'Lunas' : 'Belum Lunas' }}</span></td>
                                    <td class="cell">IDR. {{ number_format($item->total_transaction, 0, ',', '.') }}</td>
                                    @can('cashier')
                                    <td class="cell"><a class="btn-sm app-btn-secondary" href="/transaction/{{ $item->id }}">Lihat</a></td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>	
            {{ $today->links() }}
        </div>
    </div>

    <div class="tab-pane fade" id="orders-pending" role="tabpanel" aria-labelledby="orders-pending-tab">
        <div class="app-card app-card-orders-table mb-5">
            <div class="app-card-body">
                <div class="table-responsive">
                    <table class="table mb-0 text-left">
                        <thead>
                            <tr>
                                <th class="cell">No</th>
                                <th class="cell">Pesanan</th>
                                <th class="cell text-center">No Meja</th>
                                <th class="cell">Tanggal</th>
                                <th class="cell">Status</th>
                                <th class="cell">Total</th>
                                @can('cashier')
                                <th class="cell"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($thisMonth as $item)
                                @php
                                    $datetime = explode(' ', $item->created_at)[0];
                                    $date = \Carbon\Carbon::parse($datetime);

                                    $times_ex = explode(' ', $item->created_at)[1];
                                    $times = \Carbon\Carbon::parse($times_ex);
                                    $time = explode(' ', $times->toDayDateTimeString());

                                    // Normalize table display
                                    $mejaDisplay = ($item->order_type === 'takeaway' || strtolower($item->no_table) === 'bungkus' || strtolower($item->no_table) === 'takeaway') ? 'Bungkus' : $item->no_table;
                                @endphp
                                <tr>
                                    <td class="cell">{{ $loop->iteration }}</td>
                                    <td class="cell">
                                        <span class="truncate">
                                            @foreach ($item->transaction_details as $el)
                                                {{ $el->menu->name.',' }}
                                            @endforeach
                                        </span>
                                    </td>
                                    <td class="cell text-center">{{ $mejaDisplay }}</td>
                                    <td class="cell"><span>{{ $date->toFormattedDateString() }}</span><span class="note">{{ $time[4].' '.$time[5] }}</span></td>
                                    <td class="cell"><span class="badge {{ $item->status == 'paid' ? 'bg-success' : 'bg-danger'}}">{{ $item->status == 'paid' ? 'Lunas' : 'Belum Lunas' }}</span></td>
                                    <td class="cell">IDR. {{ number_format($item->total_transaction, 0, ',', '.') }}</td>
                                    @can('cashier')
                                    <td class="cell"><a class="btn-sm app-btn-secondary" href="/transaction/{{ $item->id }}">Lihat</a></td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>		
        </div>
        {{ $thisMonth->links() }}
    </div>
</div>
<script>
    const print = document.getElementById('print');
    document.querySelectorAll('.nav-link').forEach(function(elem) {
      elem.addEventListener('click', function(event) {
        let id = elem.getAttribute('id');
        const currentUrl = window.location.href;
        const [baseUrl, queryString] = currentUrl.split('?');
        let newQueryString = '';
        if (queryString) {
            const queryParams = new URLSearchParams(queryString);
            queryParams.delete('month');
            queryParams.delete('year');
            newQueryString = queryParams.toString();
        } else {
            if (id == 'orders-paid-tab') {
                print.href = '/report?data=today';
            } else if(id == 'orders-pending-tab') {
                print.href = '/report?data=thisMonth';
            } else {
                print.href = '/report?data=all';
            }
            return false;
        }
        const newUrl = `${baseUrl}${newQueryString ? '?' : ''}${newQueryString}`;
        window.location.href = newUrl;
        event.preventDefault();
      });
    });

    print.addEventListener('click', function (event) {
        let hrefPrint = print.href.split('=')[1];
        if (hrefPrint == 'all') {
            if (document.querySelector('#orders-all table tbody').childElementCount == 0) {
                alert('data printed not avaible');
                print.style.backgroundColor = '#198754';
                print.style.color = '#fff';
                event.preventDefault();
            }
        } else if(hrefPrint == 'today') {
            if (document.querySelector('#orders-paid table tbody').childElementCount == 0) {
                alert('data printed not avaible');
                print.style.backgroundColor = '#198754';
                print.style.color = '#fff';
                event.preventDefault();
            }
        } else if(hrefPrint == 'thisMonth') {
            if (document.querySelector('#orders-pending table tbody').childElementCount == 0) {
                alert('data printed not avaible');
                print.style.backgroundColor = '#198754';
                print.style.color = '#fff';
                event.preventDefault();
            }
        } else {
            if (document.querySelector('#orders-all table tbody').childElementCount == 0) {
                alert('data printed not avaible');
                print.style.backgroundColor = '#198754';
                print.style.color = '#fff';
                event.preventDefault();
            }
        }
    })
</script>  
@endsection
