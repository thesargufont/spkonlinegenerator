<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>SPONGE - Surat Perintah Kerja Online Generator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="Admin Dashboard" name="description" />
    <meta content="ThemeDesign" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

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
                                <a href="#" data-target="#" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-bell"></i> <span class="badge badge-xs badge-danger">3</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-lg">
                                    <li class="text-center notifi-title">Notification <span class="badge badge-xs badge-success">3</span></li>
                                    <li class="list-group">
                                        <!-- list item-->
                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-heading">Your order is placed</div>
                                                <p class="m-0">
                                                    <small>Dummy text of the printing and typesetting industry.</small>
                                                </p>
                                            </div>
                                        </a>
                                        <!-- list item-->
                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-body clearfix">
                                                    <div class="media-heading">New Message received</div>
                                                    <p class="m-0">
                                                        <small>You have 87 unread messages</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- list item-->
                                        <a href="javascript:void(0);" class="list-group-item">
                                            <div class="media">
                                                <div class="media-body clearfix">
                                                    <div class="media-heading">Your item is shipped.</div>
                                                    <p class="m-0">
                                                        <small>It is a long established fact that a reader will</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                        <!-- last list item -->
                                        <a href="javascript:void(0);" class="list-group-item">
                                            <small class="text-primary">See all notifications</small>
                                        </a>
                                    </li>
                                </ul>
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
                                <li><a href="#">Engineer</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="#" class="waves-effect"><i class="ti-agenda"></i><span> Laporan
                                </span></a>
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
                            <a href="calendar.html" class="waves-effect"><i class="ti-calendar"></i><span> Schdule <span class="badge badge-primary pull-right">NEW</span></span></a>
                        </li>

                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="ti-files"></i><span> Master
                                    Data </span><span class="pull-right"><i class="mdi mdi-plus"></i></span></a>
                            <ul class="list-unstyled">
                                <li><a href="{{ route('masters/employee/index') }}">Data Pengguna</a></li>
                                <li><a href="{{ route('masters/location/index') }}">Data Lokasi</a></li>
                                <li><a href="{{ route('masters/basecamp/index') }}">Data Basecamp</a></li>
                                <li><a href="{{ route('masters/department/index') }}">Data Departemen</a></li>
                                <li><a href="{{ route('masters/job/index') }}">Data Pekerjaan</a></li>
                                <li><a href="{{ route('masters/device/index') }}">Data Peralatan</a></li>
                                <li><a href="{{ route('masters/device-category/index') }}">Data Kategori Peralatan</a></li>
                                {{-- <li><a href="{{ route('masters/autorisation/index') }}">Data Otorisasi</a>
                        </li> --}}
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