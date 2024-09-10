@extends('layouts.layout')

@section('auth')
    <h4 class="pull-left page-title">Laporan Data Transaksi</h4>
    <ol class="breadcrumb pull-right">
        <li><a href="#">{{Auth::user()->name}}</a></li>
        <li class="active">Laporan Data Transaksi</li>
    </ol>
    <div class="clearfix"></div>
@endsection

@section('content')
    <div class="container">
        <div class="card-header">
            {{--            <div class="alert alert-danger" {{$hidden_status}}>{{$return_msg}}</div>--}}
            <div class="btn-group" role="group">
                <div class="form-group">
                    <button title="show/hide data filter options" type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#main-table-data-filter" aria-expanded="false" aria-controls="main-table-data-filter">{{ucfirst(__('data filter'))}}..</button>
                    {{--                    @if($access)--}}
                    <button type="button" name="download" id="download" class="btn btn-secondary" onclick="location.replace('{{ route('report.export') }}');">
                        <i class="fa fa-fw fa-file-excel-o"></i> {{ucwords(__('Download'))}}
                    </button>
                    {{--                    @endif--}}
                </div>
            </div>
        </div>

        <div class="collapse" id="main-table-data-filter">
            <div class="card card-body">
                <form method="POST" id="search-form" class="form" role="form">
                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="col-md-12">
                                {{-- NOMOR WORK ORDER --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="wo_number">NOMOR WORK ORDER</label>
                                    <div class="col-md-6">
                                        <select title="WO Number" id="wo_number" class="form-control">
                                            <option selected value=""></option>
                                            @foreach($woNumber as $wo)
                                                <option value="{{$wo}}">{{$wo}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- NOMOR SPK --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="spk_number">NOMOR SPK</label>
                                    <div class="col-md-6">
                                        <select title="SPK Number" id="spk_number" class="form-control">
                                            <option selected value=""></option>
                                            @foreach($spkNumber as $spk)
                                                <option value="{{$spk}}">{{$spk}}</option>
                                            @endforeach
                                        </select>                                    </div>
                                </div>

                                {{-- TANGGAL EFEKTIF --}}
                                <div class="col-md-6">
                                    <label class="col-md-1" for="effective_date">TANGGAL EFEKTIF</label>
                                    <div class="col-md-1"></div>
                                    <label class="col-md-1" for="effective_date_start">DARI</label>
                                    <div class="col-md-4">
                                        <input name="effective_date_start" id='effective_date_start' type="text" class="form-control doStartDate" readonly value="">
                                    </div>
                                    <label class="col-md-1" for="effective_date_end">KE</label>
                                    <div class="col-md-4">
                                        <input name="effective_date_end" id='effective_date_end' type="text" class="form-control doEndDate" readonly value="">
                                    </div>
                                </div>

                                {{-- KATEGORI WORK ORDER --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="wo_category">KATEGORI WORK ORDER</label>
                                    <div class="col-md-6">
                                        <select title="WO Category" id="wo_category" name="wo_category" class="form-control">
                                            <option selected value=""></option>
                                            @foreach($woCategory as $woCategory)
                                                <option value="{{$woCategory}}">{{$woCategory}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- KATEGORI PEKERJAAN --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="job_category">KATEGORI PEKERJAAN</label>
                                    <div class="col-md-6">
                                        <select title="Job Category" id="job_category" name="job_category" class="form-control">
                                            <option value="" selected></option>
                                        </select>
                                    </div>
                                </div>

                                {{-- DEPARTMENT --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="department">DEPARTMENT</label>
                                    <div class="col-md-6">
                                        <select title="DEPARTMENT" id="department" name="department" class="form-control">
                                            <option selected value=""></option>
                                            @if (!empty($department) && is_array($department))
                                                @foreach ($department as $dept)
                                                    <option value="{{ $dept['department_code'] }}">
                                                        {{ $dept['department_code'] }} - {{ $dept['department'] }}
                                                    </option>
                                                @endforeach
                                            @else
{{--                                                <option value="">No departments found</option>--}}
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{-- LOKASI --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="location">LOKASI</label>
                                    <div class="col-md-6">
                                        <select title="LOKASI" id="location" name="location" class="form-control">
                                            <option selected value=""></option>
                                            @if (!empty($location) && is_array($location))
                                                @foreach ($location as $loc)
                                                    <option value="{{ $loc['location'] }}">
                                                        {{ $loc['location'] }} - {{ $loc['location_type'] }}
                                                    </option>
                                                @endforeach
                                            @else
{{--                                                <option value="">No locations found</option>--}}
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{-- STATUS WORK ORDER --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="wo_status">STATUS WORK ORDER</label>
                                    <div class="col-md-6">
                                        <select title="WO Status" id="wo_status" name="wo_status" class="form-control">
                                            <option selected value=""></option>
                                            @foreach($workOrderStatus as $workOrderStatus)
                                                <option value="{{$workOrderStatus}}">{{$workOrderStatus}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- STATUS ENGINEER --}}
                                <div class="col-md-6">
                                    <label class="col-md-2" for="engineer_status">STATUS ENGINEER</label>
                                    <div class="col-md-6">
                                        <select title="Engineering Status" id="engineer_status" name="engineer_status" class="form-control">
                                            <option selected value=""></option>
                                            @foreach($engineerStatus as $engineerStatus)
                                                <option value="{{$engineerStatus}}">{{$engineerStatus}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

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
                        <h3 class="panel-title">Data Transaksi</h3>
                    </div>
                    <div class="panel-body">
                        <table id="main-table" class="table table-striped table-bordered " cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Action</th>
                                <th>Nomor WO</th>
                                <th>WO Kategori</th>
                                <th>Nomor SPK</th>
                                <th>Departemen</th>
                                <th>Kategori Pekerjaan</th>
                                <th>Status</th>
                                <th>Disetujui Oleh</th>
                                <th>Disetujui Pada</th>
                                <th>Pelapor</th>
                                <th>Tanggal Efektif</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- End Row -->
    </div>

    {{--    --}}{{--Link CSS--}}
    {{--    <!-- Datetimepicker -->--}}
    {{--    <link href="{{ asset('css/datetimepicker/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" type="text/css" >--}}
    {{--    <!-- selectize -->--}}
    {{--    <link href="{!! asset('css/selectize/selectize.bootstrap3.css') !!}"  media="all" rel="stylesheet" type="text/css" />--}}

    {{--    <script type='text/javascript' src="{{ asset('js/datetimepicker/moment.min.js') }}"></script>--}}
    {{--    <script type='text/javascript' src="{{ asset('js/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>--}}
    {{--    <script type='text/javascript' src="{{ asset('js/datetimepicker/id.js') }}"></script>--}}
    <!-- Plugins js -->
    <script src="{{ asset('plugins/timepicker/bootstrap-timepicker.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript') }}"></script>
    <script src="{{ asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js" type="text/javascript') }}"></script>
    <script src="{{ asset('pages/form-advanced.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.doStartDate').datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: 'TRUE',
                autoclose: true,
            });

            $('.doEndDate').datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: 'TRUE',
                autoclose: true,
            });


        });

        $(function() {
            var oTable = $('#main-table').DataTable({
                filter: false,
                processing: true,
                serverSide: true,
                // deferLoading: 0, //disable auto load
                stateSave: false,
                // scrollY: 500,
                // scrollX: true,
                ordering: false,
                language: {
                    paginate: {
                        first: "<i class='fa fa-step-backward'></i>",
                        last: "<i class='fa fa-step-forward'></i>",
                        next: "<i class='fa fa-caret-right'></i>",
                        previous: "<i class='fa fa-caret-left'></i>"
                    },
                    lengthMenu: "<div class=\"input-group\">_MENU_ &nbsp; / page</div>",
                    info: "_START_ to _END_ of _TOTAL_ item(s)",
                    infoEmpty: ""
                },
                ajax: {
                    'url': "{!! route('reports.getDataTable') !!}",
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    'data': function(d) {
                        d.wo_number = $('#wo_number').val();
                        d.spk_number = $('#spk_number').val();
                        d.effective_date_start = $('#effective_date_start').val();
                        d.effective_date_end = $('#effective_date_end').val();
                        d.wo_category = $('#wo_category').val();
                        d.job_category = $('#job_category').val();
                        d.department = $('#department').val();
                        d.location = $('#location').val();
                        d.wo_status = $('#wo_status').val();
                        d.engineer_status = $('#engineer_status').val();
                    }
                },
                columns: [{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                    {
                        data: 'wo_number',
                        name: 'wo_number'
                    },
                    {
                        data: 'wo_category',
                        name: 'wo_category'
                    },
                    {
                        data: 'spk_number',
                        name: 'spk_number'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'job_category',
                        name: 'job_category'
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'approve_by',
                        name: 'approve_by',
                    },
                    {
                        data: 'approve_at',
                        name: 'approve_at',
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                    },
                    {
                        data: 'effective_date',
                        name: 'effective_date',
                    },
                ],
                // order: [[ 2, "desc" ]],
                rowCallback: function(row, data, iDisplayIndex) {
                    var api = this.api();
                    var info = api.page.info();
                    var page = info.page;
                    var length = info.length;
                    var index = (page * length + (iDisplayIndex + 1));
                    //    $('td:eq(1)', row).html(index);
                },
            });

            $('#search-form').on('submit', function(e) {
                oTable.draw();
                e.preventDefault();
            });
        });

        function showItem(id) {
            {
                var urlcek = "{{route('report.cekdetail', '')}}" + "/" + id;
                var urldet = "{{route('report.detail', '')}}" + "/" + id;
                $.ajax({
                    url: urlcek,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    data: id,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.errors) {
                            $('#form_result').html(data.message);
                            setTimeout(function() {
                                $('#form_result').html('');
                            }, 4000);
                        }
                        if (data.success) {
                            window.location.href = urldet;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error Status:', status);
                        console.log('Error:', error);
                        console.log('Response:', xhr.responseText);
                        var html = '<div class="alert alert-danger">Terjadi kesalahan : ' + error + '</div>';
                        $('#form_result').html(html);
                    }
                });
                // window.location.href = url;
            }
        }

        {{--function showItem(id) {--}}
        {{--    {--}}
        {{--        var url = "{{route('form-input.approval.detail', '')}}" + "/" + id;--}}
        {{--        window.location.href = url;--}}
        {{--    }--}}
        {{--}--}}

        {{--function downloadItem(id) {--}}
        {{--    {--}}
        {{--        var url = "{{route('form-input.approval.download', '')}}" + "/" + id;--}}
        {{--        window.open(url, '_blank');--}}
        {{--    }--}}
        {{--}--}}

        {{--// var uri = encodeURI("{{url('masters/department/export-excel')}}");--}}
    </script>
@endsection
