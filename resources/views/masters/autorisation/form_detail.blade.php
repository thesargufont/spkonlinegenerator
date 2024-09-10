@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Detail Otoritas</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Detail Otoritas</li>
</ol>
<div class="clearfix"></div>
@endsection

@section('content')
<div class="container">
    <div class="card-header">
        <div class="btn-group" role="group">
            <div class="form-group">
                <button type="button" name="back" id="back" class="btn btn-secondary" onclick="doBack();"><i class="fa fa-arrow-left"></i> {{ucwords(__('Kembali'))}}</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form method="POST" id="search-form" class="form" role="form">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Form Otoritas</h3>
                    </div>
                    <div class="panel-body">
                        <span id="form_result"></span>
                        {{-- NIK --}}
                        <div class="row mb-2">
                            <label class="col-md-2">NIK</label>
                            <div class="col-md-6">
                                <input disabled  class="form-control" id="nik" type="text" class="text-uppercase form-control" name="nik" title="NIK" placeholder="NIK">
                            </div>
                            <small class="text-danger" id="device_description_error"></small>
                        </div>
                        <br>

                        {{-- PENGGUNA --}}
                        <div class="row mb-2">
                            <label class="col-md-2">PENGGUNA</label>
                            <div class="col-md-6">
                                <input disabled  id="name" type="text" class="text-uppercase form-control" name="name" title="PENGGUNA" placeholder="PENGGUNA">
                            </div>
                            <small class="text-danger" id="device_name_error"></small>
                        </div>
                        <br>
                        
                        {{-- ROLE --}}
                        <div class="row mb-2">
                            <label class="col-md-2">ROLE</label>
                            <div class="col-md-6">
                                <input disabled  id="role" type="text" class="text-uppercase form-control" name="role" title="ROLE" placeholder="ROLE">
                            </div>
                            <small class="text-danger" id="brand_error"></small>
                        </div>
                        <br>

                        {{-- AUTHORITY --}}
                        <div class="row mb-2">
                            <label class="col-md-2">OTORITAS</label>
                            <div class="col-md-6">
                                <input disabled  class="form-control" rows="5" id="authority" type="text" class="text-uppercase form-control" name="authority" title="OTORITAS" placeholder="OTORITAS">
                            </div>
                            <small class="text-danger" id="location_error"></small>
                        </div>
                        <br>

                        {{-- AKTIV --}}
                        <div class="row mb-2">
                            <label class="col-md-2">STATUS</label>
                            <div class="col-md-6">
                                <input disabled  id="status" type="text" class="text-uppercase form-control" name="status" title="STATUS" placeholder="STATUS">
                            </div>
                            <small class="text-danger" id="status_error"></small>
                        </div>
                        <br>

                        {{-- START EFFECTIVE --}}
                        <div class="row mb-2">
                            <label class="col-md-2">TANGGAL EFEKTIF</label>
                            <div class="col-md-6">
                                <input disabled  id="start_effective" type="text" class="text-uppercase form-control" name="start_effective" title="START EFFECTIVE" placeholder="START EFFECTIVE">
                            </div>
                            <small class="text-danger" id="start_effective_error"></small>
                        </div>
                        <br>

                        {{-- END EFFECTIVE --}}
                        <div class="row mb-2">
                            <label class="col-md-2">BERAKHIR EFEKTIF</label>
                            <div class="col-md-6">
                                <input disabled  id="end_effective" type="text" class="text-uppercase form-control" name="end_effective" title="END EFFECTIVE" placeholder="END EFFECTIVE">
                            </div>
                            <small class="text-danger" id="end_effective_error"></small>
                        </div>
                        <br>

                    </div> <!-- panel-body -->
                </div> <!-- panel -->
            </form>
        </div> <!-- col -->
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#nik').val('{{ $nik }}');
        $('#name').val('{{ $name }}');
        $('#role').val('{{ $role }}');
        $('#authority').val('{{ $authority }}');
        $('#status').val('{{ $active }}');
        $('#start_effective').val('{{ $start_effective }}');
        $('#end_effective').val('{{ $end_effective }}');
    });
    
    function doBack() {
        setTimeout(function() {
            window.location.href = "{{url('masters/autorisation/index')}}";
        }, 100);
    }
</script>

@endsection