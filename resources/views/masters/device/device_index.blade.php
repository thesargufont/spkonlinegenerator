@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Data Peralatan</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Data Peralatan</li>
</ol>
<div class="clearfix"></div>
@endsection

@section('content')
<div class="container">
    <div class="card-header">
        <div class="btn-group" role="group">
            <div class="form-group">
                <button title="show/hide data filter options" type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#main-table-data-filter" aria-expanded="false" aria-controls="main-table-data-filter">{{ucfirst(__('data filter'))}}..</button>
                <button type="button" name="create_new" id="create_new" class="btn btn-secondary" onclick="location.replace('{{url('masters/device/create-new')}}');"><i class="fa fa-plus"></i> {{ucwords(__('Tambah Baru'))}}</button>
                {{-- <button type="button" name="download" id="btn_download_xlsx" class="btn btn-secondary"><i class="fa fa-fw fa-file-excel-o"></i> {{ucwords(__('Download'))}}</button>
                <button disabled type="button" name="upload" id="btn_upload_xlsx" class="btn btn-secondary"><i class="fa fa-upload"></i> {{ucwords(__('Upload'))}}</button> --}}
            </div>
        </div>
    </div>

    <div class="collapse" id="main-table-data-filter">
        <div class="card card-body">
            <form method="POST" id="search-form" class="form" role="form">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="row mb-2">
                            {{-- NAMA PERALATAN --}}
                            <div class="col-md-6">
                                <label class="col-md-2">NAMA PERALATAN</label>
                                <div class="col-md-6">
                                    <input maxlength="150" id="device_name" type="text" class="text-uppercase form-control" name="device_name" title="NAMA PERALATAN" placeholder="NAMA PERALATAN">
                                    <input name="device_name_id" id="location_name_id" type="hidden"/>
                                </div>
                            </div>
                            
    
                            {{-- BRAND --}}
                            <div class="col-md-6">
                                <label class="col-md-2">BRAND</label>
                                <div class="col-md-6">
                                    <input maxlength="50" id="brand" type="text" class="text-uppercase form-control" name="brand" title="BRAND" placeholder="BRAND">
                                    <input name="brand_id" id="location_name_id" type="hidden"/>
                                </div>
                            </div>

                        </div>
                        <br>
                        
                        <div class="row mb-2">
                            {{-- LOKASI --}}
                            <div class="col-md-6">
                                <label class="col-md-2">LOKASI</label>
                                <div class="col-md-6">
                                    <select title="LOKASI" id="location" class="form-control">
                                        <option value="" selected>SEMUA LOKASI</option>
                                        @foreach ($locations as $item)
                                            <option value={{ $item->id }}>{{ $item->location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
    
                            {{-- DEPARTEMEN --}}
                            <div class="col-md-6">
                                <label class="col-md-2">DEPARTEMEN</label>
                                <div class="col-md-6">
                                    <select title="DEPARTEMEN" id="department" class="form-control">
                                        <option value="" selected>SEMUA DEPARTEMEN</option>
                                        @foreach ($departments as $item)
                                            <option value={{ $item->id }}>{{ $item->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <br>
                        

                        <div class="row mb-2">
                            {{-- KATEGORI ALAT --}}
                            <div class="col-md-6">
                                <label class="col-md-2">KATEGORI ALAT</label>
                                <div class="col-md-6">
                                    <select title="KATEGORI ALAT" id="device_category" class="form-control">
                                        <option value="" selected>SEMUA KATEGORI ALAT</option>
                                        @foreach ($deviceCategories as $item)
                                            <option value={{ $item->id }}>{{ $item->device_category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                           
    
                            {{-- NOMOR SERI --}}
                            <div class="col-md-6">
                                <label class="col-md-2">NOMOR SERI</label>
                                <div class="col-md-6">
                                    <input maxlength="100" id="serial_number" type="text" class="text-uppercase form-control" name="serial_number" title="NOMOR SERI" placeholder="NOMOR SERI">
                                    <input name="serial_number_id" id="location_name_id" type="hidden"/>
                                </div>
                            </div>

                        </div>
                        <br>
                        

                        <div class="row mb-2">
                            {{-- EQ ID --}}
                            <div class="col-md-6">
                                <label class="col-md-2">NOMOR AKTIVA</label>
                                <div class="col-md-6">
                                    <input maxlength="50" id="activa_number" type="text" class="text-uppercase form-control" name="activa_number" title="NOMOR AKTIVA" placeholder="NOMOR AKTIVA">
                                    <input name="activa_number_id" id="location_name_id" type="hidden"/>
                                </div>
                            </div>
    
                            {{-- STATUS --}}
                            <div class="col-md-6">
                                <label class="col-md-2">STATUS</label>
                                <div class="col-md-6">
                                    <select title="STATUS" id="status" class="form-control">
                                        <option value="1" selected>AKTIF</option>
                                        <option value="0">TIDAK AKTIF</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <br>
                        {{-- SEARCH --}}
                        <div class="row">
                            <div class="col col-md-3"><button type="submit" class="btn btn-primary" title="search"><i class="fa fa-search"></i> {{ucwords(__('search'))}}</button> </div>
                        </div>
                    </div> <!-- panel-body -->
                </div> <!-- panel -->
            </form>
        </div>
    </div>  

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Data Peralatan</h3>
                </div>
                <div class="panel-body">
                    <span id="form_result"></span>
                    <table  id="main-table" class="table table-striped table-bordered " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Nama Peralatan</th>
                                <th>Deskripsi</th>
                                <th>Brand</th>
                                <th>Lokasi</th>
                                <th>Departemen</th>
                                <th>Kategori Alat</th>
                                <th>Nomor Seri</th>
                                <th>Nomor Aktiva</th>
                                <th>Status</th>
                                <th>Start Effective</th>
                                <th>End Effective</th>
                                <th>Dibuat Oleh</th>
                                <th>Dibuat Pada</th>
                                <th>Diubah Oleh</th>
                                <th>Diubah Pada</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- End Row -->
</div>

<!-- Plugins js -->
<script src="{{ asset('plugins/timepicker/bootstrap-timepicker.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript') }}"></script>
<script src="{{ asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js" type="text/javascript') }}"></script>
<script src="{{ asset('pages/form-advanced.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

<script>
    $(function() {
        var oTable = $('#main-table').DataTable({
            filter: false,
            processing: true,
            serverSide: true,
            // deferLoading: 0, //disable auto load
            stateSave: false,
            scrollY: 500,
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
                'url': '{!! route('masters/device/dashboard-data') !!}',
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                },
                'data': function (d) {
                    d.device_name     = $('#device_name').val(); 
                    d.brand           = $('#brand').val(); 
                    d.location        = $('#location').val(); 
                    d.department      = $('#department').val(); 
                    d.device_category = $('#device_category').val(); 
                    d.serial_number   = $('#serial_number').val(); 
                    d.activa_number   = $('#activa_number').val(); 
                    d.status          = $('#status').val(); 
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false}, 
                { data : 'device_name' ,             name :  'device_name'           },
                { data : 'device_description' ,      name :  'device_description'    },
                { data : 'brand' ,                   name :  'brand'                 },
                { data : 'location' ,                name :  'location'              },
                { data : 'department' ,              name :  'department'            },
                { data : 'device_category' ,         name :  'device_category'       },
                { data : 'serial_number' ,           name :  'serial_number'         },
                { data : 'activa_number' ,           name :  'activa_number'                 },
                { data : 'active' ,                  name :  'active'                },
                { data : 'start_effective' ,         name :  'start_effective'       },
                { data : 'end_effective' ,           name :  'end_effective'         },
                { data : 'created_by' ,              name :  'created_by',           },
                { data : 'created_at' ,              name :  'created_at',           },
                { data : 'updated_by' ,              name :  'updated_by',           },
                { data : 'updated_at' ,              name :  'updated_at',           },
            ],
            // order: [[ 2, "desc" ]],
            rowCallback: function( row, data, iDisplayIndex ) {
                var api = this.api();    
                var info = api.page.info();
                var page = info.page;
                var length = info.length;
                var index = (page * length + (iDisplayIndex +1));
            //    $('td:eq(1)', row).html(index);
            },
        }); 
    
        $('#search-form').on('submit', function(e) {
            oTable.draw();
            e.preventDefault();
        });
    });

    $('#btn_download_xlsx').click(function() {
        $('#search-form').submit();
        $('#main-table').DataTable().ajax.reload();
        var uri = encodeURI("{{url('masters/location/export-excel')}}");
        window.open(uri,'_blank');
    });

    $('#btn_upload_xlsx').click(function() {
    location.replace('{{ url('masters/location/import-excel') }}');
});

    function deleteItem(id) {
        $('#form_result').html('');

        artLoadingDialogDo("Please wait, we process your request..",function(){
            $.ajax({
                url : '{!! route('masters/device/delete-data') !!}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{!!csrf_token()!!}'
                },
                dataType:"json",
                data: {
                    'id': id,
                },
                success: function(data){
                    artLoadingDialogClose();
                    if(data.success) {
                        $('#form_result').html(data.message);
                        $('#main-table').DataTable().ajax.reload();

                    } else {
                        $('#form_result').html(data.message);
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
    }

    function showItem(id) {
        location.replace('{{ url('masters/device/detail-data') }}/' + id);
    }

    function editItem(id) {
        location.replace('{{ url('masters/device/edit-data') }}/' + id);
    }
</script>
@endsection


