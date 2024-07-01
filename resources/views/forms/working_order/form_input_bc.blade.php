@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Form Working Order</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Form Working Order</li>
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
                <button type="button" name="save" id="saveBtn" class="btn btn-primary"><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan'))}}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form class="form-horizontal" id="wo_form" enctype="multipart/form-data">
                @csrf
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Form Header</h3>
                    </div>
                    <div class="panel-body">
                        {{-- NOMOR WORK ORDER --}}
                        <div class="form-group">
                            <label class="col-md-2">NOMOR WORK ORDER</label>
                            <div class="col-md-6">
                                <input name="wo_number" id='wo_number' type="text" class="form-control" readonly="readonly">
                            </div>
                        </div>

                        {{-- KATEGORI WORK ORDER --}}
                        <div class="form-group">
                            <label class="col-sm-2">KATEGORI WORK ORDER</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="wo_category" id="wo_category" onchange="getjobcategory()">
                                    @foreach($wo_category as $wo_category)
                                    <option value="{{$wo_category->wo_category}}">{{$wo_category->wo_category}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- DEPARTEMEN --}}
                        <div class="form-group">
                            <label class="col-sm-2">DEPARTEMEN</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="department" onchange="getwonumber()">
                                    @foreach($department as $department)
                                    <option value="{{$department->id}}" @if (Auth::user()->department_id == $department->id) selected @endif>{{$department->department_code}} - {{$department->department}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- KATEGORI PEKERJAAN --}}
                        <div class="form-group">
                            <label class="col-sm-2">KATEGORI PEKERJAAN</label>
                            <div class="col-sm-6">
                                <select class="form-control" name="job_category" id="job_category">
                                </select>
                            </div>
                        </div>

                        {{-- TANGGAL EFEKTIF --}}
                        <div class="form-group">
                            <label class="col-sm-2">TANGGAL EFEKTIF</label>
                            <div class="col-sm-6 input-group">
                                <input type="text" class="form-control datepicker" placeholder="mm/dd/yyyy" name="effective_date">
                                <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                            </div><!-- input-group -->
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
        getwonumber();
        getjobcategory();
        var detailIndex = 0;

        $('#addDetailButton').click(function() {
            detailIndex++;
            $('#work-detail-container').append(`
        <div class="col-md-12 work-detail" data-index="${detailIndex}">
            <div class="col-sm-2">
                <button type="button" id="removeDetailButton" class="btn btn-danger btn-sm waves-effect waves-light">HAPUS</button>
            </div>
            <div class="col-sm-10">
                <div class="col-md-2">
                    <div>
                        <div class="col-md-6">
                            <div>
                                <label>LOKASI</label>
                            </div>
                            <div>
                                <select class="form-control" name="details[${detailIndex}][location]">
                                    @foreach($location as $location)
                                        <option value="{{$location->id}}">{{$location->location}} - {{$location->location_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label>ALAT</label>
                            </div>
                            <div>
                                <select class="form-control" name="details[${detailIndex}][device]">
                                    <option>MODEM</option>
                                    <option>ROUTER</option>
                                    <option>MUX</option>
                                    <option>RADIO VHF</option>
                                    <option>REPEATERR</option>
                                    <option>RECEIVER</option>
                                    <option>TRANSMITER</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p>&nbsp;</p>
                    </div>
                    <div>
                        <label>DESKRIPSI</label>
                    </div>
                    <div>
                        <textarea class="form-control" rows="5" name="details[${detailIndex}][description]"></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <label>KATEGORI GANGGUAN</label>
                    </div>
                    <div>
                        <select class="form-control" name="details[${detailIndex}][disturbance_category]">
                            <option>TP OFF</option>
                            <option>TP LINK DOWN</option>
                            <option>TP ERROR</option>
                        </select>
                    </div>
                    <div>
                        <p>&nbsp;</p>
                    </div>
                    <div>
                        <label>LAMPIRAN FOTO</label>
                    </div>
                    <div>
                        <input type="file" name="details[${detailIndex}][photo1]">
                        <br>
                        <input type="file" name="details[${detailIndex}][photo2]">
                        <br>
                        <input type="file" name="details[${detailIndex}][photo3]">
                    </div>
                </div>
            </div>
            <div>
                <p>&nbsp;</p>
            </div>
        </div>
        `);
        });

        $(document).on('click', '#removeDetailButton', function() {
            $(this).closest('.work-detail').remove();
        });

        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: 'TRUE',
            autoclose: true,
        });

        $(document).on('click', '#saveBtn', function() {
            $('#form_result').html('');

            // Defining URLs
            var urls = {
                create: "{{ route('form-input.working-order.create-new') }}", // Correctly use curly braces for Blade syntax
                index: "{{ route('form-input.working-order.index') }}" // Correct the route format to dot notation for consistency
            };

            var formData = new FormData($('#wo-form')[0]);
            formData.append('wo_number', $("input[name=wo_number]").val());
            formData.append('wo_category', $("select[name=wo_category]").val());
            formData.append('department', $("select[name=department]").val());
            formData.append('job_category', $("select[name=job_category]").val());
            formData.append('effective_date', $("input[name=effective_date]").val());

            // Append form data for each detail block
            $('.work-detail').each(function() {
                var detailIndex = $(this).data('index');
                formData.append('details[' + detailIndex + '][location]', $(this).find('select[name="details[' + detailIndex + '][location]"]').val());
                formData.append('details[' + detailIndex + '][device]', $(this).find('select[name="details[' + detailIndex + '][device]"]').val());
                formData.append('details[' + detailIndex + '][description]', $(this).find('textarea[name="details[' + detailIndex + '][description]"]').val());
                formData.append('details[' + detailIndex + '][disturbance_category]', $(this).find('select[name="details[' + detailIndex + '][disturbance_category]"]').val());

                // Append file inputs
                var files = $(this).find('input[type="file"]');
                $.each(files, function(index, fileInput) {
                    if (fileInput.files.length > 0) {
                        formData.append('details[' + detailIndex + '][photo' + (index + 1) + ']', fileInput.files[0]);
                    }
                });
            });

            console.log(detailIndex);
            console.log(formData);

            // AJAX request
            $.ajax({
                url: "{{ route('form-input.working-order.create-new') }}",
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
                        // Optionally, redirect to another page after success
                        // setTimeout(function() {
                        //     window.location.href = "{{ route('form-input.working-order.index') }}";
                        // }, 1500);
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

    function getwonumber() {
        var department_id = $("select[name=department]").val();

        $.ajax({
            url: "{{ route('form-input.working-order.getwonumber') }}",
            type: 'get',
            dataType: "json",
            data: {
                'department_id': department_id
            },
            success: function(data) {
                console.log('s');
                if (data.success == true) {
                    console.log(data.wo_number);
                    //document.getElementsByName('wo_number').value = data.wo_number;
                    document.getElementById('wo_number').value = data.wo_number;
                } else {
                    console.log(data);
                    var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error Status:', status);
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
                var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                $('#form_result').html(html);
            }
        });
    }

    function getjobcategory() {
        var wo_category = $("select[name=wo_category]").val();

        $.ajax({
            url: "{{ route('form-input.working-order.getjobcategory') }}",
            type: 'get',
            dataType: "json",
            data: {
                'wo_category': wo_category
            },
            success: function(data) {
                if (data.success == true) {
                    $('#job_category').empty();
                    console.log(data.job_categories.length);
                    $.each(data.job_categories, function(key, value) {
                        $('#job_category').append('<option value="' + value.id + '">' + value.job_category + '</option>');
                    });
                    if (data.job_categories.length == 1) {
                        $('#job_category').prop('disabled', true);
                    } else {
                        $('#job_category').prop('enabled', true);
                    }
                } else {
                    console.log(data);
                    var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Error Status:', status);
                console.log('Error:', error);
                console.log('Response:', xhr.responseText);
                var html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                $('#form_result').html(html);
            }
        });
    }
</script>
@endsection