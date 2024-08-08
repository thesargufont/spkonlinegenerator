@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Data Pengguna</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Data Pengguna</li>
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
                        <h3 class="panel-title">Form Data Pengguna</h3>
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
                                    @foreach ($departments as $item)
                                        @if ($item->id == $userData->departement_id)
                                            <option value={{ $item->id }} selected>{{ $item->department }}</option>
                                        @else
                                            <option value={{ $item->id }}>{{ $item->department }}</option>
                                        @endif
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
                                    @foreach ($genders as $gender)
                                        @if ($gender == $userData->gender)
                                            <option value={{ $gender }} selected>{{ $gender }}</option>
                                        @else
                                            <option value={{ $gender }}>{{ $gender }}</option>
                                        @endif
                                    @endforeach
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
                    </div> <!-- panel-body -->
                </div>

                <button type="button" name="reset password" id="edit_password" class="btn btn-primary" onclick="doResetPassword();"><i class="fa fa-user-shield"></i> {{ucwords(__('Reset Password'))}}</button>

                <div id="div-password" class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Paswword</h3>
                    </div>
                    <div class="panel-body">
                        {{-- PASSWORD --}}
                        <div class="row mb-2">
                            <label class="col-md-2">PASSWORD *</label>
                            <div class="col-md-6">
                                <input maxlength="50" id="password" type="password" class="form-control" title="PASSWORD" placeholder="PASSWORD">
                            </div>
                        </div>
                        <br>

                        {{-- NEW PASSWORD --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NEW PASSWORD *</label>
                            <div class="col-md-6">
                                <input maxlength="50" id="new_password" type="password" class="form-control" title="NEW PASSWORD" placeholder="NEW PASSWORD">
                            </div>
                        </div>
                        <br>

                        {{-- CONFIRM PASSWORD --}}
                        <div class="row mb-2">
                            <label class="col-md-2">CONFIRM PASSWORD *</label>
                            <div class="col-md-6">
                                <input maxlength="50" id="confirm_password" type="password" class="form-control" title="CONFIRM PASSWORD" placeholder="CONFIRM PASSWORD">
                            </div>
                        </div>
                        <br>
                    </div>
                </div> 

                <br>
                <br>
                <button type="button" name="reset signature" id="edit_signature" class="btn btn-primary" onclick="doResetSignature();"><i class="fa fa-file-signature"></i> {{ucwords(__('Reset E-Signature'))}}</button>

                <div id="div-signature" class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">E-Signature</h3>
                    </div>
                    <div class="panel-body">
                        {{-- SIGNATURE PATH --}}
                        <div class="row mb-2">
                            <label class="col-md-2">TANDA TANGAN</label>
                            <div class="col-md-6">
                                <div id='root' class="signature-canvas"></div>
                                <input type="button" value="Reset" id="resetCanvas" class="btn btn-primary" />
                                <input type="button" value="Save as image" id="getImage" class="btn btn-primary" />
                                <img id="image" class="image full-width" />
                            </div>
                        </div>
                        <button type="button" name="save" id="saveButton" class="btn btn-primary"  onclick="doSubmitSignature();"><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan Tanda Tangan'))}}</button>
                        <br>
                    </div>
                </div> 
            </form>
        </div> <!-- col -->
    </div>
</div>

<style>
    .signature-canvas {
      border: 2px solid #000;
      margin-bottom: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lemonadejs/dist/lemonade.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@lemonadejs/signature/dist/index.min.js"></script>
<script>
    $(document).ready(function () {
        $('#name').val('{{ $userData->name }}');
        $('#nik').val('{{ $userData->nik }}');
        $('#email').val('{{ $userData->email }}');
        $('#phone_number').val('{{ $userData->phone_number }}');
        $('#div-password').hide();
        $('#div-signature').hide();
    });

    var firstResetPassword = false;
    var firstResetSignature = false;

    const root = document.getElementById("root")
    const resetCanvas = document.getElementById("resetCanvas")
    const getImage = document.getElementById("getImage")
    // Call signature with the root element and the options object, saving its reference in a variable
    const component = Signature(root, {
        width: 500,
        height: 200,
        instructions: "Please sign in the box above"
    });

    resetCanvas.addEventListener("click", () => {
        component.value = [];
    });

    getImage.addEventListener("click", () => {
        getImage.nextElementSibling.src = component.getImage();
    });
    
    function doSubmitSignature() {
        var image = component.getImage();

        $.ajax({
            url: "{!! route('masters/profile-user/signature') !!}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{!!csrf_token()!!}'
            },
            dataType: "json",
            data: {
                'image': image,
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
    }



    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        let ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    function doResetPassword() {
        if(firstResetPassword == false){
            firstResetPassword = true;
            $('#div-password').show();
        } else {
            firstResetPassword = false;
            $('#div-password').hide();
        }
    }

    function doResetSignature() {
        if(firstResetSignature == false){
            firstResetSignature = true;
            $('#div-signature').show();
        } else {
            firstResetSignature = false;
            $('#div-signature').hide();
        }
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