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
                    <button type="button" name="save" id="saveBtn" class="btn btn-primary" onclick="showModal()"><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan'))}}</button>
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
                            <div class="col-md-6">
                                {{-- NOMOR WORK ORDER --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="wo_number">NOMOR WORK ORDER</label>
                                    <div class="col-sm-7">
                                        <input name="wo_number" id='wo_number' type="text" class="form-control" readonly="readonly">
                                    </div>
                                    <div class="col-sm-1">
                                        <button type="button" name="save" id="saveBtn" class="btn-sm btn-primary" onclick="getwonumber()"><i class="fa fa-fw fa-refresh"></i></button>
                                    </div>
                                </div>

                                {{-- KATEGORI WORK ORDER --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="wo_category">KATEGORI WORK ORDER</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="wo_category" id="wo_category" onchange="getjobcategory()">
                                            <option value="" selected disabled hidden>PILIH SATU</option>
                                            @foreach($wo_category as $wo_category)
                                                <option value="{{$wo_category['wo_category']}}">{{$wo_category['wo_category']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- DEPARTEMEN --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="department">DEPARTEMEN</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="department" onchange="getwonumber()">
                                            @foreach($department as $department)
                                                <option value="{{$department['id']}}" @if (Auth::user()->department_id == $department['id']) selected @endif>{{$department['department_code']}} - {{$department['department']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- KATEGORI PEKERJAAN --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="job_category" onchange="checkwocategory()">KATEGORI PEKERJAAN</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" name="job_category" id="job_category">
                                        </select>
                                    </div>
                                </div>

                                {{-- TANGGAL EFEKTIF --}}
                                <div class="form-group">
                                    <label class="col-sm-3" for="effective_date">TANGGAL EFEKTIF</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control doEffectiveDate" placeholder="mm/dd/yyyy" id="effective_date" name="effective_date" value="{{$effective_date}}">
{{--                                        <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>--}}
                                    </div><!-- input-group -->
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

        .modal-content {
            background-color: #fff; /* Make sure this is the desired background */
            padding: 20px;
            border-radius: 5px;
        }
    </style>

    <!-- Plugins js -->

    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}

    <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript') }}"></script>
    <script src="{{ asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js" type="text/javascript') }}"></script>
    <script src="{{ asset('pages/form-advanced.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.doEffectiveDate').datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: 'TRUE',
                autoclose: true,
            });

            getwonumber();
            getjobcategory();
            var detailIndex = 0;

            $('#addDetailButton').click(function() {
                detailIndex++;
                $('#work-detail-container').append(`
            <div class="col-md-12 work-detail" data-index="${detailIndex}">
            <div class="col-sm-1">
                <button type="button" id="removeDetailButton" class="btn btn-danger btn-sm waves-effect waves-light">HAPUS</button>
            </div>
            <div class="col-sm-11">
                <div class="col-md-3">
                    <div>
                        <div class="col-md-12">
                            <div>
                                <label for="">LOKASI</label>
                            </div>
                            <div>
                                <select class="form-control wo-select" name="details[${detailIndex}][location]" id="details${detailIndex}location" onchange="getdevicemodel(${detailIndex})">
                                    <option value="" selected disabled hidden>PILIH SATU</option>
                                    @foreach($location as $location)
                <option value="{{$location['id']}}">{{$location['location']}} - {{$location['location_type']}}</option>
                                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div>
        <p>&nbsp;</p>
    </div>
    <div>
        <div class="col-md-12">
            <div>
                <label for="details${detailIndex}disturbance_category">KATEGORI GANGGUAN</label>
                            </div>
                            <div>
                                <select class="form-control disturbance_category" name="details[${detailIndex}][disturbance_category]" id="details${detailIndex}disturbance_category">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <div class="col-md-5">
                            <div>
                                <label for="details${detailIndex}device">ALAT</label>
                            </div>
                            <div>
                                <select class="form-control" name="details[${detailIndex}][device]" id="details${detailIndex}device" onchange="getdevicemodel(${detailIndex})">
                                    <option value="" selected disabled hidden>PILIH SATU</option>
                                    @foreach($device as $device)
                <option value="{{$device['device_name']}}">{{$device['device_name']}}</option>
                                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-7">
            <div>
                <label for="details${detailIndex}device_model">MODEL ALAT</label>
                            </div>
                            <div>
                                <select class="form-control" name="details[${detailIndex}][device_model]" id="details${detailIndex}device_model" onchange="getdevicecode(${detailIndex})" disabled>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p>&nbsp;</p>
                    </div>
                    <div>
                        <label for="details[${detailIndex}][description]">DESKRIPSI</label>
                    </div>
                    <div>
                        <textarea class="form-control" rows="4" name="details[${detailIndex}][description]" id="details[${detailIndex}][description]"></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div>
                        <label for="details${detailIndex}device_code">KODE ALAT</label>
                    </div>
                    <div>
                        <input name="details[${detailIndex}][device_code]" id='details${detailIndex}device_code' type="text" class="form-control" readonly="readonly">
                    </div>
                    <div>
                        <p>&nbsp;</p>
                    </div>
                    <div>
                        <label for="photo1_${detailIndex}">LAMPIRAN FOTO</label>
                    </div>
                    <div>
                        <div class="col-sm-10">
                            <input type="file" name="details[${detailIndex}][photo1]" id="photo1_${detailIndex}" onchange="toggleClearButton(${detailIndex}, 1)">
                            <br>
                            <input type="file" name="details[${detailIndex}][photo2]" id="photo2_${detailIndex}" onchange="toggleClearButton(${detailIndex}, 2)">
                            <br>
                            <input type="file" name="details[${detailIndex}][photo3]" id="photo3_${detailIndex}" onchange="toggleClearButton(${detailIndex}, 3)">
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_photo1_${detailIndex}" onclick="clearFileInput(${detailIndex}, 1)" style="display: none;">Clear</button>
                            <br>
                            <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_photo2_${detailIndex}" onclick="clearFileInput(${detailIndex}, 2)" style="display: none;">Clear</button>
                            <br>
                            <button class="btn btn-danger btn-sm waves-effect waves-light" type="button" id="clear_photo3_${detailIndex}" onclick="clearFileInput(${detailIndex}, 3)" style="display: none;">Clear</button>
                        </div>

                    </div>
                </div>
            </div>
            <div>
                <p>&nbsp;</p>
            </div>
            </div>
        `);
                checkwocategory_ng();
                getdevicemodel(detailIndex);
                getdevicecode(detailIndex);
            });

            $(document).on('click', '#removeDetailButton', function() {
                $(this).closest('.work-detail').remove();
            });

            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: 'TRUE',
                autoclose: true,
            });

            $('.wo-select').select2();

            $(document).on('click', '#backBtn', function() {
                window.location.href = "{{ route('form-input.working-order.index') }}";
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
            var fileInput = document.getElementById('photo' + photoIndex + '_' + detailIndex);
            var clearButton = document.getElementById('clear_photo' + photoIndex + '_' + detailIndex);
            fileInput.value = '';
            clearButton.style.display = 'none';
        }

        function checkwocategory() {
            if ($('#wo_category').val() == 'PEKERJAAN') {
                $('.disturbance_category').prop('disabled', true);
                $('.disturbance_category').empty();
            } else {
                $('.disturbance_category').prop('disabled', false);
                $('.disturbance_category').each(function() {
                    getDisturbanceCategory(this.id.substring(7, 8));
                });
            }
        }

        function checkwocategory_ng() {
            if ($('#wo_category').val() == 'PEKERJAAN') {
                $('.disturbance_category').prop('disabled', true);
            } else {
                $('.disturbance_category').prop('disabled', false);
            }
        }

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
            var department = $("select[name=department]").val();

            $.ajax({
                url: "{{ route('form-input.working-order.getjobcategory') }}",
                type: 'get',
                dataType: "json",
                data: {
                    'wo_category': wo_category,
                    'department': department,
                },
                success: function(data) {
                    if (data.success == true) {
                        $('#job_category').empty();
                        console.log(data.job_categories.length);
                        $('#job_category').append('<option value="" selected disabled hidden>PILIH SATU</option>');
                        $.each(data.job_categories, function(key, value) {
                            $('#job_category').append('<option value="' + value.job_category + '">' + value.job_category + '</option>');
                        });
                        if (data.job_categories.length == 1) {
                            console.log(1);
                            $('#job_category').prop('disabled', true);
                        } else {
                            console.log(2);
                            $('#job_category').prop('disabled', false);
                        }
                        checkwocategory();
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

        function getdevicemodel(d) {
            var device_id = '#details' + d + 'device';
            var device_model_id = '#details' + d + 'device_model';
            var device_code_id = 'details' + d + 'device_code';
            var location_id = '#details' + d + 'location';
            var device = $(device_id).val();
            var department = $("select[name=department]").val();
            var location = $(location_id).val();
            var disturbance_category_id = '#details' + d + 'disturbance_category';

            $.ajax({
                url: "{{ route('form-input.working-order.getdevicemodel') }}",
                type: 'get',
                dataType: "json",
                data: {
                    'device': device,
                    'department': department,
                    'location': location,
                },
                success: function(data) {
                    if (data.success == true) {
                        console.log('getdevicemodel', data.devices);
                        $(device_model_id).empty();
                        $(device_model_id).append('<option value="" selected disabled hidden>PILIH SATU</option>');
                        if (data.devices == null) {
                            $(device_model_id).prop("disabled", true);
                        } else {
                            $(device_model_id).prop("disabled", false);
                            $.each(data.devices, function(key, value) {
                                $(device_model_id).append('<option value="' + value.id + '">' + value.brand + '</option>');
                            });
                        }
                        document.getElementById(device_code_id).value = '';
                        $(disturbance_category_id).empty();
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

        function getdevicecode(d) {
            var device_model_id = '#details' + d + 'device_model';
            var device_code_id = 'details' + d + 'device_code';
            var device_model = $(device_model_id).val();

            $.ajax({
                url: "{{ route('form-input.working-order.getdevicecode') }}",
                type: 'get',
                dataType: "json",
                data: {
                    'device_model': device_model,
                },
                success: function(data) {
                    if (data.success == true) {
                        console.log(data);
                        if(data.devices != null){
                            document.getElementById(device_code_id).value = data.devices.activa_number;
                        }
                        if ($('#wo_category').val() == 'LAPORAN GANGGUAN') {
                            getDisturbanceCategory(d);
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

        function getDisturbanceCategory(d) {
            var device_model_id = '#details' + d + 'device_model';
            var disturbance_category_id = '#details' + d + 'disturbance_category';
            var device = $(device_model_id).val();
            var department = $("select[name=department]").val();
            console.log(disturbance_category_id);

            $.ajax({
                url: "{{ route('form-input.working-order.getdisturbancecategory') }}",
                type: 'get',
                dataType: "json",
                data: {
                    'device': device,
                },
                success: function(data) {
                    if (data.success == true) {
                        console.log(data);
                        $(disturbance_category_id).empty();
                        $(disturbance_category_id).append('<option value="" selected disabled hidden>PILIH SATU</option>');
                        $.each(data.disturbances, function(key, value) {
                            console.log(value);
                            $(disturbance_category_id).append('<option value="' + value.id + '">' + value.disturbance_category + '</option>');
                        });
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

        function doSubmit(e) {
            hideModal();
            e.preventDefault();
            e.stopPropagation();
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
                formData.append('details[' + detailIndex + '][disturbance_category]', $(this).find('select[name="details[' + detailIndex + '][disturbance_category]"]').val());
                formData.append('details[' + detailIndex + '][device]', $(this).find('select[name="details[' + detailIndex + '][device_model]"]').val());
                formData.append('details[' + detailIndex + '][description]', $(this).find('textarea[name="details[' + detailIndex + '][description]"]').val());

                // Append file inputs
                var files = $(this).find('input[type="file"]');
                $.each(files, function(index, fileInput) {
                    if (fileInput.files.length > 0) {
                        formData.append('details[' + detailIndex + '][photo' + (index + 1) + ']', fileInput.files[0]);
                    }
                });
            });


            // AJAX request
            artLoadingDialogDo("Proses menyimpan..", function() {
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
                                window.location.href = "{{ route('form-input.working-order.index') }}";
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

        function showModal() {
            modal.style.display = 'flex';

            // Disable the "tanggal efektif" field without blurring
            $(effective_date).prop("disabled", true);

            // Ensure there is no blur effect applied
            $(effective_date).css({
                "filter": "none",             // No blur effect
                "opacity": "1",               // Ensure full opacity
                "background-color": "inherit", // Keep the background color
                "border": "1px solid #ccc"    // Set the border color to the default (or customize as needed)
            });

            // Remove focus from the input field
            $(effective_date).blur();
        }

        // Hide modal function
        function hideModal() {
            modal.style.display = 'none';
            $(effective_date).prop("disabled", false);
        }

        cancelBtn.addEventListener('click', hideModal);
        actionBtn.addEventListener('click', doSubmit);
    </script>
@endsection
