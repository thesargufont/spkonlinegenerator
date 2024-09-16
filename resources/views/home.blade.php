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
                                <div class="chart-container">
                                    <canvas id="gangguanChart" style="height: 300px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-border panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">DASHBOARD PEKERJAAN</h3>
                            </div>
                            <div class="panel-body">
                                <div class="chart-container">
                                    <canvas id="pekerjaanChart" style="height: 300px"></canvas>
                                </div>
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
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/0.7.0/chartjs-plugin-datalabels.min.js"></script>
    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/1.0.0/chartjs-plugin-datalabels.min.js" integrity="sha512-XulchVN83YTvsOaBGjLeApZuasKd8F4ZZ28/aMHevKjzrrjG0lor+T4VU248fWYMNki3Eimk+uwdlQS+uZmu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}

    <style>
        .chart-container {
            height: 300px; /* Adjust based on your needs */
            overflow-y: scroll;
            overflow-x: hidden; /* Optional: Hide horizontal overflow if not needed */
        }
        /*.chart-container {*/
        /*    height: 500px; !* Adjust based on your needs *!*/
        /*    width: 100%; !* Ensure the width is set properly *!*/
        /*    overflow: auto; !* Allows both horizontal and vertical scrolling *!*/
        /*}*/
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
            // $('#total_laporan').val('asdasd');
        });

        // Predefined colors starting with red, green, blue, yellow, etc.
        const predefinedColors = [
            "#ff0000", // Red
            "#00ff00", // Green
            "#0000ff", // Blue
            "#ffff00", // Yellow
            "#ff00ff", // Magenta
            "#00ffff"  // Cyan
        ];

        // const predefinedColorsStatus = [
        //     "#31ec23",
        //     "#fa0000",
        //     "#000000",
        //     "#0004ff",
        //     "#a100ff",
        // ]

        const predefinedColorsStatus = [
            "#31ec23", // Bright green
            "#fa0000", // Red
            "#000000", // Black
            "#0004ff", // Blue
            "#a100ff", // Purple
        ];

        const pastelColorsStatus = predefinedColorsStatus.map((color) => {
            const lighten = (hex, percent) => {
                // Convert the hex color to RGB
                const num = parseInt(hex.slice(1), 16);
                let r = (num >> 16) + Math.round((255 - (num >> 16)) * percent / 100);
                let g = ((num >> 8) & 0x00ff) + Math.round((255 - ((num >> 8) & 0x00ff)) * percent / 100);
                let b = (num & 0x0000ff) + Math.round((255 - (num & 0x0000ff)) * percent / 100);

                r = r < 255 ? r : 255;
                g = g < 255 ? g : 255;
                b = b < 255 ? b : 255;

                // Return the new color as a hex code
                return `#${((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)}`;
            };

            // Lighten the color by 60% for a pastel effect
            return lighten(color, 30);
        });

        const pastelColors = predefinedColors.map((color) => {
            const lighten = (hex, percent) => {
                // Convert the hex color to RGB
                const num = parseInt(hex.slice(1), 16);
                let r = (num >> 16) + Math.round((255 - (num >> 16)) * percent / 100);
                let g = ((num >> 8) & 0x00ff) + Math.round((255 - ((num >> 8) & 0x00ff)) * percent / 100);
                let b = (num & 0x0000ff) + Math.round((255 - (num & 0x0000ff)) * percent / 100);

                r = r < 255 ? r : 255;
                g = g < 255 ? g : 255;
                b = b < 255 ? b : 255;

                // Return the new color as a hex code
                return `#${((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)}`;
            };

            // Lighten the color by 60% for a pastel effect
            return lighten(color, 30);
        });

        // Function to generate random colors in hex format
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        function getRandomPastelColor() {
            const base = 255; // Maximum value for pastel
            const variation = 127; // Random value range to add to each color component

            // Generate pastel-like color by keeping the values in the higher range
            const r = Math.floor((Math.random() * variation) + (base - variation)).toString(16).padStart(2, '0');
            const g = Math.floor((Math.random() * variation) + (base - variation)).toString(16).padStart(2, '0');
            const b = Math.floor((Math.random() * variation) + (base - variation)).toString(16).padStart(2, '0');

            return `#${r}${g}${b}`;
        }

        var statusCounts = @json($dataDashboardStatus['statusCounts']);
        var xValuesStatus = Object.keys(statusCounts);
        var yValuesStatus = Object.values(statusCounts);

        // var xValuesStatus = ["OUTS APPROVE", "ONGOING", "DONE", "NOT APPROVED","CANCEL"];
        {{--var xValuesStatus = ["NOT APPROVED", "ONGOING", "DONE", "CLOSED","CANCEL"];--}}
        {{--var yValuesStatus = ['{{ $dataDashboardStatus['statusNotApprove'] }}', '{{ $dataDashboardStatus['statusOnGoing'] }}', '{{ $dataDashboardStatus['statusDone'] }}', '{{ $dataDashboardStatus['statusClosed'] }}', '{{ $dataDashboardStatus['statusCancel'] }}'];--}}
        // var barColorsStatus = [
        //     // "#ff4400",
        //     "#31ec23",
        //     "#fa0000",
        //     "#000000",
        //     "#0004ff",
        //     "#a100ff",
        // ];

        var barColorsStatus = [];
        for (let i = 0; i < xValuesStatus.length; i++) {
            if (i < pastelColorsStatus.length) {
                // Use predefined color
                barColorsStatus.push(pastelColorsStatus[i]);
            } else {
                // Generate random color for remaining entries
                barColorsStatus.push(getRandomPastelColor());
            }
        }

        var inputCounts = @json($dataDashboardInput['inputCounts']);
        var xValuesInput = Object.keys(inputCounts);
        var yValuesInput = Object.values(inputCounts);

        {{--var xValuesInput = ["LAPORAN GANGGUAN", "PEKERJAAN"];--}}
        {{--var yValuesInput = ['{{ $dataDashboardInput['inputGangguan'] }}', '{{ $dataDashboardInput['inputPekerjaan'] }}'];--}}
        var barColorsInput = [
            "#ff4400",
            "#31ec23",
        ];

        const pastelInputColors = barColorsInput.map((color) => {
            const lighten = (hex, percent) => {
                // Convert the hex color to RGB
                const num = parseInt(hex.slice(1), 16);
                let r = (num >> 16) + Math.round((255 - (num >> 16)) * percent / 100);
                let g = ((num >> 8) & 0x00ff) + Math.round((255 - ((num >> 8) & 0x00ff)) * percent / 100);
                let b = (num & 0x0000ff) + Math.round((255 - (num & 0x0000ff)) * percent / 100);

                r = r < 255 ? r : 255;
                g = g < 255 ? g : 255;
                b = b < 255 ? b : 255;

                // Return the new color as a hex code
                return `#${((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1)}`;
            };

            // Lighten the color by 60% for a pastel effect
            return lighten(color, 30);
        });

        var gangguanCounts = @json($dataDashboardGangguan['gangguanCounts']);
        var xValuesGangguan = Object.keys(gangguanCounts);
        var yValuesGangguan = Object.values(gangguanCounts);

        {{--var xValuesGangguan = ["COMUNICATION DOWN", "SEND ALARM", "SDH MAJOR ALARM", "SDH FO CUT"];--}}
        {{--var yValuesGangguan = ['{{ $dataDashboardGangguan['COMUNICATION DOWN'] }}', '{{ $dataDashboardGangguan['SEND ALARM'] }}', '{{ $dataDashboardGangguan['SDH MAJOR ALARM'] }}', '{{ $dataDashboardGangguan['SDH FO CUT'] }}'];--}}
        var barColorsGangguan = [];

        for (let i = 0; i < xValuesGangguan.length; i++) {
            if (i < pastelColors.length) {
                // Use predefined color
                barColorsGangguan.push(pastelColors[i]);
            } else {
                // Generate random color for remaining entries
                barColorsGangguan.push(getRandomPastelColor());
            }
        }

        var jobCounts = @json($dataDashboardPekerjaan['jobCounts']);
        var xValuesPekerjaan = Object.keys(jobCounts);
        var yValuesPekerjaan = Object.values(jobCounts);

        {{--var xValuesPekerjaan = ["PEMASANGAN", "SURVEY", "RESETTING", "COMMISIONING", "INVESTIGASI", "SUPERVISI"];--}}
        {{--var yValuesPekerjaan = ['{{ $dataDashboardPekerjaan['pemasangan'] }}', '{{ $dataDashboardPekerjaan['survey'] }}', '{{ $dataDashboardPekerjaan['resetting'] }}', '{{ $dataDashboardPekerjaan['commisioning'] }}', '{{ $dataDashboardPekerjaan['investigasi'] }}', '{{ $dataDashboardPekerjaan['supervisi'] }}'];--}}

        // Dynamically set bar colors with predefined colors and random ones if needed
        var barColorsPekerjaan = [];
        for (let i = 0; i < xValuesPekerjaan.length; i++) {
            if (i < pastelColors.length) {
                // Use predefined color
                barColorsPekerjaan.push(pastelColors[i]);
            } else {
                // Generate random color for remaining entries
                barColorsPekerjaan.push(getRandomPastelColor());
            }
        }

        new Chart("statusChart", {
            type: "pie",
            data: {
                labels: xValuesStatus,
                datasets: [{
                    backgroundColor: barColorsStatus,
                    data: yValuesStatus
                }]
            },
            options: {
                scaleShowLabels: false,
                title: {
                    display: true,
                    text: "STATUS"
                },
                legend: {
                    position: 'left',
                    usePointStyle: true,
                    padding: 1,
                    labels: {
                        boxWidth: 15
                    }
                },
                responsive: true,
                plugins: {
                    datalabels: {
                        color: 'white',
                        display: true,
                        align: 'center', // Align labels to the center of the pie slices
                        anchor: 'center', // Anchor labels to the center of the pie slices
                        font: {
                            weight: 'bold' // Make the labels bold
                        },
                        formatter: function(value, context) {
                            const total = {{ $dataDashboardStatus['totalData'] }};

                            let percentage = (value / total) * 100;

                            // Check if the percentage is less than 6 and return an empty string if so
                            if (percentage < 7) {
                                return "";
                            }

                            percentage = percentage.toFixed(0) + "%";

                            return percentage;
                        }
                    }
                }
            }
        });

        new Chart("inputChart", {
            type: "pie",
            data: {
                labels: xValuesInput,
                datasets: [{
                    backgroundColor: pastelInputColors,
                    data: yValuesInput
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "KATEGORI WORK ORDER"
                },
                legend: {
                    position: 'left',
                },
                responsive: true,
                plugins: {
                    datalabels: {
                        color: 'white',
                        display: true,
                        align: 'center', // Align labels to the center of the pie slices
                        anchor: 'center', // Anchor labels to the center of the pie slices
                        font: {
                            weight: 'bold' // Make the labels bold
                        },
                        formatter: function(value, context) {
                            const total = {{ $dataDashboardInput['totalData'] }};

                            let percentage = (value / total) * 100;

                            // Check if the percentage is less than 6 and return an empty string if so
                            if (percentage < 5) {
                                return "";
                            }

                            percentage = percentage.toFixed(0) + "%";

                            return percentage;
                        }
                    }
                }
            }
        });

        new Chart("gangguanChart", {
            type: "horizontalBar", // Use 'horizontalBar' for horizontal bars
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
                },
                legend: {
                    display: false, // Disable if you don’t need a legend for a single dataset
                    position: 'right' // Legend position if needed
                },
                responsive: true, // Makes the chart responsive,
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                },
                maintainAspectRatio: false, // Allows the chart to be resized without maintaining aspect ratio
                plugins: {
                    datalabels: {
                        color: 'white',
                        display: true,
                        font: {
                            weight: 'bold' // Make the labels bold
                        },
                        formatter: function(value, context) {
                            return context.chart.data.datasets[context.data];
                        }
                    }
                }
            }
        });

        new Chart("pekerjaanChart", {
            type: "horizontalBar", // Use 'horizontalBar' for horizontal bars
            data: {
                labels: xValuesPekerjaan,
                datasets: [{
                    backgroundColor: barColorsPekerjaan,
                    data: yValuesPekerjaan,
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'KATEGORI PEKERJAAN' // Chart title
                },
                legend: {
                    display: false, // Disable if you don’t need a legend for a single dataset
                    position: 'right' // Legend position if needed
                },
                responsive: true, // Makes the chart responsive
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }]
                },
                maintainAspectRatio: false, // Allows the chart to be resized without maintaining aspect ratio
                plugins: {
                    datalabels: {
                        color: 'white',
                        display: true,
                        font: {
                            weight: 'bold' // Make the labels bold
                        },
                        formatter: function(value, context) {
                            return context.chart.data.datasets[context.data];
                        }
                    }
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
