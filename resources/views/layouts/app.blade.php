<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Apotek Batu Sehat</title>

    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png" >

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  	<!-- Bootstrap 3.3.7 -->
  	<link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  	<!-- Font Awesome -->
  	<link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
  	<!-- Ionicons -->
  	<link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
  	<!-- Theme style -->
  	<link rel="stylesheet" href="{{ asset('css/AdminLTE.min.css') }}">
  	<!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  	<link rel="stylesheet" href="{{ asset('css/skins/_all-skins.min.css') }}">
  	<!-- Date Picker -->
  	<link rel="stylesheet" href="{{ asset('bower_components/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}">
  	<!-- bootstrap wysihtml5 - text editor -->
  	<link rel="stylesheet" href="{{ asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/noty/button.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/sweet-alert/sweet-alert.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/magicsuggest/magicsuggest-min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/dropzone/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/cropper/cropper.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/colorbox/colorbox.css') }}">

  	<!-- Google Font -->
  	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @yield('css')

  	<script type="text/javascript">
    </script>
</head>
<body class="hold-transition skin-blue sidebar-mini">

<div id="modal-ajax" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-ajax-title"></h4>
            </div>
            <div id="modal-ajax-body"></div>
        </div>
    </div>
</div>

<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="{{ route('/') }} " class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>B</b>S</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg" style="    margin: 0px;"><img style="width: 15%;margin-left: -15px;margin-right: 5px;" src="{{ asset('img/logo.png') }}"><b>Batu Sehat</b> Apotek</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">         
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{ asset('img/user_large.jpg') }}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{ asset('img/user_large.jpg') }}" class="img-circle" alt="User Image">

                <p>
                  {{ Auth::user()->name }}
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="{{ route('logout') }}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset('img/user_large.jpg') }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Auth::user()->name }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="{{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
          <a href="{{ route('dashboard') }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        
        <?php if(Auth::user()->level == 1): ?>
        <li class="treeview {{ (str_replace(['obat','kategori','supplier','customer','dokter','pengguna'], '', Route::currentRouteName()) != Route::currentRouteName()) ? 'active' : '' }}">
          <a href="#">
            <i class="fa fa-tasks"></i> <span>Master</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            <li {{ in_array(Route::currentRouteName(), ['obat']) ? 'class=active' : '' }}><a href="{{ route('obat') }}"><i class="fa fa-circle-o"></i> Obat</a></li>
            <li {{ in_array(Route::currentRouteName(), ['kategori']) ? 'class=active' : '' }}><a href="{{ route('kategori') }}"><i class="fa fa-circle-o"></i> Kategori</a></li>
            <li {{ in_array(Route::currentRouteName(), ['supplier']) ? 'class=active' : '' }}><a href="{{ route('supplier') }}"><i class="fa fa-circle-o"></i> Supplier</a></li>
            <li {{ in_array(Route::currentRouteName(), ['customer']) ? 'class=active' : '' }}><a href="{{ route('customer') }}"><i class="fa fa-circle-o"></i> Customer</a></li>
            <li {{ in_array(Route::currentRouteName(), ['dokter']) ? 'class=active' : '' }}><a href="{{ route('dokter') }}"><i class="fa fa-circle-o"></i> Dokter</a></li>
            <li {{ in_array(Route::currentRouteName(), ['pengguna']) ? 'class=active' : '' }}><a href="{{ route('user') }}"><i class="fa fa-circle-o"></i> Pengguna</a></li>
          </ul>
        </li>
        <?php endif; ?>
        <li class="treeview {{ (str_replace([''], '', Route::currentRouteName()) != Route::currentRouteName()) ? 'active' : '' }}">
          <a href="#">
            <i class="fa fa-tasks"></i> <span>Transaksi</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">

            <li class="treeview">
              <a href="#"><i class="fa fa-circle-o"></i> Penjualan
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i> Reguler</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i> Resep</a></li>
              </ul>
            </li>

            <li class="treeview">
              <a href="#"><i class="fa fa-circle-o"></i> Pembelian
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i> PO (Surat Pesanan)</a></li>
              <li><a href="#"><i class="fa fa-circle-o"></i> Langsung</a></li>
              </ul>
            </li>

            <li {{ in_array(Route::currentRouteName(), ['']) ? 'class=active' : '' }}><a href="{{ route('/') }}"><i class="fa fa-circle-o"></i> Retur Penjualan</a></li>
            <li {{ in_array(Route::currentRouteName(), ['']) ? 'class=active' : '' }}><a href="{{ route('/') }}"><i class="fa fa-circle-o"></i> Retur Pembelian</a></li>
          </ul>
        </li>
        <li class="treeview {{ (str_replace([''], '', Route::currentRouteName()) != Route::currentRouteName()) ? 'active' : '' }}">
          <a href="#">
            <i class="fa fa-tasks"></i> <span>Report</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>

          <ul class="treeview-menu">
            <li {{ in_array(Route::currentRouteName(), ['']) ? 'class=active' : '' }}><a href="{{ route('/') }}"><i class="fa fa-circle-o"></i> Penjualan</a></li>
            <li {{ in_array(Route::currentRouteName(), ['']) ? 'class=active' : '' }}><a href="{{ route('/') }}"><i class="fa fa-circle-o"></i> Pembelian</a></li>
            <li {{ in_array(Route::currentRouteName(), ['']) ? 'class=active' : '' }}><a href="{{ route('/') }}"><i class="fa fa-circle-o"></i> Stok Opname</a></li>
          </ul>
        </li>
        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $header_title ?>
                <small><?php echo $header_description ?></small>
            </h1>
            <?php
            if (isset($breadcrumb) && is_array($breadcrumb)) {
                ?>
                <ol class="breadcrumb">
                    <?php
                    foreach ($breadcrumb as $key => $value) {
                        echo "<li><a href='$value[url]'>$value[text]</a></li>";
                    }
                    ?>
                </ol>
            <?php } ?>
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2014-2016 <a href="https://adminlte.io">Almsaeed Studio</a>.</strong> All rights
    reserved.
  </footer>

  
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">

        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane" id="control-sidebar-home-tab">
            </div>
            <!-- /.tab-pane -->
        </div>
    </aside>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class='control-sidebar-bg'></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2 -->
<script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="{{ asset('plugins/noty/noty.min.js') }}"></script>
<script src="{{ asset('bower_components/jquery-countable/jquery.countable.js') }}"></script>
<script src="{{ asset('bower_components/jquery-validate/jquery.validate.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset('bower_components/bootstrap-datetimepicker/moment.min.js') }}"></script>
<script src="{{ asset('bower_components/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset('plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- Slimscroll -->
<script src="{{ asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/holder/holder.min.js') }}"></script>

<script src="{{ asset('plugins/sweet-alert/sweet-alert.min.js') }}"></script>
<script src="{{ asset('bower_components/jquery-form/ajaxForm.js') }}"></script>

<script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('plugins/magicsuggest/magicsuggest-min.js') }}"></script>
<script src="{{ asset('plugins/dropzone/dropzone.min.js') }}"></script>

<script src="{{ asset('plugins/colorbox/jquery.colorbox-min.js') }}"></script>
<script src="{{ asset('plugins/clipboard/clipboard.min.js') }}"></script>
<script src="{{ asset('plugins/cropper/cropper.min.js') }}"></script>

<script src="{{ asset('bower_components/jquery-maskmoney/jquery.maskMoney.min.js') }}"></script>

<!-- App -->
<script src="{{ asset('js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('js/demo.js') }}"></script>

@yield('js')

</body>
</html>

</html>
