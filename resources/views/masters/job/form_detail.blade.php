@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Detail Data Pekerjaan</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Detail Data Pekerjaan</li>
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
                        <h3 class="panel-title">Form Data Pekerjaan</h3>
                    </div>
                    <div class="panel-body">
                        <span id="form_result"></span>
                        {{-- KATEGORI WO --}}
                        <div class="row mb-2">
                            <label class="col-md-2">KATEGORI WO</label>
                            <div class="col-md-6">
                                <input disabled required id="wo_category" type="text" class="text-uppercase form-control" name="wo_category" title="KATEGORI WO" placeholder="KATEGORI WO">
                            </div>
                            <small class="text-danger" id="wo_category_error"></small>
                        </div>
                        <br>
                        
                        {{-- KATEGORI PEKERJAAN --}}
                        <div class="row mb-2">
                            <label class="col-md-2">KATEGORI PEKERJAAN</label>
                            <div class="col-md-6">
                                <input disabled required id="job_category" type="text" class="text-uppercase form-control" name="job_category" title="KATEGORI PEKERJAAN" placeholder="KATEGORI PEKERJAAN">
                            </div>
                            <small class="text-danger" id="job_category_error"></small>
                        </div>
                        <br>

                        {{-- DEPARTEMEN --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DEPARTEMEN</label>
                            <div class="col-md-6">
                                <textarea disabled required class="form-control" rows="5" id="department" type="text" class="text-uppercase form-control" name="department" title="DEPARTEMEN" placeholder="DEPARTEMEN"></textarea>
                            </div>
                            <small class="text-danger" id="department_error"></small>
                        </div>
                        <br>

                        {{-- DESKRIPSI --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DESKRIPSI</label>
                            <div class="col-md-6">
                                <textarea disabled required class="form-control" rows="5" id="job_description" type="text" class="text-uppercase form-control" name="job_description" title="DESKRIPSI" placeholder="DESKRIPSI"></textarea>
                            </div>
                            <small class="text-danger" id="job_description_error"></small>
                        </div>
                        <br>

                        {{-- AKTIV --}}
                        <div class="row mb-2">
                            <label class="col-md-2">STATUS</label>
                            <div class="col-md-6">
                                <input disabled required id="status" type="text" class="text-uppercase form-control" name="status" title="STATUS" placeholder="STATUS">
                            </div>
                            <small class="text-danger" id="status_error"></small>
                        </div>
                        <br>

                        {{-- START EFFECTIVE --}}
                        <div class="row mb-2">
                            <label class="col-md-2">START EFFECTIVE</label>
                            <div class="col-md-6">
                                <input disabled required id="start_effective" type="text" class="text-uppercase form-control" name="start_effective" title="START EFFECTIVE" placeholder="START EFFECTIVE">
                            </div>
                            <small class="text-danger" id="start_effective_error"></small>
                        </div>
                        <br>

                        {{-- END EFFECTIVE --}}
                        <div class="row mb-2">
                            <label class="col-md-2">END EFFECTIVE</label>
                            <div class="col-md-6">
                                <input disabled required id="end_effective" type="text" class="text-uppercase form-control" name="end_effective" title="END EFFECTIVE" placeholder="END EFFECTIVE">
                            </div>
                            <small class="text-danger" id="end_effective_error"></small>
                        </div>
                        <br>

                        {{-- CREATED BY --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DIBUAT OLEH</label>
                            <div class="col-md-6">
                                <input disabled required id="created_by" type="text" class="text-uppercase form-control" name="created_by" title="DIBUAT OLEH" placeholder="DIBUAT OLEH">
                            </div>
                            <small class="text-danger" id="created_by_error"></small>
                        </div>
                        <br>
                        
                        {{-- CREATED BY --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DIBUAT PADA</label>
                            <div class="col-md-6">
                                <input disabled required id="created_at" type="text" class="text-uppercase form-control" name="created_at" title="DIBUAT PADA" placeholder="DIBUAT PADA">
                            </div>
                            <small class="text-danger" id="created_at_error"></small>
                        </div>
                        <br>

                        <div class="row mb-2">
                            <label class="col-md-2">DIUBAH OLEH</label>
                            <div class="col-md-6">
                                <input disabled required id="updated_by" type="text" class="text-uppercase form-control" name="updated_by" title="DIUBAH OLEH" placeholder="DIUBAH OLEH">
                            </div>
                            <small class="text-danger" id="updated_by_error"></small>
                        </div>
                        <br>
                        
                        {{-- CREATED BY --}}
                        <div class="row mb-2">
                            <label class="col-md-2">DIUBAH PADA</label>
                            <div class="col-md-6">
                                <input disabled required id="updated_at" type="text" class="text-uppercase form-control" name="updated_at" title="DIUBAH PADA" placeholder="DIUBAH PADA">
                            </div>
                            <small class="text-danger" id="updated_at_error"></small>
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
        $('#wo_category').val('{{ $wo_category }}');
        $('#job_category').val('{{ $job_category }}');
        $('#job_description').val('{{ $job_description }}');
        $('#department').val('{{ $department }}');
        $('#status').val('{{ $active }}');
        $('#start_effective').val('{{ $start_effective }}');
        $('#end_effective').val('{{ $end_effective }}');
        $('#created_by').val('{{ $created_by }}');
        $('#created_at').val('{{ $created_at }}');
        $('#updated_by').val('{{ $updated_by }}');
        $('#updated_at').val('{{ $updated_at }}');
    });
    
    function doBack() {
        setTimeout(function() {
            window.location.href = "{{url('masters/job/index')}}";
        }, 100);
    }
</script>

@endsection