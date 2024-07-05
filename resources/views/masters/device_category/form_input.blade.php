@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Tambah Data Kategori Alat</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Tambah Data Kategori Alat</li>
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
                        <h3 class="panel-title">Form Data Kategori Alat</h3>
                    </div>
                    <div class="panel-body">
                        <span id="form_result"></span>

                        {{-- JOB KATEGORY --}}
                        <div class="row mb-2">
                            <label class="col-md-2">KATEGORI ALAT *</label>
                            <div class="col-md-6">
                                <input maxlength="150" required id="device_category" type="text" class="text-uppercase form-control" name="device_category" title="KATEGORI ALAT" placeholder="KATEGORI ALAT">
                            </div>
                        </div>
                        <br>

                        {{-- KATEGORi GANGGUAN --}}
                        <div class="row mb-2">
                            <label class="col-md-2">KATEGORi GANGGUAN *</label>
                            <div class="col-md-6">
                                <input maxlength="150" required id="disturbance_category" type="text" class="text-uppercase form-control" name="disturbance_category" title="KATEGORi GANGGUAN" placeholder="KATEGORi GANGGUAN">
                            </div>
                        </div>
                        <br>
                        <br>
                    </div> <!-- panel-body -->
                </div> <!-- panel -->
            </form>
        </div> <!-- col -->
    </div>
</div>

<script>
    function doSave(){
        $('#form_result').html('');

        var device_category  = $('#device_category').val();
        var disturbance_category = $('#disturbance_category').val();
        
        artLoadingDialogDo("Please wait, we process your request..",function(){
            $.ajax({
                url : '{!! route('masters/device-category/create-new/create') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{!!csrf_token()!!}'
                },
                dataType:"json",
                data: {
                    'device_category'  : device_category,
                    'disturbance_category' : disturbance_category,
                },
                success: function(data){
                    artLoadingDialogClose();
                    if(data.errors)
                    {
                        $('#form_result').html(data.message);
                    }
                    if(data.success) 
                    {
                        $('#form_result').html(data.message);
                        $('#device_category').val('');
                        $('#disturbance_category').val('');
                        setTimeout(function(){ window.location.href = '{{url('masters/device-category/index')}}'; }, 1500);
                    }  
                },
                error: function(data) {
                    artLoadingDialogClose();
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
            });
        });
        return false;
    }

    function doBack(){
        setTimeout(function(){ window.location.href = '{{url('masters/device-category/index')}}'; }, 100);
    }
</script>

@endsection