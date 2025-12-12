@extends('layouts.master')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.2/dist/echarts.min.js"></script>

    <!-- ECharts -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.2/dist/echarts.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Link CSS -->
    <link rel="stylesheet" href="{{ asset('css/masterdashboard.css') }}">

    <div class="container-fluid px-4">

        <h1 class="mt-4"><span style="font-weight:normal">Hello, <b></span> {{ strtoupper(Auth::user()->name) }}</b></h1><br>

    </div>

@endsection
