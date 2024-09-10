@extends('layouts.layout')

@section('auth')
<h4 class="pull-left page-title">Data Autorisasi</h4>
<ol class="breadcrumb pull-right">
    <li><a href="#">{{Auth::user()->name}}</a></li>
    <li class="active">Data Autorisasi</li>
</ol>
<div class="clearfix"></div>
@endsection

@section('content')
<div class="container">
    <div class="card-header">
        <div class="btn-group" role="group">
            <div class="form-group">
                <button title="show/hide data filter options" type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#main-table-data-filter" aria-expanded="false" aria-controls="main-table-data-filter">{{ucfirst(__('data filter'))}}..</button>
                <button type="button" name="create_new" id="create_new" class="btn btn-secondary" onclick="location.replace('{{url('masters/autorisation/create-new')}}');"><i class="fa fa-plus"></i> {{ucwords(__('Tambah Baru'))}}</button>
                <button type="button" name="upload" id="btn_upload_xlsx" class="btn btn-secondary"><i class="fa fa-upload"></i> {{ucwords(__('Upload'))}}</button>

            </div>
        </div>
    </div>

    <div class="collapse" id="main-table-data-filter">
        <div class="card card-body">
            <form method="POST" id="search-form" class="form" role="form">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div class="row mb-2">
                            {{-- NIK --}}
                            <div class="col-md-6">
                                <label class="col-md-2">NIK</label>
                                <div class="col-md-6">
                                    <input maxlength="50" id="nik" type="text" class="text-uppercase form-control" name="nik" title="NIK" placeholder="NIK">
                                    <input name="brand_id" id="location_name_id" type="hidden"/>
                                </div>
                            </div>

                            {{-- NAMA PENGGUNA --}}
                            <div class="col-md-6">
                                <label class="col-md-2">NAMA PENGGUNA</label>
                                <div class="col-md-6">
                                    <input maxlength="150" id="user_name" type="text" class="text-uppercase form-control" name="user_name" title="NAMA PENGGUNA" placeholder="NAMA PENGGUNA">
                                    <input name="device_name_id" id="location_name_id" type="hidden"/>
                                </div>
                            </div>
                        </div>
                        <br>
                        
                        <div class="row mb-2">
                            {{-- ROLE --}}
                            <div class="col-md-6">
                                <label class="col-md-2">ROLE</label>
                                <div class="col-md-6">
                                    <select title="ROLE" id="role" class="form-control">
                                        <option value="" selected>SEMUA ROLE</option>
                                        @foreach ($roles as $item)
                                            <option value={{ $item->reff1 }}>{{ $item->reff1 }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
    
                            {{-- AUTORISASI --}}
                            <div class="col-md-6">
                                <label class="col-md-2">AUTORISASI</label>
                                <div class="col-md-6">
                                    <select title="AUTORISASI" id="authority" class="form-control">
                                        <option value="" selected>SEMUA AUTORISASI</option>
                                        @foreach ($authorities as $item)
                                            <option value={{ $item->reff1 }}>{{ $item->reff1 }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <br>

                        <div class="row mb-2">
        
                            <div class="col-md-6">
                                <label class="col-sm-2" for="effective_date">TANGGAL EFEKTIF</label>
                                <div class="col-sm-6 input-group">
                                    <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" id="effective_date" name="effective_date">
                                    <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                </div>
                            </div>
                           
    
                         
                            <div class="col-md-6">
                                <label class="col-sm-2" for="end_effective">BERAKHIR EFEKTIF</label>
                                <div class="col-sm-6 input-group">
                                    <input type="text" class="form-control datepicker" placeholder="dd/mm/yyyy" id="end_effective" name="end_effective">
                                    <span class="input-group-addon bg-custom b-0"><i class="mdi mdi-calendar text-white"></i></span>
                                </div>
                            </div>

                        </div>
                        <br>

                        <div class="row mb-2">
                            {{-- AKTIF --}}
                            <div class="col-md-6">
                                <label class="col-md-2">STATUS</label>
                                <div class="col-md-6">
                                    <select title="active" id="active" class="form-control">
                                        <option value="" selected>SEMUA</option>
                                        <option value="1">AKTIF</option>
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
                    <h3 class="panel-title">Data Autorisasi</h3>
                </div>
                <div class="panel-body">
                    <span id="form_result"></span>
                    <table  id="main-table" class="table table-striped table-bordered " cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>NIK</th>
                                <th>Pengguna</th>
                                <th>Role</th>
                                <th>Autorisasi</th>
                                <th>Aktif</th>
                                <th>Tanggal Efektif</th>
                                <th>Berakhir Efektif</th>
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
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: 'TRUE',
        });
    });

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
                'url': '{!! route('masters/autorisation/dashboard-data') !!}',
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                },
                'data': function (d) {
                    d.nik     = $('#nik').val(); 
                    d.user_name           = $('#user_name').val(); 
                    d.role        = $('#role').val(); 
                    d.authority      = $('#authority').val(); 
                    d.active = $('#active').val(); 
                    d.effective_date   = $('#effective_date').val(); 
                    d.end_effective   = $('#end_effective').val(); 
                }
            },
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false}, 
                { data : 'nik' ,             name :  'nik'           },
                { data : 'user' ,             name :  'user'           },
                { data : 'role' ,      name :  'role'    },
                { data : 'authority' ,                   name :  'authority'                 },
                { data : 'active' ,                  name :  'active'                },
                { data : 'start_effective' ,         name :  'start_effective'       },
                { data : 'end_effective' ,           name :  'end_effective'         },
            ],
            columnDefs: [
                { width: '5%', targets: 0 },
                { width: '10%', targets: 1 },
                { width: '15%', targets: 2 },
                { width: '10%', targets: 3 },
                { width: '7%', targets: 4 },
                { width: '8%', targets: 5 },
                { width: '10%', targets: 6 },
                { width: '10%', targets: 7 },
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
        location.replace('{{ url('masters/autorisation/import-excel') }}');
    });

    function deleteItem(id) {
        $('#form_result').html('');

        artLoadingDialogDo("Please wait, we process your request..",function(){
            $.ajax({
                url : '{!! route('masters/autorisation/delete-data') !!}',
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
        location.replace('{{ url('masters/autorisation/detail-data') }}/' + id);
    }

    function editItem(id) {
        location.replace('{{ url('masters/autorisation/edit-data') }}/' + id);
    }
</script>
@endsection


