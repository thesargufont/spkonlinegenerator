@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Detail Approval</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Detail Approval</li>
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
                @if($status == 'NOT APPROVE')
                <button type="button" name="back" id="approveBtn" class="btn btn-info"><i class="fa fa-fw fa-check"></i> {{ucwords(__('Setujui'))}}</button>
                <button type="button" name="back" id="notApproveBtn" class="btn btn-danger"><i class="fa fa-solid fa-square-xmark"></i> {{ucwords(__('Tidak Setujui'))}}</button>
                @elseif($status == 'ONGOING')
                <button type="button" name="back" id="cancelBtn" class="btn btn-warning"><i class="fa fa-fw fa-exclamation"></i> {{ucwords(__('Cancel'))}}</button>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form class="form-horizontal" id="wo_form" enctype="multipart/form-data">
                @csrf
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Data Header</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-6">
                            {{-- NOMOR SPK --}}
                            <div class="form-group">
                                <label class="col-md-3">NOMOR SPK</label>
                                <div class="col-md-7">
                                    <input name="spk_number" id='spk_number' type="text" class="form-control" readonly="readonly" value="{{$spk_number}}">
                                </div>
                            </div>

                            {{-- NOMOR WORK ORDER --}}
                            <div class="form-group">
                                <label class="col-md-3">NOMOR WORK ORDER</label>
                                <div class="col-md-7">
                                    <input name="wo_number" id='wo_number' type="text" class="form-control" readonly="readonly" value="{{$wo_number}}">
                                </div>
                            </div>

                            {{-- KATEGORI WORK ORDER --}}
                            <div class="form-group">
                                <label class="col-sm-3">KATEGORI WORK ORDER</label>
                                <div class="col-sm-7">
                                    <input name="wo_category" id='wo_category' type="text" class="form-control" readonly="readonly" value="{{$wo_category}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- DEPARTEMEN --}}
                            <div class="form-group">
                                <label class="col-sm-3">DEPARTEMEN</label>
                                <div class="col-sm-7">
                                    <input name="department" id='department' type="text" class="form-control" readonly="readonly" value="{{$department}}">
                                </div>
                            </div>

                            {{-- KATEGORI PEKERJAAN --}}
                            <div class="form-group">
                                <label class="col-sm-3">KATEGORI PEKERJAAN</label>
                                <div class="col-sm-7">
                                    <input name="job_category" id='job_category' type="text" class="form-control" readonly="readonly" value="{{$job_category}}">
                                </div>
                            </div>

                            {{-- TANGGAL EFEKTIF --}}
                            <div class="form-group">
                                <label class="col-sm-3">TANGGAL EFEKTIF</label>
                                <div class="col-sm-7">
                                    <input name="effective_date" id='effective_date' type="text" class="form-control" readonly="readonly" value="{{$effective_date}}">
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div><input name="action" id='action' type="hidden" class="form-control"></div>
                                <div><input name="count" id='count' type="hidden" class="form-control" value="{{$length}}"></div>
                                <div><input name="header_id" id='coheader_idunt' type="hidden" class="form-control" value="{{$id}}"></div>
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
                                    Data Detail #{{$index}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne-{{ $index }}" class="panel-collapse collapse">
                            <div class="panel-body work-detail" data-index={{$index}}>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-2" style="font-style: italic;">#1 DETIL PELAPORAN</label>
                                    </div>
                                </div>

                                <div class="col-md-12"><br></div>

                                <div class="col-md-6">
                                    {{-- LOKASI --}}
                                    <div class="form-group">
                                        <label class="col-md-2">LOKASI</label>
                                        <div class="col-md-6">
                                            <input name="detail_location_{{ $index }}" id="detail_location_{{ $index }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['location'] }}">
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

                                </div>
                                <div class="col-md-6">
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

                                </div>

                                <div class="col-md-12">
                                    {{-- LAMPIRAN --}}
                                    <div class="form-group">
                                        <label class="col-sm-1">LAMPIRAN #1</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['image_path1']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                        </div>
                                        <label class="col-sm-1">LAMPIRAN #2</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['image_path2']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                        </div>
                                        <label class="col-sm-1">LAMPIRAN #3</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['image_path3']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div><input name="detail[{{ $index }}][id]" type="hidden" class="form-control" value="{{ $detail['id'] }}"></div>
                                    <div>&nbsp;</div>
                                </div>

                                <div class="col-md-12">
                                    <hr style="border-top: 3px solid #bbb">
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-2" style="font-style: italic;">#2 DETIL PENUGASAN</label>
                                    </div>
                                </div>

                                <div class="col-md-12"><br></div>


                                <div class="col-md-6">
                                    {{-- TANGGAL MULAI --}}
                                    <div class="form-group">
                                        <label class="col-sm-2">TANGGAL MULAI</label>
                                        <div class="col-sm-6 input-group">
                                            <input type="text" class="form-control datepicker" placeholder="mm/dd/yyyy" name="detail[{{ $index }}][start_at]" value="{{ $detail['start_at'] }}" @if($status !='NOT APPROVE' ) disabled @endif>
                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                        </div><!-- input-group -->
                                    </div>
                                    {{-- ESTIMASI SELESAI --}}
                                    <div class="form-group">
                                        <label class="col-sm-2">ESTIMASI SELESAI</label>
                                        <div class="col-sm-6 input-group">
                                            <input type="text" class="form-control datepicker" placeholder="mm/dd/yyyy" name="detail[{{ $index }}][estimated_end]" value="{{ $detail['start_at'] }}" @if($status !='NOT APPROVE' ) disabled @endif>
                                            <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                        </div><!-- input-group -->
                                    </div>
                                    {{-- ENGINEER --}}
                                    <div class="form-group">
                                        <label class="col-sm-2">ASSIGN ENGINEER</label>
                                        @if($status == 'NOT APPROVE')
                                        <div class="col-sm-6">
                                            <select class="form-control" name="detail[{{ $index }}][engineer]" @if($status !='NOT APPROVE' ) disabled @endif>
                                                <option value="" disabled hidden selected>Pilih User Enginner</option>
                                                @foreach($engineers as $engineer)
                                                <option value="{{$engineer['id']}}">{{$engineer['name']}} - {{$engineer['nik']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @else
                                        <div class="col-sm-6">
                                            <input name="detail[{{ $index }}][engineer]" id="detail[{{ $index }}][engineer]" type="text" class="form-control" readonly="readonly" value="{{ $detail['engineer'] }}">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- SUPERVISOR --}}
                                    <div class="form-group">
                                        <label class="col-sm-2">ASSIGN SUPERVISOR</label>
                                        @if($status == 'NOT APPROVE')
                                        <div class="col-sm-6">
                                            <select class="form-control" name="detail[{{ $index }}][supervisor]" @if($status !='NOT APPROVE' ) disabled @endif>
                                                <option value="" disabled hidden selected>Pilih User Supervisor</option>
                                                @foreach($spvs as $spv)
                                                <option value="{{$spv['id']}}">{{$spv['name']}} - {{$spv['nik']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @else
                                        <div class="col-sm-6">
                                            <input name="detail[{{ $index }}][supervisor]" id="detail[{{ $index }}][supervisor]" type="text" class="form-control" readonly="readonly" value="{{ $detail['supervisor'] }}">
                                        </div>
                                        @endif
                                    </div>
                                    {{-- AID --}}
                                    <div class="form-group">
                                        <label class="col-sm-2">ASSIGN K3</label>
                                        @if($status == 'NOT APPROVE')
                                        <div class="col-sm-6">
                                            <select class="form-control" name="detail[{{ $index }}][aid]" @if($status !='NOT APPROVE' ) disabled @endif>
                                                <option value="" disabled hidden selected>Pilih User K3</option>
                                                @foreach($aids as $aid)
                                                <option value="{{$aid['id']}}">{{$aid['name']}} - {{$aid['nik']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @else
                                        <div class="col-sm-6">
                                            <input name="detail[{{ $index }}][aid]" id="detail[{{ $index }}][aid]" type="text" class="form-control" readonly="readonly" value="{{ $detail['aid'] }}">
                                        </div>
                                        @endif
                                    </div>
                                    {{-- DESKRIPSI PENUGASAN --}}
                                    <div class="form-group">
                                        <label class="col-sm-2">DESKRIPSI PENUGASAN</label>
                                        <div class="col-sm-6">
                                            <!-- <textarea class="form-control" rows="3" name="detail[{{ $index }}][job_description]" id="detail[{{ $index }}][job_description]" value="{{ $detail['job_description'] }}" @if($status !='NOT APPROVE' ) disabled @endif></textarea> -->
                                            <input name="detail[{{ $index }}][job_description]" id="detail[{{ $index }}][job_description]" type="text" class="form-control" value="{{ $detail['job_description'] }}" @if($status !='NOT APPROVE' ) disabled @endif>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <hr style="border-top: 3px solid #bbb">
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-2" style="font-style: italic;">#3 DETIL PENGERJAAN</label>
                                    </div>
                                </div>

                                <div class="col-md-12"><br></div>


                                <div class="col-md-12">
                                    {{-- STATUS ENGINEER --}}
                                    <div class="form-group">
                                        <label class="col-sm-1">STATUS ENGINEER</label>
                                        <div class="col-sm-3">

                                            <input name="detail[status_engineer]" id="detail[status_engineer]" type="text" class="form-control" value="{{ $detail['engineer_status'] }}" disabled>

                                        </div>
                                        <label class="col-sm-1">DESKRIPSI ENGINEER</label>
                                        <div class="col-sm-3">
                                            <input name="detail[desc_engineer]" id="detail[desc_engineer]" type="text" class="form-control" value="{{ $detail['executor_desc'] }}" disabled>
                                        </div>
                                        <label class="col-sm-1" style="color: red;">NOMOR WP*</label>
                                        <div class="col-sm-3">
                                            <input name="detail[wp_number]" id="detail[wp_number" type="text" class="form-control" value="{{ $detail['wp_number'] }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                {{-- LAMPIRAN FOTO # --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-1">LAMPIRAN #1</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['job_attachment1']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">

                                        </div>
                                        <label class="col-sm-1">LAMPIRAN #2</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['job_attachment2']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">

                                        </div>
                                        <label class="col-sm-1">LAMPIRAN #3</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['job_attachment3']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">

                                        </div>

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
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: 'TRUE',
            autoclose: true,
        });

        $(document).on('click', '#backBtn', function() {
            window.location.href = "{{ route('form-input.approval.index') }}";
        });

        $(document).on('click', '#approveBtn', function() {
            $('#form_result').html('');
            var length_ = $('#count').val();
            document.getElementById('action').value = 'ONGOING';
            console.log()
            var formData = new FormData($('#wo-form')[0]);
            formData.append('header_id', $("input[name=header_id]").val());
            formData.append('spk_number', $("input[name=spk_number]").val());
            formData.append('action', $("input[name=action]").val());
            // Append form data for each detail block
            for (let i = 1; i <= length_; i++) {
                formData.append('detail[' + i + '][id]', $('input[name="detail[' + i + '][id]"]').val());
                formData.append('detail[' + i + '][start_at]', $('input[name="detail[' + i + '][start_at]"]').val());
                formData.append('detail[' + i + '][estimated_end]', $('input[name="detail[' + i + '][estimated_end]"]').val());
                formData.append('detail[' + i + '][engineer]', $('select[name="detail[' + i + '][engineer]"]').val());
                formData.append('detail[' + i + '][supervisor]', $('select[name="detail[' + i + '][supervisor]"]').val());
                formData.append('detail[' + i + '][aid]', $('select[name="detail[' + i + '][aid]"]').val());
                formData.append('detail[' + i + '][job_description]', $('input[name="detail[' + i + '][job_description]"]').val());
            }

            console.log(formData);

            artLoadingDialogDo("Proses menyimpan..", function() {
                // AJAX request
                $.ajax({
                    url: "{{ route('form-input.approval.approve') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        artLoadingDialogClose();
                        if (data.errors) {
                            $('#form_result').html(data.message);
                            setTimeout(function() {
                                $('#form_result').html('');
                            }, 5000);
                        }
                        if (data.success) {
                            $('#form_result').html(data.message);
                            //Optionally, redirect to another page after success
                            setTimeout(function() {
                                window.location.href = "{{ route('form-input.approval.index') }}";
                            }, 1500);
                        }
                    },
                    error: function(xhr, status, error) {
                        artLoadingDialogClose();
                        console.log('Error Status:', status);
                        console.log('Error:', error);
                        console.log('Response:', xhr.responseText);
                        var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                        $('#form_result').html(html);
                    }
                });
            });
            return false; // Prevent default form submission

        });
        $(document).on('click', '#notApproveBtn', function() {
            $('#form_result').html('');
            var length_ = $('#count').val();
            document.getElementById('action').value = 'CLOSED';
            console.log()
            var formData = new FormData($('#wo-form')[0]);
            formData.append('header_id', $("input[name=header_id]").val());
            // formData.append('spk_number', $("input[name=spk_number]").val());
            formData.append('action', $("input[name=action]").val());

            // AJAX request
            artLoadingDialogDo("Proses menyimpan..", function() {
                $.ajax({
                    url: "{{ route('form-input.approval.notapprove') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        artLoadingDialogClose();
                        if (data.errors) {
                            $('#form_result').html(data.message);
                            setTimeout(function() {
                                $('#form_result').html('');
                            }, 5000);
                        }
                        if (data.success) {
                            $('#form_result').html(data.message);
                            //Optionally, redirect to another page after success
                            setTimeout(function() {
                                window.location.href = "{{ route('form-input.approval.index') }}";
                            }, 1500);
                        }
                    },
                    error: function(xhr, status, error) {
                        artLoadingDialogClose();
                        console.log('Error Status:', status);
                        console.log('Error:', error);
                        console.log('Response:', xhr.responseText);
                        var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                        $('#form_result').html(html);
                    }
                });
            });
            return false; // Prevent default form submission

        });

        $(document).on('click', '#cancelBtn', function() {
            $('#form_result').html('');
            var length_ = $('#count').val();
            document.getElementById('action').value = 'CANCEL';
            console.log()
            var formData = new FormData($('#wo-form')[0]);
            formData.append('header_id', $("input[name=header_id]").val());
            formData.append('action', $("input[name=action]").val());
            // Append form data for each detail block
            for (let i = 1; i <= length_; i++) {
                formData.append('detail[' + i + '][id]', $('input[name="detail[' + i + '][id]"]').val());
            }

            console.log(formData);

            // AJAX request
            artLoadingDialogDo("Proses menyimpan..", function() {
                $.ajax({
                    url: "{{ route('form-input.approval.cancel') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        artLoadingDialogClose();
                        if (data.errors) {
                            $('#form_result').html(data.message);
                            setTimeout(function() {
                                $('#form_result').html('');
                            }, 5000);
                        }
                        if (data.success) {
                            $('#form_result').html(data.message);
                            //Optionally, redirect to another page after success
                            setTimeout(function() {
                                window.location.href = "{{ route('form-input.approval.index') }}";
                            }, 1500);
                        }
                    },
                    error: function(xhr, status, error) {
                        artLoadingDialogClose();
                        console.log('Error Status:', status);
                        console.log('Error:', error);
                        console.log('Response:', xhr.responseText);
                        var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                        $('#form_result').html(html);
                    }
                });
            });
            return false; // Prevent default form submission

        });
    });
</script>
@endsection