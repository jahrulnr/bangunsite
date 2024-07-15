
<div class="modal fade" aria-hidden="true" id="@yield('modal-id', $id ?? 'modal')" style="display: none">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">@yield('modal-title', $title ?? 'Example Modal')</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<textarea name="" id="mirror-{{$id??'modal'}}" cols="30" rows="10">@yield('modal-body', $body ?? 'Example')</textarea>
			</div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				{!! $button ?? "" !!} @yield('modal-button', '')
			</div>
		</div>
	</div>
</div>

@push('css')
@css(asset('assets/plugins/codemirror/codemirror.css'))
@css(asset('assets/plugins/codemirror/theme/dracula.css'))
@css(asset('assets/plugins/codemirror/addon/dialog/dialog.css'))
@css(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.css'))
@endpush
@push('js')
@js(asset('assets/js/attrchange.js'))
@js(asset('assets/plugins/codemirror/codemirror.js'))
@js(asset('assets/plugins/codemirror/mode/shell/shell.js'))
@js(asset('assets/plugins/codemirror/addon/dialog/dialog.js'))
@js(asset('assets/plugins/codemirror/addon/search/searchcursor.js'))
@js(asset('assets/plugins/codemirror/addon/search/search.js'))
@js(asset('assets/plugins/codemirror/addon/display/autorefresh.js'))
@js(asset('assets/plugins/codemirror/addon/scroll/annotatescrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/jump-to-line.js'))
<script>
// $(document).ready(function () {
	const textarea = document.getElementById("mirror-{{$id??'modal'}}")
	let mirror = CodeMirror.fromTextArea(textarea, {
		mode: 'shell',
		theme: "dracula",
		readOnly: true,
  		autoRefresh: true,
  		extraKeys: {"Alt-F": "findPersistent"}
	});

	// remove old value
	mirror.setValue("")

	$("#{{$id??'modal'}}").attrchange({
		callback: function(){
		setTimeout(() => {
				mirror.refresh()
			}, 200);
		}
	})
	$(textarea).change(function(){
		mirror.setValue($(this).val())
		setTimeout(function(){
			mirror.scrollIntoView((mirror.lastLine()), 0)
		}, 200)
	})
// })
</script>
@endpush