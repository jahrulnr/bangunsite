<div class="modal fade" aria-hidden="true" id="@yield('modal-id', $id ?? 'modal')">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">@yield('modal-title', $title ?? '')</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				@yield('modal-body', $body ?? '')
			</div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				@yield('modal-button', $button ?? '')
			</div>
		</div>
	</div>
</div>