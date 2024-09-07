@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">DETAIL {{$wo_category}}</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">DETAIL {{$wo_category}}</li>
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
                @if($status != 'DONE')
                <button type="button" name="back" id="approveBtn" class="btn btn-info" onclick="showModal()"><i class="fa fa-fw fa-check"></i> {{ucwords(__('Submit'))}}</button>
                @endif
                <!-- <button type="button" name="back" id="pdfBtn" class="btn btn-info">{{ucwords(__('Download PDF'))}}</button> -->
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form class="form-horizontal" id="wo_form" enctype="multipart/form-data">
                @csrf
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">DATA HEADER</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-6">
                            {{-- NOMOR SPK --}}
                            <div class="form-group">
                                <label class="col-md-3" for="spk_number">NOMOR SPK</label>
                                <div class="col-md-7">
                                    <input name="spk_number" id='spk_number' type="text" class="form-control" readonly="readonly" value="{{$spk_number}}">
                                </div>
                            </div>

                            {{-- NOMOR WORK ORDER --}}
                            <div class="form-group">
                                <label class="col-md-3" for="wo_number">NOMOR WORK ORDER</label>
                                <div class="col-md-7">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <div><input name="action" id='action' type="hidden" class="form-control"></div>
                                <div><input name="count" id='count' type="hidden" class="form-control" value="{{$length}}"></div>
                                <div><input name="header_id" id='header_id' type="hidden" class="form-control" value="{{$id}}"></div>
                            </div>
                        </div>
                    </div> <!-- panel-body -->
                </div> <!-- panel -->

                <!-- <div class="col-lg-6"> -->
                <div class="panel-group" id="accordion-test-2">
                    @foreach($details as $detail)
                    <div class="panel panel-info panel-color">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion-test-2" href="#collapseOne-{{ $detail['index'] }}" aria-expanded="false" class="collapsed">
                                    DATA DETAIL #{{$detail['index']}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne-{{ $detail['index'] }}" class="panel-collapse collapse">
                            <div class="panel-body work-detail" data-index={{$detail['index']}}>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h4 class="col-sm-2" style="font-style: italic;">#1 DETIL PELAPORAN</h4>
                                    </div>
                                </div>

                                <div class="col-md-12"><br></div>

                                <div class="col-md-6">
                                    {{-- LOKASI --}}
                                    <div class="form-group">
                                        <label class="col-md-2" for="detail_location_{{ $detail['index'] }}">LOKASI</label>
                                        <div class="col-md-6">
                                            <input name="detail_location_{{ $detail['index'] }}" id="detail_location_{{ $detail['index'] }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['location'] }}">
                                        </div>
                                    </div>

                                    {{-- ALAT --}}
                                    <div class="form-group">
                                        <label class="col-md-2" for="detail_device_{{ $detail['index'] }}">ALAT</label>
                                        <div class="col-md-6">
                                            <input name="detail_device_{{ $detail['index'] }}" id="detail_device_{{ $detail['index'] }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device'] }}">
                                        </div>
                                    </div>

                                    {{-- MODEL ALAT --}}
                                    <div class="form-group">
                                        <label class="col-sm-2" for="detail_device_model_{{ $detail['index'] }}">MODEL ALAT</label>
                                        <div class="col-sm-6">
                                            <input name="detail_device_model_{{ $detail['index'] }}" id="detail_device_model_{{ $detail['index'] }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device_model'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- KODE ALAT --}}
                                    <div class="form-group">
                                        <label class="col-sm-2" for="detail_device_code_{{ $detail['index'] }}">KODE ALAT</label>
                                        <div class="col-sm-6">
                                            <input name="detail_device_code_{{ $detail['index'] }}" id="detail_device_code_{{ $detail['index'] }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['device_code'] }}">
                                        </div>
                                    </div>

                                    {{-- KATEGORI GANGGUAN --}}
                                    <div class="form-group">
                                        <label class="col-sm-2" for="detail_disturbance_category_{{ $detail['index'] }}">KATEGORI GANGGUAN</label>
                                        <div class="col-sm-6">
                                            <input name="detail_disturbance_category_{{ $detail['index'] }}" id="detail_disturbance_category_{{ $detail['index'] }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['disturbance_category'] }}">
                                        </div>
                                    </div>

                                    {{-- DESKRIPSI PELAPORAN --}}
                                    <div class="form-group">
                                        <label class="col-sm-2" for="detail_description_{{ $detail['index'] }}">DESKRIPSI PELAPORAN</label>
                                        <div class="col-sm-6">
                                            <input name="detail_description_{{ $detail['index'] }}" id="detail_description_{{ $detail['index'] }}" type="text" class="form-control" readonly="readonly" value="{{ $detail['description'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    {{-- LAMPIRAN --}}
                                    <div class="form-group">
                                        <label class="col-sm-1" for="">LAMPIRAN #1</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['image_path1']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                        </div>
                                        <label class="col-sm-1" for="">LAMPIRAN #2</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['image_path2']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
                                        </div>
                                        <label class="col-sm-1" for="">LAMPIRAN #3</label>
                                        <div class="col-sm-3">
                                            <img src="{{ Storage::url($detail['image_path3']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width:70%;">
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
                                        <label class="col-sm-3" for="detail[{{ $detail['index'] }}][start_at]">TANGGAL MULAI</label>
                                        <div class="col-sm-7">
                                            <input name="detail[{{ $detail['index'] }}][start_at]" id="detail[{{ $detail['index'] }}][start_at]" type="text" class="form-control" readonly="readonly" value="{{ $detail['start_effective'] }}">
                                        </div><!-- input-group -->
                                    </div>
                                    {{-- ESTIMASI SELESAI --}}
                                    <div class="form-group">
                                        <label class="col-sm-3" for="detail[{{ $detail['index'] }}][estimated_end]">ESTIMASI SELESAI</label>
                                        <div class="col-sm-7">
                                            <input name="detail[{{ $detail['index'] }}][estimated_end]" id="detail[{{ $detail['index'] }}][estimated_end]" type="text" class="form-control" readonly="readonly" value="{{ $detail['estimated_end'] }}">
                                        </div><!-- input-group -->
                                    </div>
                                    {{-- ENGINEER --}}
                                    <div class="form-group">
                                        <label class="col-sm-3" for="detail[{{ $detail['index'] }}][engineer]">ASSIGN ENGINEER</label>
                                        <div class="col-sm-7">
                                            <input name="detail[{{ $detail['index'] }}][engineer]" id="detail[{{ $detail['index'] }}][engineer]" type="text" class="form-control" readonly="readonly" value="{{ $detail['engineer'] }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- SUPERVISOR --}}
                                    <div class="form-group">
                                        <label class="col-sm-3" for="detail[{{ $detail['index'] }}][supervisor]">ASSIGN SUPERVISOR</label>
                                        <div class="col-sm-7">
                                            <input name="detail[{{ $detail['index'] }}][supervisor]" id="detail[{{ $detail['index'] }}][supervisor]" type="text" class="form-control" readonly="readonly" value="{{ $detail['supervisor'] }}">
                                        </div>
                                    </div>
                                    {{-- K3 --}}
                                    <div class="form-group">
                                        <label class="col-sm-3" for="detail[{{ $detail['index'] }}][aid]">ASSIGN K3</label>
                                        <div class="col-sm-7">
                                            <input name="detail[{{ $detail['index'] }}][aid]" id="detail[{{ $detail['index'] }}][aid]" type="text" class="form-control" readonly="readonly" value="{{ $detail['aid'] }}">
                                        </div>
                                    </div>
                                    {{-- DESKRIPSI PENUGASAN --}}
                                    <div class="form-group">
                                        <label class="col-sm-3" for="detail[{{ $detail['index'] }}][desc_job]">DESKRIPSI PENUGASAN</label>
                                        <div class="col-sm-7">
                                            <input name="detail[{{ $detail['index'] }}][desc_job]" id="detail[{{ $detail['index'] }}][desc_job]" type="text" class="form-control" value="{{ $detail['description'] }}" disabled>
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

                                <div class="form-group">
                                    <div><input name="detail[{{ $detail['index'] }}][id]" type="hidden" class="form-control" value="{{ $detail['id'] }}"></div>
                                    <div>&nbsp;</div>
                                </div>

                                <div class="col-md-12">
                                    {{-- STATUS ENGINEER --}}
                                    <div class="form-group">
                                        <label class="col-sm-1" for="detail[{{ $detail['index'] }}][status_engineer]">STATUS ENGINEER</label>
                                        <div class="col-sm-3">
                                            @if($status != 'DONE')
                                            <select class="form-control" name="detail[{{ $detail['index'] }}][status_engineer]">
                                                @foreach($status_detail as $status_det)
                                                <option value="{{$status_det}}" @if ($status_det== $detail['engineer_status'] ) selected @endif>{{$status_det}}</option>
                                                @endforeach
                                            </select>
                                            @else
                                            <input name="detail[{{ $detail['index'] }}][status_engineer]" id="detail[{{ $detail['index'] }}][status_engineer]" type="text" class="form-control" value="{{ $detail['engineer_status'] }}" disabled>
                                            @endif
                                        </div>
                                        <label class="col-sm-1" >DESKRIPSI ENGINEER</label>
                                        <div class="col-sm-3">
                                            <input name="detail[{{ $detail['index'] }}][desc_engineer]" id="detail[{{ $detail['index'] }}][desc_engineer]" type="text" class="form-control" value="{{ $detail['executor_desc'] }}" @if($status=='DONE' ) disabled @endif>
                                        </div>
                                        <label class="col-sm-1" style="color: red;" for="detail[{{ $detail['index'] }}][wp_number]">NOMOR WP*</label>
                                        <div class="col-sm-3">
                                            <input name="detail[{{ $detail['index'] }}][wp_number]" id="detail[{{ $detail['index'] }}][wp_number]" type="text" class="form-control" value="{{ $detail['wp_number'] }}" @if($status=='DONE' ) disabled @endif>
                                        </div>
                                    </div>
                                    <!-- {{-- DESKRIPSI ENGINEER --}}
                                    <div class="form-group">
                                    </div>
                                    {{-- NOMOR WP --}}
                                    <div class="form-group">
                                    </div> -->
                                </div>
                                {{-- LAMPIRAN FOTO # --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-1" for="detail[{{ $detail['index'] }}][photo1]">LAMPIRAN #1</label>
                                        <div class="col-sm-3">
                                            @if($status != 'DONE')
                                            <input type="file" name="detail[{{ $detail['index'] }}][photo1]" id="detail[{{ $detail['index'] }}][photo1]">
                                            <br>
                                            <p style="font-style: italic;">preview gambar yg tersimpan :</p>
                                            <img src="{{ Storage::url($detail['job_attachment1']) }}" id="detail[{{ $detail['index'] }}][photo1]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">
                                            @else
                                            <img src="{{ Storage::url($detail['job_attachment1']) }}" id="detail[{{ $detail['index'] }}][photo1]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">
                                            @endif
                                            <!-- <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $detail['index'] }}][photo1]" onclick="clearFileInput($detail['index'], 1)" style="display: none;">Clear</button> -->
                                            <!-- <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $detail['index'] }}][photo1]" onclick="clearFileInput($detail['index'], 1)" style="display: none;">Clear</button> -->
                                        </div>
                                        <label class="col-sm-1" for="detail[{{ $detail['index'] }}][photo2]">LAMPIRAN #2</label>
                                        <div class="col-sm-3">
                                            @if($status != 'DONE')
                                            <input type="file" name="detail[{{ $detail['index'] }}][photo2]" id="detail[{{ $detail['index'] }}][photo2]">
                                            <br>
                                            <p style="font-style: italic;">preview gambar yg tersimpan :</p>
                                            <img src="{{ Storage::url($detail['job_attachment2']) }}" id="detail[{{ $detail['index'] }}][photo2]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">
                                            @else
                                            <img src="{{ Storage::url($detail['job_attachment2']) }}" id="detail[{{ $detail['index'] }}][photo2]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">
                                            @endif
                                            <!-- <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $detail['index'] }}][photo2]" onclick="clearFileInput($detail['index'], 1)" style="display: none;">Clear</button> -->
                                            <!-- <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $detail['index'] }}][photo2]" onclick="clearFileInput($detail['index'], 1)" style="display: none;">Clear</button> -->
                                        </div>
                                        <label class="col-sm-1" for="detail[{{ $detail['index'] }}][photo3]">LAMPIRAN #3</label>
                                        <div class="col-sm-3">
                                            @if($status != 'DONE')
                                            <input type="file" name="detail[{{ $detail['index'] }}][photo3]" id="detail[{{ $detail['index'] }}][photo3]">
                                            <br>
                                            <p style="font-style: italic;">preview gambar yg tersimpan :</p>
                                            <img src="{{ Storage::url($detail['job_attachment3']) }}" id="detail[{{ $detail['index'] }}][photo3]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">
                                            @else
                                            <img src="{{ Storage::url($detail['job_attachment3']) }}" id="detail[{{ $detail['index'] }}][photo3]" alt="..tidak ditemukan." class="img-responsive" style="max-width: 70%;">
                                            @endif
                                            <!-- <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $detail['index'] }}][photo3]" onclick="clearFileInput($detail['index'], 1)" style="display: none;">Clear</button> -->
                                            <!-- <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $detail['index'] }}][photo3]" onclick="clearFileInput($detail['index'], 1)" style="display: none;">Clear</button> -->
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

<body>
    <div id="modal" class="modal-container"> 
        <div class="modal-content"> 
  
            <h2>Konfirmasi</h2> 
            <p class="confirmation-message"> 
                Anda yakin akan menyimpan? 
            </p> 
  
            <div class="button-container"> 
                <button id="cancelBtn" class="btn btn-secondary"> Batal </button> 
                <button id="actionBtn" class="btn btn-primary"> Ya </button> 
            </div> 
        </div> 
    </div> 
</body>

<style>
    .signature-canvas {
        border: 2px solid #000;
        margin-bottom: 10px;
    }
</style>

{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}

<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: 'TRUE',
            autoclose: true,
        });

        $(document).on('click', '#backBtn', function() {
            window.location.href = "{{ route('form-input.engineer.index') }}";
        });

        $(document).on('click', '#pdfBtn', function() {
            var url = "{{route('form-input.engineer.download', '')}}" + "/" + $("input[name=header_id]").val();
            window.open(url, '_blank');
        });

    });

    function doSubmit() {
        hideModal();
        $('#form_result').html('');
        var length_ = $('#count').val();
        console.log()
        var formData = new FormData($('#wo-form')[0]);
        formData.append('header_id', $("input[name=header_id]").val());
        // Append form data for each detail block
        for (let i = 1; i <= length_; i++) {
            formData.append('detail[' + i + '][id]', $('input[name="detail[' + i + '][id]"]').val());
            formData.append('detail[' + i + '][desc_engineer]', $('input[name="detail[' + i + '][desc_engineer]"]').val());
            formData.append('detail[' + i + '][wp_number]', $('input[name="detail[' + i + '][wp_number]"]').val());
            formData.append('detail[' + i + '][start_at]', $('input[name="detail[' + i + '][start_at]"]').val());
            formData.append('detail[' + i + '][estimated_end]', $('input[name="detail[' + i + '][estimated_end]"]').val());
            formData.append('detail[' + i + '][estimated_end]', $('input[name="detail[' + i + '][estimated_end]"]').val());
            formData.append('detail[' + i + '][status_engineer]', $('select[name="detail[' + i + '][status_engineer]"]').val());

            let fileInput = $('input[name="detail[' + i + '][photo1]"]')[0];
            if (fileInput.files[0]) {
                formData.append('detail[' + i + '][photo1]', fileInput.files[0]);
            }
            let fileInput2 = $('input[name="detail[' + i + '][photo2]"]')[0];
            if (fileInput2.files[0]) {
                formData.append('detail[' + i + '][photo2]', fileInput2.files[0]);
            }
            let fileInput3 = $('input[name="detail[' + i + '][photo3]"]')[0];
            if (fileInput3.files[0]) {
                formData.append('detail[' + i + '][photo3]', fileInput3.files[0]);
            }

        }

        console.log(formData);

        // AJAX request
        artLoadingDialogDo("Proses menyimpan..", function() {
            $.ajax({
                url: "{{ route('form-input.engineer.submit') }}",
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
                            window.location.href = "{{ route('form-input.engineer.index') }}";
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
            return false;
        });
        return false; // Prevent default form submission

    }

    function toggleClearButton(detailIndex, photoIndex) {
        var fileInput = document.getElementById('photo' + photoIndex + '_' + detailIndex);
        var clearButton = document.getElementById('clear_photo' + photoIndex + '_' + detailIndex);
        if (fileInput.value) {
            clearButton.style.display = 'inline-block';
        } else {
            clearButton.style.display = 'none';
        }
    }

    function clearFileInput(detailIndex, photoIndex) {
        var fileInput = document.getElementById('detail[' + $detail['index'] + '][photo1]');
        var clearButton = document.getElementById('clear_detail[' + $detail['index'] + '][photo1]');
        fileInput.value = '';
        clearButton.style.display = 'none';
    }

    function showModal() { 
        modal.style.display = 'flex'; 
    } 

    // Hide modal function 
    function hideModal() { 
        modal.style.display = 'none'; 
    } 

    cancelBtn.addEventListener('click', hideModal); 
    actionBtn.addEventListener('click', doSubmit); 
</script>
@endsection