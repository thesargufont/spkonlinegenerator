@extends('layouts.layout')

@section('auth')
    <h4 class="pull-left page-title">Welcome Page</h4>
    <ol class="breadcrumb pull-right">
        <li><a href="#">{{Auth::user()->name}}</a></li>
        <li class="active">Welcome Page</li>
    </ol>
    <div class="clearfix"></div>
@endsection

@section('content')
    <div class="col-sm-12">
        <h5>Hallo, <b>{{Auth::user()->name}}</b>. Selamat Datang di <b> SPONGE - Surat Perintah Online Generator</b>.</h5>
    </div>
@endsection

{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}