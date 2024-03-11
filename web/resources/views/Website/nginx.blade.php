@extends('Widget.modal')
@section('modal-id'){{"nginx-editor-modal"}}@overwrite
@section('modal-title')
Nginx Configuration
@overwrite

@push('css')
@css(asset('assets/plugins/codemirror/codemirror.css'))
@css(asset('assets/plugins/codemirror/theme/dracula.css'))
@css(asset('assets/plugins/codemirror/addon/dialog/dialog.css'))
@css(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.css'))
@endpush

@section('modal-body')
@csrf
@method('PATCH')
<textarea id="editor" name="content" class="form-control" readonly>{!! $nginxConf !!}</textarea>
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
@js(asset('assets/plugins/codemirror/keymap/sublime.js'))
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
      keymap: 'sublime',
      theme: 'dracula',
      autoRefresh: true,
      extraKeys: {"Alt-F": "findPersistent"}
  }

  const editor = CodeMirror.fromTextArea(document.getElementById('editor'), editorConfig);
  $('#show-nginx-modal').click(function(){
    setTimeout(() => {
      editor.refresh()
    }, 200);
  })
})
</script>
@endpush