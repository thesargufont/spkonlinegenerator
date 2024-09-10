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
                <button type="button" name="template" id="template_department" class="btn btn-secondary"><i class="fa fa-download"></i> {{ucwords(__('Template'))}}</button>
                <button type="button" name="upload" id="upload" class="btn btn-secondary" disabled><i class="fa fa-upload"></i> {{ucwords(__('upload'))}}</button>
                <button type="button" name="saveBtn" id="saveBtn" class="btn btn-primary" onclick="doSaveUpload()" disabled><i class="fa fa-fw fa-save"></i> {{ucwords(__('Simpan'))}}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form id="formUpload" enctype="multipart/form-data" method="POST">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Upload Data Basecamp</h3>
                    </div>
                

                    <div class="panel-body">
                        <span id="form_result"></span>
                        
                        {{-- FILE --}}
                        <div class="row mb-2">
                            {{-- <div class="col col-md-2" style="max-width:150px; flex:0px">{!!Form::label(Str::title(__('FILE')))!!}</div> --}}
                            <div class="col-sm-8">
                                <div class="col-md-6 custom-file">
                                    {{-- <input required id="department_name" type="file" class="form-control" name="department_name" title="NAMA BAGIAN" placeholder="NAMA BAGIAN"> --}}
                                    <input type="file" class="form-control" id="validatedCustomFile" name="validatedCustomFile" title="{{__('file select input, for upload file')}} ({{__('required')}})" required>
                                    {{-- <label class="custom-file-label" for="validatedCustomFile">{{__('choose')}} {{__('file')}}...</label> --}}
                                </div>
                            </div>
                            @csrf
                            <button class="btn btn-secondary" id="btnSubmit"  type="submit"></button>
                            <input type="hidden" name="hiddenField" id="hiddenField">
                            <input type="hidden" name="fileName" id="fileName">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="div-main-table" class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Preview Data Bagian</h3>
                </div>
                <div class="panel-body">
                    <table  id="main-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Basecamp</th>
                                <th>Deskripsi</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- End Row -->
</div>

<script>
    $(document).ready(function () {
        $("#btnSubmit").hide();
    });

    $(function() {
        $('#div-main-table').hide();
        $('#upload').prop('disabled',true);
        $('#saveBtn').prop('disabled',true);

        var oTable = $('#main-table').DataTable(
        {
            filter: false,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollY: true,
            scrollX: true,
            language: {
                paginate: {
                    first: "<i class='fa fa-step-backward'></i>",
                    last: "<i class='fa fa-step-forward'></i>",
                    next: "<i class='fa fa-caret-right'></i>",
                    previous: "<i class='fa fa-caret-left'></i>"
                },
                lengthMenu:     "<div class=\"input-group\">_MENU_ &nbsp; / page</div>",
                info:           "_START_ to _END_ of _TOTAL_ item(s)",
                infoEmpty:      ""
            },
            ajax: {
                'url': '{!! route('masters/basecamp/display-upload') !!}',
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                },
                'data': function (d) {
                    d.fileName = $('#hiddenField').val();
                },
            }, 
            columns: [
                { targets: 0, data: null, orderable: false, searchable: false , className: 'text-right'},
                {data: 'basecamp' ,           name: 'basecamp'      },
                {data: 'description' ,        name: 'description'     },
                {data: 'remark' ,             name: 'remark'          },
            ],
            // order: [5, 'desc'],
            rowCallback: function( row, data, iDisplayIndex ) {
                var api = this.api();    
                var info = api.page.info();
                var page = info.page;
                var length = info.length;
                var index = (page * length + (iDisplayIndex +1));
                $('td:eq(0)', row).html(index);
            }
        });
        oTable.clear().draw();
    });

    $('#template_department').click(function(){
        html = '';
        $('#form_result').html(html);
        var uri = encodeURI("{{url('/masters/basecamp/download-template')}}");
        window.open(uri,'_blank');
    });

    $("#validatedCustomFile").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        $('#upload').prop('disabled',false);
    });

    $('#upload').click(function() {
        html = '';
        $('#form_result').html(html);
        $('#btnSubmit').click();
    });

    $("#btnSubmit").click(function(e) {
        e.preventDefault();
        var fd = new FormData(this.form);
        var fileUploadName = $('#validatedCustomFile').val();
        
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: '{!! route('masters/basecamp/upload') !!}', // This is the url we gave in the route
            headers: {
                'X-CSRF-TOKEN': '{!!csrf_token()!!}'
            },
            data: {
                fd,
                fileUploadName
            },
            data: fd,
            processData: false,
            contentType: false,
            success: function(response){ // What to do if we succeed
                $('#hiddenField').val(response.filename);
                $('#main-table').DataTable().ajax.reload();
                if(response.success == true)
                {
                    $('#saveBtn').prop('disabled', false);
                    $('#div-main-table').show();
                    html = response.message;
                    $('#form_result').html(html);
                } else {
                    $('#saveBtn').prop('disabled', true);
                    $('#div-main-table').show();
                    html = response.message;
                    $('#form_result').html(html);
                }
                check = 1;
            },
            error: function(jqXHR, textStatus, errorThrown) { 
                html = '<div class="alert alert-danger">Terjadi kesalahan</div>';
                $('#form_result').html(html);
            }
        });
    });

    function doSaveUpload(){
        $('#form_result').html('');
        artLoadingDialogDo("Harap tunggu, sedang dalam proses...",function(){
            $.ajax({
                type: 'POST',
                url: '{!! route('masters/basecamp/save-upload') !!}', 
                headers: {
                    'X-CSRF-TOKEN': '{!!csrf_token()!!}'
                },
                data: {
                    'fileData'       : $('#hiddenField').val(),
                    'fileUploadName' : $('#validatedCustomFile').val(),
                },
                success: function(data){
                    artLoadingDialogClose();
                    $('#saveBtn').prop('disabled', true);
                    if(data.success)
                    { 
                        $('#form_result').html(data.message);
                        $('#div-main-table').hide();
                        $('#validatedCustomFile').next('label').html('pilih berkas...');
                    }
                    else 
                    {
                        $('#form_result').html(data.message);
                    }
                },
                error: function(data) {
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
            return false;
        });
    }

    function doBack(){
        setTimeout(function(){ window.location.href = '{{url('masters/basecamp/index')}}'; }, 100);
    }
</script>
@endsection