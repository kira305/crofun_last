<!DOCTYPE html>
<html>
<head>
    <meta name="_token" content="<?php echo e(csrf_token(), false); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="x-pjax-version" content="v1">
    <title>Cro-Fun</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link href="<?php echo e(asset('AdminLTE-master/bower_components/bootstrap/dist/css/bootstrap.min.css'), false); ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo e(asset('AdminLTE-master/bower_components/font-awesome/css/font-awesome.min.css'), false); ?>" rel="stylesheet">
    <!-- Ionicons -->
    <link href="<?php echo e(asset('AdminLTE-master/bower_components/Ionicons/css/ionicons.min.css'), false); ?>" rel="stylesheet">
    <!-- DataTables -->
    <link href="<?php echo e(asset('AdminLTE-master/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css'), false); ?>"
        rel="stylesheet">
    <!-- Theme style -->
    <link href="<?php echo e(asset('AdminLTE-master/dist/css/skins/_all-skins.min.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('AdminLTE-master/plugins/timepicker/bootstrap-timepicker.min.css'), false); ?>" rel="stylesheet">
    <!-- Select2 -->
    <link href="<?php echo e(asset('AdminLTE-master/dist/css/AdminLTE.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/bootstrap_add.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/master.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/gobal.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/util.css'), false); ?>" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/2.0.1/jquery.pjax.min.js"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/ajax_setup.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('AdminLTE-master/dist/js/adminlte.min.js'), false); ?>"></script>
    <link href="<?php echo e(asset('datepicker/style.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('datepicker/jquery-ui.min.css'), false); ?>" rel="stylesheet">
    <script type="text/javascript" src="<?php echo e(asset('datepicker/jquery-ui.min.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/session_timeout.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/jquery.ui.datepicker-ja.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/sweetalert2.all.min.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/smooth-scrollbar.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/gobal.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/moment.min.js'), false); ?>"></script>
    <?php echo $__env->yieldContent('styles'); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
</head>
<?php ($authUser = Auth::user()); ?>
<body class="hold-transition skin-blue sidebar-mini my-scrollbar">
    <div class="wrapper">
        <header class="main-header">
            <nav class="navbar display-flex">
                <div class="ab-l-m m-l-20 menu-btn-hide">
                    <a class="text-white fs-20" id="menu_btn" href="#" role="button"><i class="fa fa-bars "></i></a>
                </div>
                <div class="ab-c-m text-white">
                    <div class="fs-20" onclick="window.location='<?php echo e(url("/home"), false); ?>'">Cro-Fun</div>
                </div>
                <div class="ab-r-m display-flex">
                    <div class="flex-c-m pc-show">
                        <span class="text-white"><?php echo e($authUser->company->abbreviate_name, false); ?></span>
                        <span class="text-white">-</span>
                        <span class="text-white"><?php echo e($authUser->usr_name, false); ?></span>
                    </div>
                    <a class="m-l-10 m-r-10 mobile-show" href="<?php echo e(url('user/logout'), false); ?>">
                        <span class="text-white fs-30 m-r-8"><i class="fa fa-sign-out"></i></span>
                    </a>
                    <a class="btn bg-orange m-l-10 m-r-10 pc-show" href="<?php echo e(url('user/logout'), false); ?>">
                        LOGOUT
                    </a>
                </div>
            </nav>

        </header>
        <aside class="main-sidebar main-menu-hide elevation-4">
            <div class="logo flex-c-m brand-link">
                <span class="logo-lg">
                    <img onclick="window.location='<?php echo e(url("/home"), false); ?>'"
                        src="<?php echo e(asset('uploads/logo/'.$logo), false); ?>">
                </span>
            </div>
            <div class="flex-c-m brand-link mobile-show">
                <span class="text-white fs-40 m-r-8"><i class="fa fa-user-circle"></i></span>
                <span class="text-white"><?php echo e($authUser->company->abbreviate_name, false); ?></span>
                <span class="text-white"> - </span>
                <span class="text-white"><?php echo e($authUser->usr_name, false); ?></span>
            </div>
            <section class="sidebar">
                <ul class="sidebar-menu" data-widget="tree">
                    <?php ($url_s0 = explode("/",Request::path())); ?>
                    <?php ($d_ul = ""); ?>
                    <?php ($d_il = ""); ?>
                    <?php $__currentLoopData = $menu_all_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu_all): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ($menu_s0 = explode("/",$menu_all->link_url)); ?>
                    <?php if($menu_all['dis_sort'] == 1): ?>
                    <?php echo $d_ul; ?>

                    <?php echo $d_il; ?>

                    <li class="menu_parent treeview <?php if(strpos($menu_s0[0], $url_s0[0]) !== false): ?> menu-open <?php endif; ?>"
                        data-value="<?php echo e($menu_all->id, false); ?>">
                        <a id="menu_title_<?php echo e($menu_all->position, false); ?>">
                            <i class="fa  fa-folder"></i> <span><?php echo e($menu_all->link_name, false); ?> </span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu" <?php if(strpos($menu_s0[0], $url_s0[0]) !==false): ?> style="display: block;"
                            <?php endif; ?> id="menu_<?php echo e($menu_all->position, false); ?>">
                            <?php ($d_ul = "</ul>"); ?>
                        <?php ($d_il = "
                    </li>"); ?>
                    <?php elseif($menu_all['dis_sort'] === 0): ?>
                    <?php echo $d_ul; ?>

                    <?php echo $d_il; ?>

                    <li class="treeview">
                    <li class="menu_main menuc_<?php echo e($loop->iteration, false); ?> <?php if($menu_s0[0] == $url_s0[0]): ?> label-default <?php endif; ?>"
                        data-value="<?php echo e($menu_all->id, false); ?>"><a href="<?php echo e(url($menu_all->link_url), false); ?>"><i
                                class="<?php echo e($menu_all->icon, false); ?>"></i><?php echo e($menu_all->link_name, false); ?></a></li>
                    </li>
                    <?php ($d_ul = ""); ?>
                    <?php ($d_il = ""); ?>
                    <?php else: ?>
                    <li style="padding-left: 20px;" class="menu_child menuc_<?php echo e($loop->iteration, false); ?>"
                        data-value="<?php echo e($menu_all->id, false); ?>"><a href="<?php echo e(url($menu_all->link_url), false); ?>"><i
                                class="<?php echo e($menu_all->icon, false); ?>"></i><?php echo e($menu_all->link_name, false); ?></a></li>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </section>
        </aside>

        <div class="content-wrapper">
            <main class="py-4" id="body">
                <?php echo $__env->yieldContent('breadcrumbs'); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
            <?php echo $__env->make('layouts.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="" id="sidebar-overlay"></div>
        </div>
        <?php echo $__env->make('layouts.bind_js', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>

    <script type="text/javascript" src="<?php echo e(asset('AdminLTE-master/bower_components/datatables.net/js/jquery.dataTables.min.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('AdminLTE-master/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/get_headquarter_list.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/get_department_list.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/get_group_list.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/ready_event.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/get_position_list.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/get_rule_list.js'), false); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('js/menu_rule.js'), false); ?>"></script>
    <?php if(Request::path() == 'tree/index'): ?>
        <script type="text/javascript" src="<?php echo e(asset('js/digaram_datepicker.js'), false); ?>"></script>
    <?php endif; ?>
</body>
</html>
