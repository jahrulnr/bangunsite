@php
	$id = $id ?? '';
	$method = $method ?? 'POST';
	$action = $action ?? '#';
	$title = $title ?? '';
	$body = $body ?? '';
	$button = $button ?? '';
@endphp
<form class="modal fade" aria-hidden="true" id="@yield('modal-id', $id)" method="@yield('modal-method', $method)" action="@yield('action', $action)" enctype="multipart/form-data">
	@csrf
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{!! $title !!} @yield('modal-title', '')</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				{!! $body !!} @yield('modal-body', '')
			</div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				{!! $button !!} @yield('modal-button', '')
			</div>
		</div>
	</div>
</form>