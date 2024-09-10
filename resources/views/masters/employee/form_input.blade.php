@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Tambah Data Pengguna</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Tambah Data Pengguna</li>
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
                        {{-- NAMA --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NAMA *</label>
                            <div class="col-md-6">
                                <input required maxlength="255" id="name" type="text" class="text-uppercase form-control" title="NAMA" placeholder="NAMA">
                            </div>
                        </div>
                        <br>

                        {{-- NIK --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NIK *</label>
                            <div class="col-md-6">
                                <input required max="10" id="nik" type="text" class="text-uppercase form-control" title="NIK" placeholder="NIK">
                            </div>
                        </div>
                        <br>

                        {{-- DEPARTMENT --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DEPARTMENT *</label>
                            <div class="col-md-6">
                                <select title="DEPARTMENT" id="department" class="form-control">
                                    <option value="" selected disabled>PILIH SATU</option>
                                    @foreach ($departments as $item)
                                        <option value={{ $item->id }}>{{ $item->department }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>

                        {{-- GENDER --}}
                        <div class="row mb-2">
                            <label class="col-md-2">JENIS KELAMIN *</label>
                            <div class="col-md-6">
                                <select title="JENIS KELAMIN" id="gender" class="form-control">
                                    <option value="" selected disabled>PILIH SATU</option>
                                    <option value="PRIA">PRIA</option>
                                    <option value="WANITA">WANITA</option>
                                </select>
                            </div>
                        </div>
                        <br>

                        {{-- EMAIL --}}
                        <div class="row mb-2">
                            <label class="col-md-2">EMAIL *</label>
                            <div class="col-md-6">
                                <input maxlength="50" required id="email" type="email" class="form-control" title="EMAIL" placeholder="EMAIL">
                            </div>
                        </div>
                        <br>

                        {{-- PHONE NUMBER --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NOMOR TELEPHONE </label>
                            <div class="col-md-6">
                                <input max="15" id="phone_number" type="tel" class="text-uppercase form-control" title="NOMOR TELEPHONE" placeholder="NOMOR TELEPHONE" onkeypress="return onlyNumberKey(event)">
                            </div>
                        </div>
                        <br>

                        {{-- PASSWORD --}}
                        {{-- <div class="row mb-2">
                            <label class="col-md-2">PASSWORD *</label>
                            <div class="col-md-6">
                                <input maxlength="50" required id="password" type="password" class="form-control" title="PASSWORD" placeholder="PASSWORD">
                            </div>
                        </div>
                        <br> --}}

                        {{-- CONFIRM PASSWORD --}}
                        {{-- <div class="row mb-2">
                            <label class="col-md-2">CONFIRM PASSWORD *</label>
                            <div class="col-md-6">
                                <input maxlength="50" required id="confirm_password" type="password" class="form-control" title="CONFIRM PASSWORD" placeholder="CONFIRM PASSWORD">
                            </div>
                        </div>
                        <br> --}}

                        {{-- SIGNATURE PATH --}}
                        {{-- <div class="row mb-2">
                            <label class="col-md-2">TANDA TANGAN</label>
                            <div class="col-md-6">
                                <canvas id="signatureCanvas" class="signature-canvas"></canvas>
                            </div>
                        </div>
                        <button type="button" name="save" id="saveSignature" class="btn btn-primary" onclick="doSaveSignature();"><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan Tanda Tangan'))}}</button>
                        <br> --}}

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
    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        let ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    function doSave() {
        $('#form_result').html('');

        var name         = $('#name').val();
        var nik          = $('#nik').val();
        var department   = $('#department').val();
        var gender       = $('#gender').val();
        var email        = $('#email').val();
        var phone_number = $('#phone_number').val();

        $.ajax({
            url: "{!! route('masters/employee/create-new/create') !!}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{!!csrf_token()!!}'
            },
            dataType: "json",
            data: {
                'name': name,
                'nik': nik,
                'department': department,
                'gender': gender,
                'email': email,
                'phone_number': phone_number,
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
                        window.location.href = "{{url('masters/employee/index')}}";
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
            window.location.href = "{{url('masters/employee/index ')}}";
        }, 100);
    }
</script>

@endsection