<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{env('APP_NAME')}}</title>

	<!-- Font Awesome -->
	<link rel="stylesheet" href="{{asset("assets/plugins/fontawesome-free/css/all.min.css")}}">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{asset('assets/css/adminlte.min.css')}}">
	<link rel="stylesheet" href="{{asset('assets/plugins/toastr/toastr.min.css')}}">
</head>
<body class="hold-transition lockscreen dark-mode">
<!-- Automatic element centering -->
	<div class="lockscreen-wrapper">
		<div class="lockscreen-logo">
			<b>Bangun</b>Site
		</div>
		<!-- User name -->
		<div class="lockscreen-name">{{$user->name}}</div>

		<!-- START LOCK SCREEN ITEM -->
		<div class="lockscreen-item">
			<!-- lockscreen image -->
			<div class="lockscreen-image">
				<img src="{{App\Models\User::getPicture()}}" alt="User Image">
			</div>
			<!-- /.lockscreen-image -->

			<!-- lockscreen credentials (contains the form) -->
			<form class="lockscreen-credentials" action="{{route('login.validate')}}" method="POST">
				@csrf
				<div class="input-group">
					<input type="password" class="form-control" placeholder="password" name="password" required>
					<div class="input-group-append">
						<button type="submit" class="btn">
							<i class="fas fa-arrow-right text-muted"></i>
						</button>
					</div>
				</div>
			</form>
		<!-- /.lockscreen credentials -->

		</div>
		<!-- /.lockscreen-item -->
		<div class="help-block text-center">
			Enter your password to retrieve your session
		</div>
		<div class="text-center">
			Or sign in as a <a href="{{route('login')}}" class="text-info">different user</a>
		</div>
	</div>
	<!-- /.center -->

<!-- jQuery -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
@if ($msg = session('error'))
<script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
<script>toastr.error("{{$msg}}")</script>
@endif
</body>
</html>
