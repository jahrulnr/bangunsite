@extends('layout')
@section('head', $site->name)

@section('content')
<div class="card card-primary card-outline card-tabs">
	<div class="card-header p-0 pt-1 border-bottom-0">
		<ul class="nav nav-tabs" id="tab" role="tablist">
			<li class="nav-item">
				<a class="nav-link text-light active" id="site-tab" data-toggle="pill" href="#site" role="tab" aria-controls="site" aria-selected="true">
					Site
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link text-light" id="config-tab" data-toggle="pill" href="#config" role="tab" aria-controls="config" aria-selected="false">
					Config
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link text-light" id="ssl-tab" data-toggle="pill" href="#ssl-config" role="tab" aria-controls="ssl" aria-selected="false">
					SSL
				</a>
			</li>
		</ul>
	</div>
	<div class="card-body">
		<div class="tab-content" id="tab-content">
			<div class="tab-pane fade active show" id="site" role="tabpanel" aria-labelledby="site-tab">
				<form action="{{route('website.update', $site->id)}}" method="POST">
					@method('PATCH')
					@include('Website.form', [
						'name' => $site->name,
						'domain' => $site->domain,
						'path' => $site->path,
						'ssl' => $site->ssl,
						'active' => $site->active,
					])
					<div class="d-flex justify-content-end">
						<button class="btn btn-primary" type="submit">Update</button>
					</div>
				</form>
			</div>
			<div class="tab-pane fade" id="config" role="tabpanel" aria-labelledby="config-tab">
					Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
			</div>
			<div class="tab-pane fade" id="ssl-config" role="tabpanel" aria-labelledby="ssl-tab">
					Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
			</div>
		</div>
	</div>
	<!-- /.card -->
</div>
@endsection