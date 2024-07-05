@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Tambah Data Basecamp</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Tambah Data Basecamp</li>
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
                        <h3 class="panel-title">Form Data Basecamp</h3>
                    </div>
                    <div class="panel-body">
                        <span id="form_result"></span>
                        {{-- NAMA BASECAMP --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NAMA BASECAMP *</label>
                            <div class="col-md-6">
                                <input required id="basecamp_name" type="text" class="text-uppercase form-control" name="basecamp_name" title="NAMA BASECAMP" placeholder="NAMA BASECAMP">
                            </div>
                            <small class="text-danger" id="basecamp_name_error"></small>
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

<script>
    function doSave() {
        $('#form_result').html('');

        var basecamp_name = $('#basecamp_name').val();
        var description = $('#description').val();

        artLoadingDialogDo("Please wait, we process your request..",function(){
            $.ajax({
                url: "{!! route('masters/basecamp/create-new/create') !!}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{!!csrf_token()!!}'
                },
                dataType: "json",
                data: {
                    'basecamp_name': basecamp_name,
                    'description': description,
                },
                success: function(data) {
                    artLoadingDialogClose();
                    if (data.errors) {
                        $('#form_result').html(data.message);
                    }
                    if (data.success) {
                        $('#form_result').html(data.message);
                        $('#basecamp_name').val('');
                        $('#description').val('');
                        setTimeout(function() {
                            window.location.href = "{{url('masters/basecamp/index')}}";
                        }, 1500);
                    }
                },
                error: function(data) {
                    artLoadingDialogClose();
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
        });
        return false;
    }

    function doBack() {
        setTimeout(function() {
            window.location.href = "{{url('masters/basecamp/index')}}";
        }, 100);
    }
</script>

@endsection