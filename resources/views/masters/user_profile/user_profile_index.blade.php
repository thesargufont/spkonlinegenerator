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
<div class="container" style="overflow:scroll; height:100%;">
    <div class="card-header">
        <div class="btn-group" role="group">
            <div class="form-group">
                {{-- <button type="button" name="back" id="back" class="btn btn-secondary" onclick="doBack();"><i class="fa fa-arrow-left"></i> {{ucwords(__('Kembali'))}}</button> --}}
                <button type="button" name="back" id="back" class="btn btn-secondary" onclick="doBack();"><i class="fa fa-arrow-left"></i> {{ucwords(__('Kembali'))}}</button>
                {{-- <button type="button" name="save" id="saveBtn" class="btn btn-primary" onclick="doSave();"><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan'))}}</button> --}}
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
                                <input disabled required maxlength="255" id="name" type="text" class="text-uppercase form-control" title="NAMA" placeholder="NAMA">
                            </div>
                        </div>
                        <br>

                        {{-- NIK --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NIK *</label>
                            <div class="col-md-6">
                                <input disabled required max="10" id="nik" type="text" class="text-uppercase form-control" title="NIK" placeholder="NIK">
                            </div>
                        </div>
                        <br>

                        {{-- DEPARTMENT --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DEPARTEMENT *</label>
                            <div class="col-md-6">
                                <select disabled title="DEPARTMENT" id="department" class="form-control">
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
                                <select disabled title="JENIS KELAMIN" id="gender" class="form-control">
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
                                <input disabled maxlength="50" required id="email" type="email" class="form-control" title="EMAIL" placeholder="EMAIL">
                            </div>
                        </div>
                        <br>

                        {{-- PHONE NUMBER --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NOMOR TELEPHONE </label>
                            <div class="col-md-6">
                                <input disabled max="15" id="phone_number" type="tel" class="text-uppercase form-control" title="NOMOR TELEPHONE" placeholder="NOMOR TELEPHONE" onkeypress="return onlyNumberKey(event)">
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
                        <span id="form_result_password"></span>
                        {{-- PASSWORD --}}
                        <div class="row mb-2">
                            <label class="col-md-2">PASSWORD LAMA *</label>
                            <div class="col-md-6">
                                <input maxlength="50" id="password" type="password" class="form-control" title="PASSWORD LAMA" placeholder="PASSWORD LAMA">
                            </div>
                        </div>
                        <br>

                        {{-- NEW PASSWORD --}}
                        <div class="row mb-2">
                            <label class="col-md-2">PASSWORD BARU *</label>
                            <div class="col-md-6">
                                <input maxlength="50" id="new_password" type="password" class="form-control" title="PASSWORD BARU" placeholder="PASSWORD BARU">
                            </div>
                        </div>
                        <br>

                        {{-- CONFIRM PASSWORD --}}
                        <div class="row mb-2">
                            <label class="col-md-2">KONFIRMASI PASSWORD BARU *</label>
                            <div class="col-md-6">
                                <input maxlength="50" id="confirm_password" type="password" class="form-control" title="KONFIRMASI PASSWORD BARU" placeholder="KONFIRMASI PASSWORD BARU">
                            </div>
                        </div>
                        <br>
                        <button type="button" name="save" id="savePassword" class="btn btn-primary" ><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan Password'))}}</button>
                        <br>
                    </div>
                </div>

                <br>
                <br>
                <button type="button" name="reset signature" id="edit_signature" class="btn btn-primary" onclick="doResetSignature();"><i class="fa fa-file-signature"></i> {{ucwords(__('E-Signature'))}}</button>

                <div id="div-signature" class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">E-Signature</h3>
                    </div>
                    <div class="panel-body">
                        <span id="form_result_signature"></span>
                        {{-- SIGNATURE PATH --}}
                        <div class="row mb-2">
                            <label class="col-md-2">TANDA TANGAN</label>
                            <div class="col-md-4">
                                <div id='root' class="signature-canvas"></div>
                                <input type="button" value="Reset" id="resetCanvas" class="btn btn-primary" />
                                <input type="button" value="Save as image" id="getImage" class="btn btn-primary" />
                                <img id="image" class="image full-width" />
                            </div>
                            <label class="col-sm-1">PREVIEW</label> <!-- stefan tambahan -->
                            <div class="col-sm-3" style="display: flex; justify-content: left; align-items: left;">
                                <img src="{{ Storage::url($userData->signature_path) }}" alt="..tidak ditemukan." style="max-width:100%;">
                            </div>
                        </div>
                        <br>
                        <button type="button" name="save" id="saveButton" class="btn btn-primary" onclick="doSubmitSignature();"><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan Tanda Tangan'))}}</button>
                        <br>
                    </div>
                </div>
            </form>
        </div> <!-- col -->
    </div>

    {{-- <button id="showModalBtn" 
        class="button delete-button"> 
        Show Delete Confirmation 
    </button>  --}}
</div>

<body>
    <div id="modal" class="modal-container"> 
        <div class="modal-content"> 
  
            <h2>Konfirmasi</h2> 
            <p class="confirmation-message"> 
                Anda yakin akan menyimpan?? 
            </p> 
  
            <div class="button-container"> 
                <button id="cancelBtn" class="btn btn-secondary"> Batal </button> 
                <button id="deleteBtn" class="btn btn-primary"> Ya </button> 
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

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lemonadejs/dist/lemonade.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@lemonadejs/signature/dist/index.min.js"></script>
<script>
    // Get modal and buttons 
    const modal = document.getElementById('modal'); 
    const showModalSave = document.getElementById('savePassword'); 
    const cancelBtn = document.getElementById('cancelBtn'); 
    const deleteBtn = document.getElementById('deleteBtn'); 

    // Show modal function 
    function showModal() { 
        modal.style.display = 'flex'; 
    } 

    // Hide modal function 
    function hideModal() { 
        modal.style.display = 'none'; 
    } 

    // Attach click event listeners 
    showModalSave.addEventListener('click', showModal); 
    cancelBtn.addEventListener('click', hideModal); 

    deleteBtn.addEventListener('click', doSubmitPassword); 

    $(document).ready(function() {
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
        $('#form_result_signature').html('');

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
                    $('#form_result_signature').html(data.message);
                }
                if (data.success) {
                    $('#form_result_signature').html(data.message);
                    resetCanvas.addEventListener("click", () => {
                        component.value = [];
                    });
                }
            },
            error: function(data) {
                console.log(data);
                html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                $('#form_result_signature').html(html);
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

    function doSubmitPassword() {
        modal.style.display = 'none'; 

        $('#form_result_password').html('');

        var password = $('#password').val();
        var new_password = $('#new_password').val();
        var confirm_password = $('#confirm_password').val();

        $.ajax({
            url: "{!! route('masters/profile-user/password') !!}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{!!csrf_token()!!}'
            },
            dataType: "json",
            data: {
                'password': password,
                'new_password': new_password,
                'confirm_password': confirm_password,
            },
            success: function(data) {
                if (data.errors) {
                    $('#form_result_password').html(data.message);
                }
                if (data.success) {
                    $('#form_result_password').html(data.message);
                    $('#password').val('');
                    $('#new_password').val('');
                    $('#confirm_password').val('');
                }
            },
            error: function(data) {
                console.log(data);
                html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                $('#form_result_password').html(html);
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

    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        let ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }

    function doResetPassword() {
        if (firstResetPassword == false) {
            firstResetPassword = true;
            $('#div-password').show();
        } else {
            firstResetPassword = false;
            $('#div-password').hide();
        }
    }

    function doResetSignature() {
        if (firstResetSignature == false) {
            firstResetSignature = true;
            $('#div-signature').show();
        } else {
            firstResetSignature = false;
            $('#div-signature').hide();
        }
    }

    function doSave() {
        $('#form_result').html('');

        var name = $('#name').val();
        var nik = $('#nik').val();
        var department = $('#department').val();
        var gender = $('#gender').val();
        var email = $('#email').val();
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
                    }, 100);
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
        modal.style.display = 'none';
        setTimeout(function() {
            window.location.href = "{{url('home')}}";
        }, 100);
    }    
</script>

@endsection