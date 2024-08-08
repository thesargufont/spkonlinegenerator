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
                <button type="button" name="back" id="approveBtn" class="btn btn-info"><i class="fa fa-fw fa-check"></i> {{ucwords(__('Submit'))}}</button>
                @endif
                <button type="button" name="back" id="pdfBtn" class="btn btn-info">{{ucwords(__('Download PDF'))}}</button>
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
                        {{-- NOMOR SPK --}}
                        <div class="form-group">
                            <label class="col-md-2">NOMOR SPK</label>
                            <div class="col-md-6">
                                <input name="spk_number" id='spk_number' type="text" class="form-control" readonly="readonly" value="{{$spk_number}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div><input name="action" id='action' type="hidden" class="form-control"></div>
                            <div><input name="count" id='count' type="hidden" class="form-control" value="{{$length}}"></div>
                            <div><input name="header_id" id='header_id' type="hidden" class="form-control" value="{{$id}}"></div>
                            <div>&nbsp;</div>
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
                                    DATA DETAIL #{{$index}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne-{{ $index }}" class="panel-collapse collapse">
                            <div class="panel-body work-detail" data-index={{$index}}>
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

                                <div class="form-group">
                                    <div><input name="detail[{{ $index }}][id]" type="hidden" class="form-control" value="{{ $detail['id'] }}"></div>
                                    <div>&nbsp;</div>
                                </div>

                                {{-- TANGGAL MULAI --}}
                                <div class="form-group">
                                    <label class="col-sm-2">TANGGAL MULAI</label>
                                    <div class="col-sm-6">
                                        <input name="detail[{{ $index }}][start_at]" id="detail[{{ $index }}][start_at]" type="text" class="form-control" readonly="readonly" value="{{ $detail['start_effective'] }}">
                                    </div><!-- input-group -->
                                </div>
                                {{-- ESTIMASI SELESAI --}}
                                <div class="form-group">
                                    <label class="col-sm-2">ESTIMASI SELESAI</label>
                                    <div class="col-sm-6">
                                        <input name="detail[{{ $index }}][estimated_end]" id="detail[{{ $index }}][estimated_end]" type="text" class="form-control" readonly="readonly" value="{{ $detail['estimated_end'] }}">
                                    </div><!-- input-group -->
                                </div>
                                {{-- ENGINEER --}}
                                <div class="form-group">
                                    <label class="col-sm-2">ASSIGN ENGINEER</label>
                                    <div class="col-sm-6">
                                        <input name="detail[{{ $index }}][engineer]" id="detail[{{ $index }}][engineer]" type="text" class="form-control" readonly="readonly" value="{{ $detail['engineer'] }}">
                                    </div>
                                </div>
                                {{-- SUPERVISOR --}}
                                <div class="form-group">
                                    <label class="col-sm-2">ASSIGN SUPERVISOR</label>
                                    <div class="col-sm-6">
                                        <input name="detail[{{ $index }}][supervisor]" id="detail[{{ $index }}][supervisor]" type="text" class="form-control" readonly="readonly" value="{{ $detail['supervisor'] }}">
                                    </div>
                                </div>
                                {{-- STATUS ENGINEER --}}
                                <div class="form-group">
                                    <label class="col-sm-2">STATUS ENGINEER</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" name="detail[{{ $index }}][status_engineer]">
                                            @foreach($status_detail as $status_detail)
                                            <option value="{{$status_detail}}" @if ($status_detail==$detail['engineer_status'] ) selected @endif>{{$status_detail}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- DESKRIPSI ENGINEER --}}
                                <div class="form-group">
                                    <label class="col-sm-2">DESKRIPSI ENGINEER</label>
                                    <div class="col-sm-6">
                                        <input name="detail[{{ $index }}][desc_engineer]" id="detail[{{ $index }}][desc_engineer]" type="text" class="form-control" value="{{ $detail['description'] }}">
                                    </div>
                                </div>
                                {{-- NOMOR WP --}}
                                <div class="form-group">
                                    <label class="col-sm-2">NOMOR WP</label>
                                    <div class="col-sm-6">
                                        <input name="detail[{{ $index }}][wp_number]" id="detail[{{ $index }}][wp_number]" type="text" class="form-control" value="">
                                    </div>
                                </div>
                                {{-- LAMPIRAN FOTO --}}
                                <div class="form-group">
                                    <label class="col-sm-2">LAMPIRAN FOTO</label>
                                    <div class="col-sm-6">
                                        @if($detail['job_attachment1'] == null || $detail['job_attachment1'] == '')
                                        <input type="file" name="detail[{{ $index }}][photo1]" id="detail[{{ $index }}][photo1]">
                                        <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $index }}][photo1]" onclick="clearFileInput($index, 1)" style="display: none;">Clear</button>
                                        @else
                                        <img src="{{ Storage::url($detail['image_path1']) }}" alt="..tidak ditemukan." class="img-responsive" style="max-width: 100%;">
                                        <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_detail[{{ $index }}][photo1]" onclick="clearFileInput($index, 1)" style="display: none;">Clear</button>
                                        @endif
                                    </div>
                                    <div class="col-sm-1">
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
            window.location.href = "{{ route('form-input.engineer.index') }}";
        });

        $(document).on('click', '#pdfBtn', function() {
            var url = "{{route('form-input.engineer.downloadpdf', '')}}" + "/" + $("input[name=header_id]").val();
            window.open(url, '_blank');
        });

        $(document).on('click', '#approveBtn', function() {
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

            }

            console.log(formData);

            // AJAX request
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
                    if (data.errors) {
                        $('#form_result').html(data.message);
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
                    console.log('Error Status:', status);
                    console.log('Error:', error);
                    console.log('Response:', xhr.responseText);
                    var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                    $('#form_result').html(html);
                }
            });
            return false; // Prevent default form submission

        });
    });

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
        var fileInput = document.getElementById('detail[' + $index + '][photo1]');
        var clearButton = document.getElementById('clear_detail[' + $index + '][photo1]');
        fileInput.value = '';
        clearButton.style.display = 'none';
    }
</script>
@endsection