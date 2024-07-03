@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Detail Working Order</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Detail Working Order</li>
</ol>
<div class="clearfix"></div>
@endsection

@section('content')
<div class="container">
    <div class="card-header">
        <span id="form_result"></span>
        <div class="btn-group" role="group">
            <div class="form-group">
                <button type="button" name="back" id="backBtn" class="btn btn-primary"><i class="fa fa-fw fa-arrow-left"></i> {{ucwords(__('Kembali'))}}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form class="form-horizontal" id="wo_form" enctype="multipart/form-data">
                @csrf
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Header</h3>
                    </div>
                    <div class="panel-body">
                        {{-- NOMOR SPK --}}
                        <div class="form-group">
                            <label class="col-md-2">NOMOR SPK</label>
                            <div class="col-md-6">
                                <input name="wo_number" id='wo_number' type="text" class="form-control" readonly="readonly" value="{{$spk_number}}">
                            </div>
                        </div>

                        {{-- NOMOR WORK ORDER --}}
                        <div class="form-group">
                            <label class="col-md-2">NOMOR WORK ORDER</label>
                            <div class="col-md-6">
                                <input name="wo_number" id='wo_number' type="text" class="form-control" readonly="readonly" value="{{$wo_number}}">
                            </div>
                        </div>

                        {{-- KATEGORI WORK ORDER --}}
                        <div class="form-group">
                            <label class="col-sm-2">KATEGORI WORK ORDER</label>
                            <div class="col-sm-6">
                                <input name="wo_category" id='wo_category' type="text" class="form-control" readonly="readonly" value="{{$wo_category}}">
                            </div>
                        </div>

                        {{-- DEPARTEMEN --}}
                        <div class="form-group">
                            <label class="col-sm-2">DEPARTEMEN</label>
                            <div class="col-sm-6">
                                <input name="department" id='department' type="text" class="form-control" readonly="readonly" value="{{$department}}">
                            </div>
                        </div>

                        {{-- KATEGORI PEKERJAAN --}}
                        <div class="form-group">
                            <label class="col-sm-2">KATEGORI PEKERJAAN</label>
                            <div class="col-sm-6">
                                <input name="job_category" id='job_category' type="text" class="form-control" readonly="readonly" value="{{$job_category}}">
                            </div>
                        </div>

                        {{-- TANGGAL EFEKTIF --}}
                        <div class="form-group">
                            <label class="col-sm-2">TANGGAL EFEKTIF</label>
                            <div class="col-sm-6">
                                <input name="effective_date" id='effective_date' type="text" class="form-control" readonly="readonly" value="{{$effective_date}}">
                            </div>
                        </div>
                    </div> <!-- panel-body -->
                </div> <!-- panel -->

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Form Detail</h3>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group" id="work-detail-container">

                            </div>
                            <div>
                                <button type="button" id="addDetailButton" class="btn btn-primary btn-sm waves-effect waves-light">+ Tambah Detail</button>
                            </div>
                        </form>
                    </div> <!-- panel-body -->
                </div> <!-- panel -->

            </form>

        </div> <!-- col -->
    </div>
</div>

<!-- Plugins js -->
@endsection

{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}

@section('script')

<script>
    $(document).ready(function() {
        $(document).on('click', '#backBtn', function() {
            window.location.href = "{{ route('form-input.working-order.index') }}";
        });
    });

    function showItem(id) {
        {
            window.location.href = "{{ route('form-input.working-order.detail', ['id' => $workingOrder - > id]) }}";
        }
    }
</script>
@endsection