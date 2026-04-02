@extends('layouts.main')

@section('container')

@can('owner')
    @section('container')
        @include('dashboard.owner')
    @endsection
@endcan

@can('cashier')
    @section('container')
        @include('dashboard.cashier')
    @endsection
@endcan

@endsection
