@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Tambah Data Pekerjaan</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Tambah Data Pekerjaan</li>
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
                        <h3 class="panel-title">Form Data Pekerjaan</h3>
                    </div>
                    <div class="panel-body">
                        <span id="form_result"></span>
                        {{-- WO KATEGORI --}}
                        <div class="row mb-2">
                            <label class="col-md-2">KATEGORI WO *</label>
                            <div class="col-md-6">
                                <select title="KATEGORI WO" id="wo_category" class="form-control">
                                    <option value="" selected>PILIH SATU</option>
                                    <option value="PEKERJAAN" selected>PEKERJAAN</option>
                                    <option value="LAPORAN GANGGUAN" selected>LAPORAN GANGGUAN</option>
                                </select>
                            </div>
                        </div>
                        <br>

                        {{-- JOB KATEGORY --}}
                        <div class="row mb-2">
                            <label class="col-md-2">KATEGORI PEKERJAAN *</label>
                            <div class="col-md-6">
                                <input maxlength="50" required id="job_category" type="text" class="text-uppercase form-control" name="job_category" title="KATEGORI PEKERJAAN" placeholder="KATEGORI PEKERJAAN">
                            </div>
                        </div>
                        <br>

                        {{-- DESKRIPSI --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DESKRIPSI</label>
                            <div class="col-md-6">
                                <textarea maxlength="100" required class="form-control" rows="5" id="description" type="text" class="text-uppercase form-control" name="description" title="DESKRIPSI" placeholder="DESKRIPSI"></textarea>
                            </div>
                        </div>
                        <br>

                        {{-- DEPARTEMEN --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DEPARTEMEN *</label>
                            <div class="col-md-6">
                                <select title="DEPARTEMEN" id="department" class="form-control">
                                    <option value="" selected>PILIH SATU</option>
                                    @foreach ($departments as $item)
                                        <option value={{ $item->id }}>{{ $item->department_code }} - {{ $item->department }}</option>
                                    @endforeach
                                </select>
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

        var wo_category  = $('#wo_category').val();
        var job_category = $('#job_category').val();
        var description  = $('#description').val();
        var department   = $('#department').val();
        
        artLoadingDialogDo("Please wait, we process your request..",function(){
            $.ajax({
                url : '{!! route('masters/job/create-new/create') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{!!csrf_token()!!}'
                },
                dataType:"json",
                data: {
                    'wo_category'  : wo_category,
                    'job_category' : job_category,
                    'description'  : description,
                    'department'   : department,
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
                        $('#wo_category').val('');
                        $('#job_category').val('');
                        $('#description').val('');
                        $('#department').val('');
                        setTimeout(function(){ window.location.href = '{{url('masters/job/index')}}'; }, 1500);
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
        setTimeout(function(){ window.location.href = '{{url('masters/job/index')}}'; }, 100);
    }
</script>

@endsection