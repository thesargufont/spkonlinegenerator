<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>SPONGE - Surat Perintah Kerja Online Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="Admin Dashboard" name="description" />
    <meta content="ThemeDesign" name="author" />
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge" /> -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="shortcut icon" href="{{ asset('images/pln_logo.png') }}">

    <!-- DataTables -->
    <link href="{{ asset('plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">


    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="fixed-left">
<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">
        <!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center">
                <a href="{{ asset('home') }}" class="logo"><img src="{{ asset('images/sponge_logo.png') }}" height="28"></a>
                <a href="{{ asset('home') }}" class="logo-sm"><img src="{{ asset('images/pln_logo.png') }}" height="36"></a>
            </div>
        </div>
        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="">
                    <div class="pull-left">
                        <button type="button" class="button-menu-mobile open-left waves-effect waves-light">
                            <i class="ion-navicon"></i>
                        </button>
                        <span class="clearfix"></span>
                    </div>
                    <form class="navbar-form pull-left" role="search">
                        <div class="form-group">
                            {{-- <input type="text" class="form-control search-bar" placeholder="Search..."> --}}
                        </div>
                        <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                    </form>

                    <ul class="nav navbar-nav navbar-right pull-right">
                        <li class="dropdown hidden-xs">
                            <a href="{{ route('notifications') }}" class="waves-effect waves-light">
                                <i class="fa fa-bell"></i><span class="badge badge-xs badge-danger" id="span_notif"></span>
                            </a>
                        </li>
                        <li class="hidden-xs">
                            <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="fa fa-crosshairs"></i></a>
                        </li>

                        <li class="hidden-xs">
                            <a href="{{ route('profile-user') }}" id="btn-profile" class="waves-effect waves-light"><i class="fa fa-user"></i></a>
                        </li>

                        <li class="hidden-xs">
                            <a href="{{ route('actionlogout') }}" id="btn-logout" class="waves-effect waves-light"><i class="fa fa-sign-out"></i></a>
                        </li>

                        <li class="dropdown">
                            {{-- <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><img src="{{ asset('images/users/avatar-1.jpg') }}"
                            alt="user-img" class="img-circle"> </a> --}}
                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)"> Profile</a></li>
                                <li><a href="javascript:void(0)"><span class="badge badge-success pull-right">5</span> Settings </a></li>
                                <li><a href="javascript:void(0)"> Lock screen</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)"> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>
    </div>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->

    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">
            <div class="user-details">
                <div class="text-center">
                    {{-- <img src="{{ asset('images/users/avatar-1.jpg') }}" alt="" class="img-circle"> --}}
                </div>
                <div class="user-info">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{Auth::user()->name}}</a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)"> Profile</a></li>
                            <li><a href="javascript:void(0)"> Settings</a></li>
                            <li><a href="javascript:void(0)"> Lock screen</a></li>
                            <li class="divider"></li>
                            <li><a href="javascript:void(0)"> Logout</a></li>
                        </ul>
                    </div>

                    <p class="text-muted m-0"><i class="fa fa-dot-circle-o text-success"></i> Online</p>
                </div>
            </div>
            <!--- Divider -->
            <div id="sidebar-menu">
                <ul>
                    <li>
                        <a href="{{ route('home') }}" class="waves-effect"><i class="ti-home"></i><span> Dashboard
                                </span></a>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-write"></i><span> Forms
                                </span><span class="pull-right"><i class="mdi mdi-plus"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('form-input.working-order.index') }}">Pelaporan</a></li>
                            <li><a href="{{ route('form-input.approval.index') }}">Approval</a></li>
                            <li><a href="{{ route('form-input.engineer.index') }}">Engineer</a></li>
                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" href="{{ route('reports.index') }}" class="waves-effect"><i class="ti-write"></i><span> Laporan
                                </span><span class="pull-right"><i class="mdi mdi-plus"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('reports.index') }}">Data Transaksi</a></li>
                        </ul>
                    </li>

                    <!-- <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-agenda"></i> <span> Laporan
                            </span> <span class="pull-right"><i class="mdi mdi-plus"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="#">Working Order</a></li>
                            <li><a href="#">Surat Perintah Kerja</a></li>
                            <li><a href="#">Berita Acara</a></li>
                        </ul>
                    </li> -->

                    <li>
                        <a class="waves-effect"><i class="ti-calendar"></i><span> Schedule <span class="badge badge-primary pull-right">NEW</span></span></a>
                    </li>

                    <li id="master_nav_non_editable" class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-files"></i><span> Master
                                    Data </span><span class="pull-right"><i class="mdi mdi-plus"></i></span></a>
                    </li>
                    <li id="master_nav_editable" class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-files"></i><span> Master
                                    Data </span><span class="pull-right"><i class="mdi mdi-plus"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('masters/employee/index') }}">Data Pengguna</a></li>
                            <li><a href="{{ route('masters/location/index') }}">Data Lokasi</a></li>
                            <li><a href="{{ route('masters/basecamp/index') }}">Data Basecamp</a></li>
                            <li><a href="{{ route('masters/department/index') }}">Data Departemen</a></li>
                            <li><a href="{{ route('masters/job/index') }}">Data Pekerjaan</a></li>
                            <li><a href="{{ route('masters/device/index') }}">Data Peralatan</a></li>
                            <li><a href="{{ route('masters/device-category/index') }}">Data Kategori Peralatan</a>
                            </li>
                            <li><a href="{{ route('masters/autorisation/index') }}">Data Autorisasi</a>
                    		</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div> <!-- end sidebarinner -->
    </div>
    <!-- Left Sidebar End -->

    <!-- Start right Content here -->

    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">

                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-header-title">
                            @yield('auth')
                        </div>
                    </div>
                </div>
            </div> <!-- container -->
            @yield('content')
        </div> <!-- content -->
        <footer class="footer">
            SPONGE - Surat Perintah Online Generator PLN Ungaran
        </footer>
    </div>
    <!-- End Right content here -->
</div>
<!-- END wrapper -->


<!-- jQuery  -->

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/modernizr.min.js') }}"></script>
<script src="{{ asset('js/detect.js') }}"></script>
<script src="{{ asset('js/fastclick.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('js/jquery.blockUI.js') }}"></script>
<script src="{{ asset('js/waves.js') }}"></script>
<script src="{{ asset('js/wow.min.js') }}"></script>
<script src="{{ asset('js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('js/jquery.scrollTo.min.js') }}"></script>

<!-- <script src="{{ asset('js/app.js') }}"></script> -->
<script src="{{ asset('plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
@yield('script')

<!-- Datatables-->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap.min.js') }}"></script>

<script src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('pages/dashborad.js') }}"></script>
<script src="{{ asset('js/app2.js') }}"></script>
</body>

<body class="hold-transition sidebar-collapse">
<div class="wrapper">
    <!-- modal for loading dialog -->
    <div class="modal fade" id="divArtLoadingDialog" tabindex="-1" role="dialog" aria-labelledby="divArtLoadingDialogTitle" aria-hidden="true">
        <br>
        <br>
        <br>
        <br>
        <br>
        <div id="divArtLoadingDialogModal" class="modal-dialog modal-dialog-centered modal-dialog-lg" role="document">
            <div class="breadcrumb modal-content">
                <div id="artLoadingDialogBody" class="modal-body">
                    <div class="text-center">
                        <div class="loading-spinner text-primary" style="width: 5rem; height: 5rem;" role="status" title="spinner for unmeasurable processes">
                            <span class="sr-only">loading...</span>
                        </div>
                        <div id="artLoadingDialogText">please wait while we process your request..</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- modal for artemis confirmation dialog -->
        <div class="modal fade" id="divArtConfirmation" tabindex="-1" role="dialog" aria-labelledby="divArtConfirmationTitle" aria-hidden="true">
            <br>
            <br>
            <br>
            <br>
            <br>
            <div id="divArtConfirmationModal" class="modal-dialog modal-dialog-centered modal-dialog-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="artConfirmationTitle">Artemis</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ucwords(__('close'))}}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="artConfirmationBody" class="modal-body">
                        {{ucfirst(__('Are you sure to do this action?'))}}
                    </div>
                    <div class="modal-footer">
                        <button id="artConfirmationBtnOk" type="button" class="btn btn-secondary">{{ucwords(__('OK'))}}</button>
                        <button id="artConfirmationBtnCancel" type="button" class="btn btn-secondary" data-dismiss="modal">{{ucwords(__('cancel'))}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<style>
    .loading-spinner {
        width: 30px;
        height: 30px;
        border: 2px solid indigo;
        border-radius: 50%;
        border-top-color: #0001;
        display: inline-block;
        animation: loadingspinner .7s linear infinite;
    }


    .modal-container {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .modal-content {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 100%;
        padding: 20px;
        text-align: center;
    }

    button {
        border: none;
    }

    h2 {
        color: #515151;
    }

    .confirmation-message {
        margin-bottom: 20px;
    }

    .button-container {
        display: flex;
        justify-content: space-around;
    }

    .button {
        padding: 10px 20px;
        font-size: 16px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .cancel-button {
        background-color: #ccc;
        color: #535353;
    }

    .delete-button {
        background-color: #e74c3c;
        color: #fff;
    }

    @keyframes loadingspinner {
        0% {
            transform: rotate(0deg)
        }

        100% {
            transform: rotate(360deg)
        }
    }
</style>


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        getNotif();
    });

    var notif = true;

    function getNotif() {
        $.ajax({
            url: "{!! route('layout/get-notif') !!}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{!!csrf_token()!!}'
            },
            dataType: "json",
            data: {
                'notif': notif,
            },
            success: function(data) {
                if (data.errors) {
                    $('#span_notif').html('');
                }
                if (data.success) {
                    $('#span_notif').html('new');
                }

                if (data.master) {
                    $('#master_nav_non_editable').hide();
                    $('#master_nav_editable').show();
                } else {
                    $('#master_nav_non_editable').show();
                    $('#master_nav_editable').hide();
                }
            },
            error: function(data) {
                console.log(data);
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
    }

    function artLoadingDialogDo(text, onShownCallback = null, onHiddenCallback = null, modalWidth = 500) {
        $('#artLoadingDialogText').html(text);
        if (onShownCallback != null) {
            $('#divArtLoadingDialog').off('shown.bs.modal');
            $('#divArtLoadingDialog').on('shown.bs.modal', artAddModalStack);
            $('#divArtLoadingDialog').on('shown.bs.modal', onShownCallback);
        }
        if (onHiddenCallback != null) {
            $('#divArtLoadingDialog').off('hidden.bs.modal');
            $('#divArtLoadingDialog').on('hidden.bs.modal', artSubModalStack);
            $('#divArtLoadingDialog').on('hidden.bs.modal', onHiddenCallback);
        }
        $('#divArtLoadingDialogModal').attr('style', 'width: ' + modalWidth + 'px !important');
        $('#divArtLoadingDialog').modal({
            backdrop: 'static',
            keyboard: false
        }).show();
    }

    function artLoadingDialogClose() {
        $('#divArtLoadingDialog').modal('hide');
    }

    function artConfirmationDo(title, text, okCallback,modalWidth=600, okText='{{ucwords(__('form.ok'))}}', cancelText='{{ucwords(__('form.cancel'))}}', cancelCallback=null) {
        $('#artConfirmationTitle').text(title);
        $('#artConfirmationBody').html(text);
        $('#artConfirmationBtnOk').html(okText);
        $('#artConfirmationBtnCancel').html(cancelText);
        $('#artConfirmationBtnOk').off('click');
        $('#artConfirmationBtnOk').click(okCallback);
        $('#artConfirmationBtnCancel').off('click');
        if(cancelCallback!=null)
        {
            $('#artConfirmationBtnCancel').click(cancelCallback);
        }
        $('#artMessageDialogBtnOk').focus();
        $('#divArtConfirmationModal').attr('style', 'width: '+modalWidth+'px !important');
        $('#divArtConfirmation').modal().show();
    }

    function artConfirmationClose() {
        $('#divArtConfirmation').modal('hide');
    }

    function artAddModalStack(event) {
        // keep track of the number of open modals
        if (typeof($('body').data('fv_open_modals')) == 'undefined') {
            $('body').data('fv_open_modals', 0);
        }
        // if the z-index of this modal has been set, ignore.
        if ($(this).hasClass('fv-modal-stack')) {
            return;
        }
        $(this).addClass('fv-modal-stack');
        $('body').data('fv_open_modals', $('body').data('fv_open_modals') + 1);
        $(this).css('z-index', 4040 + (10 * $('body').data('fv_open_modals')));
        $('.modal-backdrop').not('.fv-modal-stack').css('z-index', 4039 + (10 * $('body').data('fv_open_modals')));
        $('.modal-backdrop').not('fv-modal-stack').addClass('fv-modal-stack');
    };

    $('.modal').on('shown.bs.modal', artAddModalStack);
</script>

</html>
