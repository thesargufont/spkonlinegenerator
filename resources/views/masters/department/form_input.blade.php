@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Tambah Data Bagian</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Tambah Data Bagian</li>
</ol>
<div class="clearfix"></div>
@endsection

@section('content')
<div class="container">
    <div class="card-header">
        <div class="btn-group" role="group">
            <div class="form-group">
                <button type="button" name="back" id="back" class="btn btn-secondary" onclick="doBack();"><i class="fa fa-arrow-left"></i> {{ucwords(__('Kembali'))}}</button>
                <button type="button" name="save" id="saveBtn" class="btn btn-primary" onclick="doSave();"><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan'))}}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form method="POST" id="search-form" class="form" role="form">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Form Data Bagian</h3>
                    </div>
                    <div class="panel-body">
                        <span id="form_result"></span>
                        {{-- NAMA BAGIAN --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NAMA BAGIAN *</label>
                            <div class="col-md-6">
                                <input required id="department_name" type="text" class="text-uppercase form-control" name="department_name" title="NAMA BAGIAN" placeholder="NAMA BAGIAN">
                            </div>
                            <small class="text-danger" id="department_name_error"></small>
                        </div>
                        <br>

                        {{-- DESKRIPSI --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DESKRIPSI *</label>
                            <div class="col-md-6">
                                <textarea required class="form-control" rows="5" id="description" type="text" class="text-uppercase form-control" name="description" title="DESKRIPSI" placeholder="DESKRIPSI"></textarea>
                            </div>
                            <small class="text-danger" id="description_error"></small>
                        </div>
                        <br>

                    </div> <!-- panel-body -->
                </div> <!-- panel -->
            </form>
        </div> <!-- col -->
    </div>
</div>

<!-- Plugins js -->
{{-- <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js" type="text/javascript') }}"></script>
<script src="{{ asset('pages/form-advanced.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script> --}}

<script>
    function showConfirmTest() {
        var department_name = $('#department_name').val();
        if (!!department_name && department_name.toUpperCase() == '') {
            console.log('kosong');
        } else {
            console.log('ada');
        }
        return false;
        $('#form_result').html('');
        html = '<div class="alert alert-danger">Lantai sudah pernah ada</div>';
        $('#form_result').html(html);
    }

    function confirmTestCallback() {
        $("#back").attr("disabled", false);
        $("#saveBtn").attr("disabled", false);
        $(".delete-admin").attr("disabled", false);
        $('#submitBtn').click();
    }

    $('#submitBtn').click(function(event) {
        console.log('masukkkkk');
        $(".form-control").removeClass('is-invalid');
        event.preventDefault();
        $("[id*=_error]").hide();
        $("[id*=_error]").html("{{ucfirst(__('please fill out this field'))}}");
        var isError = false;
        $('#form-add').find('select, textarea, input').each(function() {
            if (!$(this).prop('required')) {} else {
                if (!$(this).val()) {
                    isError = true;
                    name = $(this).attr('name');
                    $(this).addClass("is-invalid");
                    $("#" + name + "_error").show();
                    console.log($("#" + name + "_error"));
                    // fail_log += name + " is required \n";
                }
            }
        });
        if (isError) {
            return false;
        }
        saveData();
    });

    function doSave() {
        $('#form_result').html('');

        var department_name = $('#department_name').val();
        var description = $('#description').val();

        $.ajax({
            url: "{!! route('masters/department/create-new/create') !!}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{!!csrf_token()!!}'
            },
            dataType: "json",
            data: {
                'department_name': department_name,
                'description': description,
            },
            success: function(data) {
                if (data.errors) {
                    $('#form_result').html(data.message);
                }
                if (data.success) {
                    $('#form_result').html(data.message);
                    $('#department_name').val('');
                    $('#description').val('');
                    setTimeout(function() {
                        window.location.href = "{{url('masters/department/index')}}";
                    }, 1500);
                }
            },
            error: function(data) {
                console.log(data);
                html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                $('#form_result').html(html);
                if (data.responseJSON.message) {
                    var target = data.responseJSON.errors;
                    for (var k in target) {
                        if (!Array.isArray(target[k]['0'])) {
                            var msg = target[k]['0'];
                            artCreateFlashMsg(msg, "danger", true);
                        }
                    }
                }
            }
        });
        return false;
    }

    function doBack() {
        setTimeout(function() {
            window.location.href = "{{url('masters/department/index')}}";
        }, 100);
    }
</script>

@endsection