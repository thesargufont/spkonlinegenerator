@extends('layouts.layout')

@section('auth')
    <h4 class="pull-left page-title">Detail Report</h4>
    <ol class="breadcrumb pull-right">
        <li><a href="#">{{Auth::user()->name}}</a></li>
        <li class="active">Detail Report</li>
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
                            <div class="col-md-6">
                                {{-- NOMOR SPK --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="spk_number">NOMOR SPK</label>
                                    <div class="col-sm-7">
                                        <input name="wo_number" id='spk_number' type="text" class="form-control" readonly="readonly" value="{{$spk_number}}">
                                    </div>
                                </div>

                                {{-- NOMOR WORK ORDER --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="wo_number">NOMOR WORK ORDER</label>
                                    <div class="col-sm-7">
                                        <input name="wo_number" id='wo_number' type="text" class="form-control" readonly="readonly" value="{{$wo_number}}">
                                    </div>
                                </div>

                                {{-- KATEGORI WORK ORDER --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="wo_category">KATEGORI WORK ORDER</label>
                                    <div class="col-sm-7">
                                        <input name="wo_category" id='wo_category' type="text" class="form-control" readonly="readonly" value="{{$wo_category}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- DEPARTEMEN --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="department">DEPARTEMEN</label>
                                    <div class="col-sm-7">
                                        <input name="department" id='department' type="text" class="form-control" readonly="readonly" value="{{$department}}">
                                    </div>
                                </div>

                                {{-- KATEGORI PEKERJAAN --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="job_category">KATEGORI PEKERJAAN</label>
                                    <div class="col-sm-7">
                                        <input name="job_category" id='job_category' type="text" class="form-control" readonly="readonly" value="{{$job_category}}">
                                    </div>
                                </div>

                                {{-- TANGGAL EFEKTIF --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="effective_date">TANGGAL EFEKTIF</label>
                                    <div class="col-sm-7">
                                        <input name="effective_date" id='effective_date' type="text" class="form-control" readonly="readonly" value="{{$effective_date}}">
                                    </div>
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
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <h4 class="col-sm-2" style="font-style: italic;">#1 DETIL PELAPORAN</h4>
                                            </div>
                                        </div>

                                        <div class="col-md-12"><br></div>

                                        <div class="col-md-6">
                                            {{-- LOKASI --}}
                                            <div class="form-group">
                                                <label class="col-md-3" for="detail_location_{{ $index }}">LOKASI</label>
                                                <div class="col-md-7">
                                                    <input name="detail_location_{{ $index }}" id="detail_location_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['location'] }}">
                                                </div>
                                            </div>

                                            {{-- KATEGORI GANGGUAN --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail_disturbance_category_{{ $index }}">KATEGORI GANGGUAN</label>
                                                <div class="col-sm-7">
                                                    <input name="detail_disturbance_category_{{ $index }}" id="detail_disturbance_category_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['disturbance_category'] }}">
                                                </div>
                                            </div>

                                            {{-- DESKRIPSI PELAPORAN --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail_description_{{ $index }}">DESKRIPSI PELAPORAN</label>
                                                <div class="col-sm-7">
                                                    <input name="detail_description_{{ $index }}" id="detail_description_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['description'] }}">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            {{-- ALAT --}}
                                            <div class="form-group">
                                                <label class="col-md-3" for="detail_device_{{ $index }}">ALAT</label>
                                                <div class="col-md-7">
                                                    <input name="detail_device_{{ $index }}" id="detail_device_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device'] }}">
                                                </div>
                                            </div>

                                            {{-- MODEL ALAT --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail_device_model_{{ $index }}">MODEL ALAT</label>
                                                <div class="col-sm-7">
                                                    <input name="detail_device_model_{{ $index }}" id="detail_device_model_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device_model'] }}">
                                                </div>
                                            </div>
                                            {{-- KODE ALAT --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail_device_code_{{ $index }}">KODE ALAT</label>
                                                <div class="col-sm-7">
                                                    <input name="detail_device_code_{{ $index }}" id="detail_device_code_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device_code'] }}">
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-md-12">
                                            {{-- UPLOAD --}}
                                            <div class="form-group">
                                                <label class="col-sm-1" for="detail[image_path1]">UPLOAD #1</label>
                                                <div class="col-sm-3">
                                                    <img src="{{ Storage::url($detail['image_path1']) }}" id="detail[image_path1]" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                                </div>
                                                <label class="col-sm-1" for="detail[image_path2]">UPLOAD #2</label>
                                                <div class="col-sm-3">
                                                    <img src="{{ Storage::url($detail['image_path2']) }}" id="detail[image_path2]" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                                </div>
                                                <label class="col-sm-1" for="detail[image_path3]">UPLOAD #3</label>
                                                <div class="col-sm-3">
                                                    <img src="{{ Storage::url($detail['image_path3']) }}" id="detail[image_path3]" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <hr style="border-top: 3px solid #bbb">
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <h4 class="col-sm-2" style="font-style: italic;">#2 DETIL PENUGASAN</h4>
                                            </div>
                                        </div>

                                        <div class="col-md-12"><br></div>

                                        <div class="col-md-6">
                                            {{-- TANGGAL MULAI --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail[start_at]">TANGGAL MULAI</label>
                                                <div class="col-sm-7">
                                                    <input name="detail[start_at]" id="detail[start_at]" type="text" class="form-control" readonly="readonly" value="{{ $detail['start_effective'] }}">
                                                </div><!-- input-group -->
                                            </div>
                                            {{-- ESTIMASI SELESAI --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail[estimated_end]">ESTIMASI SELESAI</label>
                                                <div class="col-sm-7">
                                                    <input name="detail[estimated_end]" id="detail[estimated_end]" type="text" class="form-control" readonly="readonly" value="{{ $detail['estimated_end'] }}">
                                                </div><!-- input-group -->
                                            </div>
                                            {{-- ENGINEER --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail[engineer]">ASSIGN ENGINEER</label>
                                                <div class="col-sm-7">
                                                    <input name="detail[engineer]" id="detail[engineer]" type="text" class="form-control" readonly="readonly" value="{{ $detail['engineer'] }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            {{-- SUPERVISOR --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail[supervisor]">ASSIGN SUPERVISOR</label>
                                                <div class="col-sm-7">
                                                    <input name="detail[supervisor]" id="detail[supervisor]" type="text" class="form-control" readonly="readonly" value="{{ $detail['supervisor'] }}">
                                                </div>
                                            </div>
                                            {{-- K3 --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail[aid]">ASSIGN K3</label>
                                                <div class="col-sm-7">
                                                    <input name="detail[aid]" id="detail[aid]" type="text" class="form-control" readonly="readonly" value="{{ $detail['aid'] }}">
                                                </div>
                                            </div>
                                            {{-- DESKRIPSI PENUGASAN --}}
                                            <div class="form-group">
                                                <label class="col-sm-3" for="detail[desc_job]">DESKRIPSI PENUGASAN</label>
                                                <div class="col-sm-7">
                                                    <input name="detail[desc_job]" id="detail[desc_job]" type="text" class="form-control" value="{{ $detail['job_description'] }}" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <hr style="border-top: 3px solid #bbb">
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <h4 class="col-sm-2" style="font-style: italic;">#3 DETIL PENGERJAAN</h4>
                                            </div>
                                        </div>

                                        <div class="col-md-12"><br></div>


                                        <div class="col-md-12">
                                            {{-- STATUS ENGINEER --}}
                                            <div class="form-group">
                                                <label class="col-sm-1" for="detail[status_engineer]">STATUS ENGINEER</label>
                                                <div class="col-sm-3">

                                                    <input name="detail[status_engineer]" id="detail[status_engineer]" type="text" class="form-control" value="{{ $detail['engineer_status'] }}" disabled>

                                                </div>
                                                <label class="col-sm-1" for="detail[desc_engineer]">DESKRIPSI ENGINEER</label>
                                                <div class="col-sm-3">
                                                    <input name="detail[desc_engineer]" id="detail[desc_engineer]" type="text" class="form-control" value="{{ $detail['executor_desc'] }}" disabled>
                                                </div>
                                                <label class="col-sm-1" style="color: red;" for="detail[wp_number]">NOMOR WP*</label>
                                                <div class="col-sm-3">
                                                    <input name="detail[wp_number]" id="detail[wp_number]" type="text" class="form-control" value="{{ $detail['wp_number'] }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- LAMPIRAN FOTO # --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-sm-1" for="detail[job_attachment1]">LAMPIRAN #1</label>
                                                <div class="col-sm-3">
                                                    <img src="{{ Storage::url($detail['job_attachment1']) }}" id="detail[job_attachment1]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">

                                                </div>
                                                <label class="col-sm-1" for="detail[job_attachment2]">LAMPIRAN #2</label>
                                                <div class="col-sm-3">
                                                    <img src="{{ Storage::url($detail['job_attachment2']) }}" id="detail[job_attachment2]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">

                                                </div>
                                                <label class="col-sm-1" for="detail[job_attachment3]">LAMPIRAN #3</label>
                                                <div class="col-sm-3">
                                                    <img src="{{ Storage::url($detail['job_attachment3']) }}" id="detail[job_attachment3]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

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
                window.location.href = "{{ route('reports.index') }}";
            });
        });
    </script>
@endsection
