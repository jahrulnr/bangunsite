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
				<form action="{{route('website.updateConfig', $site->id)}}" method="POST">
					@csrf
					<textarea id="editor" name="config">{{session('config') ?? $config}}</textarea>
					<div class="d-flex justify-content-end">
						<button class="btn btn-primary mt-3" type="submit">Update</button>
					</div>
				</form>
			</div>
			<div class="tab-pane fade" id="ssl-config" role="tabpanel" aria-labelledby="ssl-tab">
				@if ($privateCert && $publicCert && !$isEnabled)
					<div class="callout callout-info">
						SSL is exists, but not enabled
					</div>
				@endif
				<form action="{{route('website.updateSSL', $site->id)}}" method="POST" class="row">
					@csrf
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label>Private Key</label>
							<textarea class="form-control mb-3" name="private" cols="30" rows="10">{{$privateCert}}</textarea>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label>Public Key</label>
							<textarea class="form-control mb-3" name="public" cols="30" rows="10">{{$publicCert}}</textarea>
						</div>
					</div>
					<div class="col-12">
						<div class="d-flex justify-content-end">
							<div>
								<button class="btn btn-primary mr-3" id="install-ssl">Install SSL</button>
								<button class="btn btn-primary" type="submit">Update</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /.card -->
</div>
@endsection

@push('footer')
	@include('Widget.log', [
		'id' => 'modal-install',
		'title' => 'Install SSL',
		'body' => ''
	])
@endpush

@push('css')
@css(asset('assets/plugins/codemirror/codemirror.css'))
@css(asset('assets/plugins/codemirror/theme/dracula.css'))
@css(asset('assets/plugins/codemirror/addon/dialog/dialog.css'))
@css(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.css'))
@endpush
@push('js')
@js(asset('assets/plugins/codemirror/codemirror.js'))
@js(asset('assets/plugins/codemirror/mode/nginx/nginx.js'))
@js(asset('assets/plugins/codemirror/keymap/sublime.js'))
@js(asset('assets/plugins/codemirror/addon/dialog/dialog.js'))
@js(asset('assets/plugins/codemirror/addon/search/searchcursor.js'))
@js(asset('assets/plugins/codemirror/addon/search/search.js'))
@js(asset('assets/plugins/codemirror/addon/display/autorefresh.js'))
@js(asset('assets/plugins/codemirror/addon/scroll/annotatescrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/jump-to-line.js'))
<script>
var editor = CodeMirror.fromTextArea(document.getElementById('editor'), {
    lineNumbers: true,
    mode: 'nginx',
	keymap: 'sublime',
    theme: 'dracula',
	autoRefresh: true,
	extraKeys: {"Alt-F": "findPersistent"}
});
$('#config-tab').click(function(){
	setTimeout(() => {
		editor.refresh()
	}, 200);
})
</script>
<script>
$(document).ready(function(){
	let clicked = false
	let start = "true"
	let progress = "false"
	const modal = $('#modal-install')
	$("#install-ssl").click(function(e){
		e.preventDefault()
		modal.modal('show')

		let textarea = $('#mirror-modal-install')
		if (clicked == false)
			clicked = setInterval(() => {
				$.ajax({
					url: '{{route("website.installSSL", $site->id)}}?start='+start+'&progress='+progress,
					success: function(resp){
						start = "false"
						progress = "true"
						textarea.val(resp).trigger('change')

						if(resp.includes("-- Task Done --")){
							clearInterval(clicked)
							clicked = false
							start = "true"
							progress = "false"
						}
					}
				})
			}, 1000);
	})

	modal.on('hidden.bs.modal', function(){
		if (clicked != false){
			clearInterval(clicked)
			clicked = false
		}
	})
})
</script>
@endpush