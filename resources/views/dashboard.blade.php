@extends('layouts.layout')

@section('auth')
    <h4 class="pull-left page-title">Dashboard</h4>
    <ol class="breadcrumb pull-right">
        <li><a href="#">{{Auth::user()->name}}</a></li>
        <li class="active">Dashboard</li>
    </ol>
    <div class="clearfix"></div>
@endsection


@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel-body">
                    <style>
                        body {
                        background-image: url('images/background_page(2).png');
                        background-repeat: no-repeat;
                        background-position: center;
                        background-size: cover;
                        height: 1000px;
                        }
                    </style>
        
                <h5>Hallo, <b>{{Auth::user()->name}}</b>. Selamat Datang di <b> SPONGE - Surat Perintah Online Generator</b>.</h5>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}