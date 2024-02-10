@php
	$id = $id ?? $__env->yieldContent('modal-id', 'modal');
	$title = $title ?? $__env->yieldContent('modal-title');
	$body = $body ?? $__env->yieldContent('modal-body');
	$button = $button ?? $__env->yieldContent('modal-button');
@endphp
<div class="modal fade" aria-hidden="true" id="{{$id}}">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">{!! $title !!}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				{!! $body !!}
			</div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				{!! $button !!}
			</div>
		</div>
	</div>
</div>