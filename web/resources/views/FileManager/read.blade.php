@extends('Widget.modal')
@section('modal-id')read-file @overwrite
@section('modal-title')
Editor - <code>samplecode</code>
@overwrite

@push('css')
<link rel="stylesheet" href="{{asset('assets/plugins/codemirror/codemirror.css')}}">
<link rel="stylesheet" href="{{asset('assets/plugins/codemirror/theme/dracula.css')}}">
@css(asset('assets/plugins/codemirror/addon/dialog/dialog.css'))
<link rel="stylesheet" href="{{asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.css')}}">
@endpush

@section('modal-body')
@csrf
<textarea id="editor" name="content" class="form-control" readonly></textarea>
@overwrite

@section('modal-button')
{{-- <button class="btn btn-primary" type="submit"></button> --}}
@overwrite

@push('css')
  <style>
    .CodeMirror * { font-size: 11pt; }
  </style>
@endpush

@push('js')
<script src="{{asset('assets/plugins/codemirror/codemirror.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/shell/shell.js')}}"></script>
{{-- <script src="{{asset('assets/plugins/codemirror/mode/properties/properties.js')}}"></script> --}}
<script src="{{asset('assets/plugins/codemirror/mode/nginx/nginx.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/htmlmixed/htmlmixed.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/htmlembedded/htmlembedded.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/xml/xml.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/clike/clike.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/php/php.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/javascript/javascript.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/mode/css/css.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/addon/dialog/dialog.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/addon/search/searchcursor.js')}}"></script>
@js(asset('assets/plugins/codemirror/addon/search/search.js'))
@js(asset('assets/plugins/codemirror/addon/display/autorefresh.js'))
<script src="{{asset('assets/plugins/codemirror/addon/scroll/annotatescrollbar.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.js')}}"></script>
<script src="{{asset('assets/plugins/codemirror/addon/search/jump-to-line.js')}}"></script>
<script>
$(document).ready(function(){
  let editorConfig = {
      lineNumbers: true,
      theme: 'dracula',
  		autoRefresh: true,
  		extraKeys: {"Alt-F": "findPersistent"}
  }

  const editor = CodeMirror.fromTextArea(document.getElementById('editor'), editorConfig);
  const readModal = document.getElementById('read-file ')
  $('.readfile').click(function(){
    const data = JSON.parse($(this).parent().find('data').text())

    const rModal = $(readModal)
    rModal.find('.modal-title code').text(data.name)
    rModal.find('.modal-body textarea').val('loading ...')

    var mode = data.name.split('.').slice(-1)[0]
    if (mode.includes('conf')) mode = 'nginx'
    if (mode.includes('json')||mode.includes('lock')) mode = 'javascript'
    if (mode.includes('js')) mode = 'javascript'
    if (mode.includes('sh')) mode = 'shell'
    console.info('mode: '+ mode)
    editor.setOption('mode', mode)
    rModal.modal('show')

    $.ajax({
      url: '{{route('filemanager.showfile')}}?path={{$fullPath}}',
      type: 'POST',
      data: {
        _token: '{{csrf_token()}}',
        name: data.name
      },
      success: function(resp){
        rModal.find('.modal-body textarea').val(resp.content).trigger('change')
        editor.setOption('value', resp.content)
        setTimeout(() => {
          editor.refresh()
        }, 200);
      },
      error: function(xhr, err, errThrow){
        rModal.modal('hide')
        toastr.error(err.message)
      }
    })
  })
})
$('#config-tab').click(function(){
	setTimeout(() => {
		editor.refresh()
	}, 200);
})
</script>
@endpush