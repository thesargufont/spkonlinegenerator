@extends('layouts.layout')

@section('auth')
{{--    <h4 class="pull-left page-title">Dashboard</h4>--}}
{{--    <ol class="breadcrumb pull-right">--}}
{{--        <li><a href="#">{{Auth::user()->name}}</a></li>--}}
{{--        <li class="active">Dashboard</li>--}}
{{--    </ol>--}}
{{--    <div class="clearfix"></div>--}}
@endsection
@section('content')
    <div class="container" style="overflow:scroll; height:100%;">
        <div class="row">
            <div class="col-sm-3">
                <form method="POST" id="search-form" class="form" role="form">
                    <div class="row">
                        <div class="col-sm-15">
                            <div class="panel panel-primary text-center larger-panel">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Total Laporan</h4>
                                </div>
                                <div class="panel-body">
                                    <h3 class="">
                                        <b><p class="text-muted"><b></b></p>{{ $totalReport }}</b>
                                    </h3>
                                    <p class="text-muted"><b></b>Laporan periode 2024</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-15">
                            <div class="panel panel-primary text-center larger-panel">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Total Laporan Gangguan</h4>
                                </div>
                                <div class="panel-body">
                                    <h3 class=""><b>{{ $totalReportProblem }}</b></h3>
                                    <p class="text-muted"><b>{{ $problemPercentage }}%</b> Total laporan</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-15">
                            <div class="panel panel-primary text-center larger-panel">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Total Pekerjaan</h4>
                                </div>
                                <div class="panel-body">
                                    <h3 class=""><b>{{ $totalReportJob }}</b></h3>
                                    <p class="text-muted"><b>{{ $jobPercentage }}%</b> Total laporan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-sm-9">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel panel-border panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">DASHBOARD STATUS</h3>
                            </div>
                            <div class="panel-body">
                                <canvas id="statusChart" style="height: 300px"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-border panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">DASHBOARD INPUT</h3>
                            </div>
                            <div class="panel-body">
                                <canvas id="inputChart" style="height: 300px"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-border panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">DASHBOARD GANGGUAN</h3>
                            </div>
                            <div class="panel-body">
                                <canvas id="gangguanChart" style="height: 300px"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-border panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">DASHBOARD PEKERJAAN</h3>
                            </div>
                            <div class="panel-body">
                                <canvas id="pekerjaanChart" style="height: 300px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

{{--        <div class="row">--}}
{{--            <div class="col-md-12">--}}
{{--                <div class="panel panel-primary">--}}
{{--                    <div class="panel-heading">--}}
{{--                        <h3 class="panel-title">Data Pekerjaan</h3>--}}
{{--                    </div>--}}
{{--                    <div class="panel-body">--}}
{{--                        <span id="form_result"></span>--}}
{{--                        <table  id="main-table" class="table table-striped table-bordered dt-responsive" cellspacing="0" width="100%">--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>Nomor WO</th>--}}
{{--                                <th>Tipe WO</th>--}}
{{--                                <th>Nomor SPK</th>--}}
{{--                                <th>Nomor WP</th>--}}
{{--                                <th>Kategori Pekerjaan</th>--}}
{{--                                <th>Departemen</th>--}}
{{--                                <th>Status</th>--}}
{{--                                <th>Tanggal Effective</th>--}}
{{--                                <th>Dibuat Oleh</th>--}}
{{--                                <th>Dibuat Pada</th>--}}
{{--                                <th>Diubah Oleh</th>--}}
{{--                                <th>Diubah Pada</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                        </table>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>

    <link rel="stylesheet" href="{{ asset ('plugins/morris/morris.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <style>
        .larger-panel {
            min-height: 225px; /* Increase height */
            padding: 10px; /* Add more padding */
        }

        .larger-panel .panel-body {
            padding: 30px 0; /* More padding inside body */
        }
    </style>

    <script>
        $(document).ready(function () {
            $('#total_laporan').val('asdasd');
        });

        var xValues = ["NOT APPROVE", "ONGOING", "DONE", "CLOSED", "CANCEL"];
        var yValues = ['{{ $problemTlkm }}', '{{ $problemScd }}', '{{ $problemPsis }}', '{{ $problemUpt }}', '{{ $problemDspc }}'];
        var barColors = [
            "#b91d47",
            "#00aba9",
            "#2b5797",
            "#e8c3b9",
            "#1e7145"
        ];

        var xValuesStatus = ["OUTS APPROVE", "ONGOING", "DONE", "NOT APPROVED", "CANCEL"];
        var yValuesStatus = ['{{ $dataDashboardStatus['statusNotApprove'] }}', '{{ $dataDashboardStatus['statusOnGoing'] }}', '{{ $dataDashboardStatus['statusDone'] }}', '{{ $dataDashboardStatus['statusClosed'] }}', '{{ $dataDashboardStatus['statusCancel'] }}'];
        var barColorsStatus = [
            "#ff4400",
            "#0004ff",
            "#31ec23",
            "#000000",
            "#fa0000"
        ];

        var xValuesInput = ["LAPORAN GANGGUAN", "PEKERJAAN"];
        var yValuesInput = ['{{ $dataDashboardInput['inputGangguan'] }}', '{{ $dataDashboardInput['inputPekerjaan'] }}'];
        var barColorsInput = [
            "#ff4400",
            "#0004ff",
        ];

        var xValuesGangguan = ["COMUNICATION DOWN", "SEND ALARM", "SDH MAJOR ALARM", "SDH FO CUT"];
        var yValuesGangguan = ['{{ $dataDashboardGangguan['COMUNICATION DOWN'] }}', '{{ $dataDashboardGangguan['SEND ALARM'] }}', '{{ $dataDashboardGangguan['SDH MAJOR ALARM'] }}', '{{ $dataDashboardGangguan['SDH FO CUT'] }}'];
        var barColorsGangguan = [
            "#ff4400",
            "#008aee",
            "#00f821",
            "#ffc300",
            "#a100ff",
        ];

        var xValuesPekerjaan = ["PEMASANGAN", "SURVEY", "RESETTING", "COMMISIONING", "INVESTIGASI", "SUPERVISI"];
        var yValuesPekerjaan = ['{{ $dataDashboardPekerjaan['pemasangan'] }}', '{{ $dataDashboardPekerjaan['survey'] }}', '{{ $dataDashboardPekerjaan['resetting'] }}', '{{ $dataDashboardPekerjaan['commisioning'] }}', '{{ $dataDashboardPekerjaan['investigasi'] }}', '{{ $dataDashboardPekerjaan['supervisi'] }}'];
        var barColorsPekerjaan = [
            "#ff4400",
            "#008aee",
            "#00f821",
            "#ffc300",
            "#a100ff",
            "#00fcc0",
        ];

        new Chart("statusChart", {
            type: "doughnut",
            data: {
                labels: xValuesStatus,
                datasets: [{
                    backgroundColor: barColorsStatus,
                    data: yValuesStatus
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "STATUS"
                }
            }
        });

        var xValues1 = ["TELEKOMUNIKASI", "SCADA", "PROSIS", "UPT", "DISPATCHER"];
        var yValues1 = ['{{ $jobTlkm }}', '{{ $jobScd }}', '{{ $jobPsis }}', '{{ $jobUpt }}', '{{ $jobDspc }}'];
        var barColors1 = [
            "#b91d47",
            "#00aba9",
            "#2b5797",
            "#e8c3b9",
            "#1e7145"
        ];

        new Chart("inputChart", {
            type: "doughnut",
            data: {
                labels: xValuesInput,
                datasets: [{
                    backgroundColor: barColorsInput,
                    data: yValuesInput
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "KATEGORI WORK ORDER"
                }
            }
        });

        new Chart("gangguanChart", {
            type: "doughnut",
            data: {
                labels: xValuesGangguan,
                datasets: [{
                    backgroundColor: barColorsGangguan,
                    data: yValuesGangguan
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "KATEGORI GANGGUAN"
                }
            }
        });

        new Chart("pekerjaanChart", {
            type: "doughnut",
            data: {
                labels: xValuesPekerjaan,
                datasets: [{
                    backgroundColor: barColorsPekerjaan,
                    data: yValuesPekerjaan
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "KATEGORI PEKERJAAN"
                }
            }
        });

        $(function() {
            var oTable = $('#main-table').DataTable({
                filter: false,
                processing: true,
                serverSide: true,
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
                    'url': '{!! route('dashboard.dashboard-data') !!}',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{!! csrf_token() !!}'
                    },
                    'data': function (d) {
                        d.location_name = $('#location_name').val();
                    }
                },
                columns: [
                    { data : 'wo_number' ,       name :  'wo_number'        },
                    { data : 'wo_category' ,     name :  'wo_category'      },
                    { data : 'spk_number' ,      name :  'spk_number'       },
                    // { data : 'wp_number' ,       name :  'wp_number'        },
                    { data : 'job_category' ,    name :  'job_category'     },
                    { data : 'department' ,      name :  'department'       },
                    { data : 'status' ,          name :  'status'           },
                    { data : 'effective_date' ,  name :  'effective_date'   },
                    { data : 'created_by' ,      name :  'created_by',      },
                    { data : 'created_at' ,      name :  'created_at',      },
                    { data : 'updated_by' ,      name :  'updated_by',      },
                    { data : 'updated_at' ,      name :  'updated_at',      },
                ],
                rowCallback: function( row, data, iDisplayIndex ) {
                    var api = this.api();
                    var info = api.page.info();
                    var page = info.page;
                    var length = info.length;
                    var index = (page * length + (iDisplayIndex +1));
                },
            });

            $('#search-form').on('submit', function(e) {
                oTable.draw();
                e.preventDefault();
            });
        });
    </script>

@endsection
