<!DOCTYPE html>
<html>
<head>
    <meta name="_token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="x-pjax-version" content="v1">
    <title>Cro-Fun</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link href="{{ asset('AdminLTE-master/bower_components/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('AdminLTE-master/bower_components/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Ionicons -->
    <link href="{{ asset('AdminLTE-master/bower_components/Ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <!-- DataTables -->
    <link href="{{ asset('AdminLTE-master/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}"
        rel="stylesheet">
    <!-- Theme style -->
    <link href="{{ asset('AdminLTE-master/dist/css/skins/_all-skins.min.css') }}" rel="stylesheet">
    <link href="{{ asset('AdminLTE-master/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link href="{{ asset('AdminLTE-master/dist/css/AdminLTE.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap_add.css') }}" rel="stylesheet">
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    <link href="{{ asset('css/gobal.css') }}" rel="stylesheet">
    <link href="{{ asset('css/util.css') }}" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/2.0.1/jquery.pjax.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/ajax_setup.js') }}"></script>
    <script type="text/javascript" src="{{ asset('AdminLTE-master/dist/js/adminlte.min.js') }}"></script>
    <link href="{{ asset('datepicker/style.css') }}" rel="stylesheet">
    <link href="{{ asset('datepicker/jquery-ui.min.css') }}" rel="stylesheet">
    <script type="text/javascript" src="{{ asset('datepicker/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/session_timeout.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.ui.datepicker-ja.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/smooth-scrollbar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/gobal.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/moment.min.js') }}"></script>
    @yield('styles')
    @yield('scripts')
</head>
@php($authUser = Auth::user())
<body class="hold-transition skin-blue sidebar-mini my-scrollbar">
    <div class="wrapper">
        <header class="main-header">
            <nav class="navbar display-flex">
                <div class="ab-l-m m-l-20 menu-btn-hide">
                    <a class="text-white fs-20" id="menu_btn" href="#" role="button"><i class="fa fa-bars "></i></a>
                </div>
                <div class="ab-c-m text-white">
                    <div class="fs-20" onclick="window.location='{{ url("/home") }}'">Cro-Fun</div>
                </div>
                <div class="ab-r-m display-flex">
                    <div class="flex-c-m pc-show">
                        <span class="text-white">{{ $authUser->company->abbreviate_name }}</span>
                        <span class="text-white">-</span>
                        <span class="text-white">{{ $authUser->usr_name }}</span>
                    </div>
                    <a class="m-l-10 m-r-10 mobile-show" href="{{ url('user/logout') }}">
                        <span class="text-white fs-30 m-r-8"><i class="fa fa-sign-out"></i></span>
                    </a>
                    <a class="btn bg-orange m-l-10 m-r-10 pc-show" href="{{ url('user/logout') }}">
                        LOGOUT
                    </a>
                </div>
            </nav>

        </header>
        <aside class="main-sidebar main-menu-hide elevation-4">
            <div class="logo flex-c-m brand-link">
                <span class="logo-lg">
                    <img onclick="window.location='{{ url("/home") }}'"
                        src="{{ asset('uploads/logo/'.$logo) }}">
                </span>
            </div>
            <div class="flex-c-m brand-link mobile-show">
                <span class="text-white fs-40 m-r-8"><i class="fa fa-user-circle"></i></span>
                <span class="text-white">{{ $authUser->company->abbreviate_name }}</span>
                <span class="text-white"> - </span>
                <span class="text-white">{{ $authUser->usr_name }}</span>
            </div>
            <section class="sidebar">
                <ul class="sidebar-menu" data-widget="tree">
                    @php($url_s0 = explode("/",Request::path()))
                    @php($d_ul = "")
                    @php($d_il = "")
                    @foreach( $menu_all_list as $menu_all)
                    @php($menu_s0 = explode("/",$menu_all->link_url))
                    @if ($menu_all['dis_sort'] == 1)
                    {!! $d_ul !!}
                    {!! $d_il !!}
                    <li class="menu_parent treeview @if (strpos($menu_s0[0], $url_s0[0]) !== false) menu-open @endif"
                        data-value="{{ $menu_all->id }}">
                        <a id="menu_title_{{$menu_all->position}}">
                            <i class="fa  fa-folder"></i> <span>{{$menu_all->link_name}} </span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu" @if (strpos($menu_s0[0], $url_s0[0]) !==false) style="display: block;"
                            @endif id="menu_{{$menu_all->position}}">
                            @php($d_ul = "</ul>")
                        @php($d_il = "
                    </li>")
                    @elseif ($menu_all['dis_sort'] === 0)
                    {!! $d_ul !!}
                    {!! $d_il !!}
                    <li class="treeview">
                    <li class="menu_main menuc_{{$loop->iteration}} @if ($menu_s0[0] == $url_s0[0]) label-default @endif"
                        data-value="{{ $menu_all->id }}"><a href="{{url($menu_all->link_url)}}"><i
                                class="{{ $menu_all->icon }}"></i>{{$menu_all->link_name}}</a></li>
                    </li>
                    @php($d_ul = "")
                    @php($d_il = "")
                    @else
                    <li style="padding-left: 20px;" class="menu_child menuc_{{$loop->iteration}}"
                        data-value="{{ $menu_all->id }}"><a href="{{ url($menu_all->link_url) }}"><i
                                class="{{ $menu_all->icon }}"></i>{{ $menu_all->link_name }}</a></li>
                    @endif
                    @endforeach
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            <main class="py-4" id="body">
                @yield('breadcrumbs')
                @yield('content')
            </main>
            @include('layouts.footer')
            <div class="" id="sidebar-overlay"></div>
        </div>
        @include('layouts.bind_js')
    </div>

    <script type="text/javascript" src="{{ asset('AdminLTE-master/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('AdminLTE-master/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/get_headquarter_list.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/get_department_list.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/get_group_list.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/ready_event.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/get_position_list.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/get_rule_list.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/menu_rule.js') }}"></script>
    @if(Request::path() == 'tree/index')
        <script type="text/javascript" src="{{ asset('js/digaram_datepicker.js') }}"></script>
    @endif
</body>
</html>
