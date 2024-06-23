@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Tambah Data Lokasi</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Tambah Data Lokasi</li>
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
                        {{-- NAMA LOKASI --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NAMA LOKASI *</label>
                            <div class="col-md-6">
                                <input maxlength="50" required id="location_name" type="text" class="text-uppercase form-control" name="location_name" title="NAMA LOKASI" placeholder="NAMA LOKASI">
                            </div>
                        </div>
                        <br>

                        {{-- DESKRIPSI --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DESKRIPSI *</label>
                            <div class="col-md-6">
                                <textarea maxlength="100" required class="form-control" rows="5" id="description" type="text" class="text-uppercase form-control" name="description" title="DESKRIPSI" placeholder="DESKRIPSI"></textarea>
                            </div>
                        </div>
                        <br>

                        {{-- TIPE LOKASI --}}
                        <div class="row mb-2">
                            <label class="col-md-2">TIPE LOKASI</label>
                            <div class="col-md-6">
                                <input maxlength="50" required id="location_type" type="text" class="text-uppercase form-control" name="location_type" title="TIPE LOKASI" placeholder="TIPE LOKASI">
                            </div>
                        </div>
                        <br>

                        {{-- ADDRESS --}}
                        <div class="row mb-2">
                            <label class="col-md-2">ALAMAT</label>
                            <div class="col-md-6">
                                <textarea maxlength="255" required class="form-control" rows="5" id="addresss" type="text" class="text-uppercase form-control" name="addresss" title="ALAMAT" placeholder="ALAMAT"></textarea>
                            </div>
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
    function doSave(){
        $('#form_result').html('');

        var location_name = $('#location_name').val();
        var description   = $('#description').val();
        var location_type = $('#location_type').val();
        var addresss      = $('#addresss').val();
        
        $.ajax({
            url : '{!! route('masters/location/create-new/create') !!}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{!!csrf_token()!!}'
            },
            dataType:"json",
            data: {
                'location_name'   : location_name,
                'description'     : description,
                'location_type'   : location_type,
                'addresss'        : addresss,
            },
            success: function(data){
                if(data.errors)
                {
                    $('#form_result').html(data.message);
                }
                if(data.success) 
                {
                    $('#form_result').html(data.message);
                    $('#location_name').val('');
                    $('#description').val('');
                    $('#location_type').val('');
                    $('#location_type').val('');
                    $('#addresss').val('');
                    setTimeout(function(){ window.location.href = '{{url('masters/location/index')}}'; }, 1500);
                }  
            },
            error: function(data) {
                console.log(data);
                html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                $('#form_result').html(html);
                if(data.responseJSON.message) {
                    var target = data.responseJSON.errors;
                    for (var k in target){
                        if(!Array.isArray(target[k]['0']))
                        {
                            var msg = target[k]['0'];
                            artCreateFlashMsg(msg,"danger",true);
                        }
                    }
                }
            }
        });return false;
    }

    function doBack(){
        setTimeout(function(){ window.location.href = '{{url('masters/location/index')}}'; }, 100);
    }
</script>

@endsection