<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('head') | {{env('APP_NAME')}}</title>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset("assets/plugins/fontawesome-free/css/all.min.css")}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset("assets/css/adminlte.min.css")}}">
  <link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
  <link rel="icon" href="{{asset('assets/favicon.png')}}">
  @stack('css')
</head>
<body class="hold-transition layout-fixed dark-mode">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">About</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline" action="https://www.google.com/search" target="_blank">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" name="q" type="search"  placeholder="Google Search" aria-label="Google Search" required>
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  @include('Widget.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>@yield("head", "Beranda")</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/" class="text-light">App</a></li>
              <li class="breadcrumb-item active">@yield('head', "Beranda")</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        @yield("content")
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  @stack('footer')

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> {{env('APP_VERSION')}}
    </div>
    <strong>Copyright &copy; {{date('Y', time()+7*3600)}}.</strong> All rights reserved.
  </footer>

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/js/adminlte.min.js')}}"></script>

@if ($msg = session('success'))
<script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
<script>toastr.success("{{$msg}}")</script>
@elseif ($msg = session('error'))
<script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
<script>toastr.error("{{$msg}}")</script>
@endif
@if (env("ENABLE_LOCKSCREEN"))
<script src="{{asset('assets/plugins/jquery/jquery-idletimer.js')}}"></script>
<script>
	$(document).ready(function(){
    console.info('Lock screen enabled')
		$.idleTimer({{env('LOCK_AFTER')}}*1000); 

		$(document).bind("idle.idleTimer", function(){
			window.location.href = '{{route('lockscreen')}}'
		});
	})
</script>
@endif

@stack('js')
</body>
</html>
