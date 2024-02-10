@extends('layout')
@section('head', $site->name)

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/dracula.min.css" integrity="sha512-gFMl3u9d0xt3WR8ZeW05MWm3yZ+ZfgsBVXLSOiFz2xeVrZ8Neg0+V1kkRIo9LikyA/T9HuS91kDfc2XWse0K0A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/nginx/nginx.min.js" integrity="sha512-kgLrmRot2x/yBR/HMHKt1S1Q0gIFOt6JGwAqrowCFxtal0MLUrqwzOu1YUA59Uds85K/1dnw9xZrXCs/5FAFJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
var editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
    lineNumbers: true,
    mode: 'nginx',
    theme: 'dracula',
	value: $('#editor').val()
});
$('#config-tab').click(function(){
	setTimeout(() => {
		editor.refresh()
	}, 200);
})
</script>
@endpush

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
				<form action="{{route('website.updateConfig', $site->id)}}" method="POST">
					@csrf
					<textarea id="editor" name="config">{{session('config') ?? $config}}</textarea>
					<div class="d-flex justify-content-end">
						<button class="btn btn-primary mt-3" type="submit">Update</button>
					</div>
				</form>
			</div>
			<div class="tab-pane fade" id="ssl-config" role="tabpanel" aria-labelledby="ssl-tab">				
				<form action="{{route('website.updateSSL', $site->id)}}" method="POST" class="row">
					@csrf
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label>Private Key</label>
							<textarea class="form-control mb-3" name="private" cols="30" rows="10">{{$config}}</textarea>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label>Public Key</label>
							<textarea class="form-control mb-3" name="public" cols="30" rows="10">{{$config}}</textarea>
						</div>
					</div>
					<div class="col-12">
						<div class="d-flex justify-content-end">
							<button class="btn btn-primary" type="submit">Update</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /.card -->
</div>
@endsection