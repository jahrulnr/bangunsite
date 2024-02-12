@extends('Widget.modal')
@section('modal-id'){{"defconf-editor-modal"}}@overwrite
@section('modal-title')
Default Configuration
@overwrite

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/dracula.min.css" integrity="sha512-gFMl3u9d0xt3WR8ZeW05MWm3yZ+ZfgsBVXLSOiFz2xeVrZ8Neg0+V1kkRIo9LikyA/T9HuS91kDfc2XWse0K0A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/nginx/nginx.min.js" integrity="sha512-kgLrmRot2x/yBR/HMHKt1S1Q0gIFOt6JGwAqrowCFxtal0MLUrqwzOu1YUA59Uds85K/1dnw9xZrXCs/5FAFJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$(document).ready(function(){
  let editorConfig = {
      lineNumbers: true,
      mode: 'nginx',
      theme: 'dracula',
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