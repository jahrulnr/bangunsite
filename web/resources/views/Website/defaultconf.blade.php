@extends('Widget.modal')
@section('modal-id'){{"defconf-editor-modal"}}@overwrite
@section('modal-title')
Default Configuration
@overwrite

@push('css')
@css(asset('assets/plugins/codemirror/codemirror.css'))
@css(asset('assets/plugins/codemirror/theme/dracula.css'))
@css(asset('assets/plugins/codemirror/addon/dialog/dialog.css'))
@css(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.css'))
@endpush

@section('modal-body')
@csrf
<textarea id="editor-defconf" name="config" class="form-control" readonly>{!! $defConf !!}</textarea>
@overwrite

@section('modal-button')
<button class="btn btn-primary" type="submit">Update</button>
@endsection

@push('css')
  <style>
    .CodeMirror * { font-size: 11pt; }
  </style>
@endpush

@push('js')
@js(asset('assets/plugins/codemirror/codemirror.js'))
@js(asset('assets/plugins/codemirror/mode/nginx/nginx.js'))
@js(asset('assets/plugins/codemirror/addon/dialog/dialog.js'))
@js(asset('assets/plugins/codemirror/addon/search/searchcursor.js'))
@js(asset('assets/plugins/codemirror/addon/search/search.js'))
@js(asset('assets/plugins/codemirror/addon/display/autorefresh.js'))
@js(asset('assets/plugins/codemirror/addon/scroll/annotatescrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/jump-to-line.js'))
<script>
$(document).ready(function(){
  let editorConfig = {
      lineNumbers: true,
      mode: 'nginx',
      theme: 'dracula',
      autoRefresh: true,
      extraKeys: {"Alt-F": "findPersistent"}
  }

  const editor = CodeMirror.fromTextArea(document.getElementById('editor-defconf'), editorConfig);
  $('#show-defconf-modal').click(function(){
    setTimeout(() => {
      editor.refresh()
    }, 200);
  })
})
</script>
@endpush