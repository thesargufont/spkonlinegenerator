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

                <!-- <div class="col-lg-6"> -->
                <div class="panel-group" id="accordion-test-2">
                    @foreach($details as $index => $detail)
                    <div class="panel panel-info panel-color">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-{{ $index }}" aria-expanded="false" class="collapsed">
                                    Detail Work Order #{{$index}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne-{{ $index }}" class="panel-collapse collapse">
                            <div class="panel-body">
                                {{-- LOKASI --}}
                                <div class="form-group">
                                    <label class="col-md-2">LOKASI</label>
                                    <div class="col-md-6">
                                        <input name="detail_location_{{ $index }}" id="detail_location_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['location'] }}">
                                    </div>
                                </div>

                                {{-- ALAT --}}
                                <div class="form-group">
                                    <label class="col-md-2">ALAT</label>
                                    <div class="col-md-6">
                                        <input name="detail_device_{{ $index }}" id="detail_device_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device'] }}">
                                    </div>
                                </div>

                                {{-- MODEL ALAT --}}
                                <div class="form-group">
                                    <label class="col-sm-2">MODEL ALAT</label>
                                    <div class="col-sm-6">
                                        <input name="detail_device_model_{{ $index }}" id="detail_device_model_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device_model'] }}">
                                    </div>
                                </div>

                                {{-- KODE ALAT --}}
                                <div class="form-group">
                                    <label class="col-sm-2">KODE ALAT</label>
                                    <div class="col-sm-6">
                                        <input name="detail_device_code_{{ $index }}" id="detail_device_code_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device_code'] }}">
                                    </div>
                                </div>

                                {{-- KATEGORI GANGGUAN --}}
                                <div class="form-group">
                                    <label class="col-sm-2">KATEGORI GANGGUAN</label>
                                    <div class="col-sm-6">
                                        <input name="detail_disturbance_category_{{ $index }}" id="detail_disturbance_category_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['disturbance_category'] }}">
                                    </div>
                                </div>

                                {{-- DESKRIPSI PELAPORAN --}}
                                <div class="form-group">
                                    <label class="col-sm-2">DESKRIPSI PELAPORAN</label>
                                    <div class="col-sm-6">
                                        <input name="detail_description_{{ $index }}" id="detail_description_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['description'] }}">
                                    </div>
                                </div>
                                {{-- UPLOAD #1 --}}
                                <div class="form-group">
                                    <label class="col-sm-2">UPLOAD #1</label>
                                    <div class="col-sm-6">
                                        <img src="{{ Storage::url($detail['image_path1']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width: 100%;">
                                    </div>
                                </div>
                                {{-- UPLOAD #2 --}}
                                <div class="form-group">
                                    <label class="col-sm-2">UPLOAD #2</label>
                                    <div class="col-sm-6">
                                        <img src="{{ Storage::url($detail['image_path2']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width: 100%;">
                                    </div>
                                </div>
                                {{-- UPLOAD #3 --}}
                                <div class="form-group">
                                    <label class="col-sm-2">UPLOAD #3</label>
                                    <div class="col-sm-6">
                                        <img src="{{ Storage::url($detail['image_path3']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- </div> -->

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
</script>
@endsection