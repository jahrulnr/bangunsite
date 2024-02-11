@extends('Widget.modal')
@section('modal-id')read-file @overwrite
@section('modal-title')
Editor - <code>samplecode</code>
@overwrite

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/dracula.min.css" integrity="sha512-gFMl3u9d0xt3WR8ZeW05MWm3yZ+ZfgsBVXLSOiFz2xeVrZ8Neg0+V1kkRIo9LikyA/T9HuS91kDfc2XWse0K0A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('modal-body')
@csrf
<textarea id="editor" name="content" class="form-control" readonly></textarea>
@overwrite

@section('modal-button')
{{-- <button class="btn btn-primary" type="submit"></button> --}}
@endsection

@push('css')
  <style>
    .CodeMirror * { font-size: 11pt; }
  </style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/nginx/nginx.min.js" integrity="sha512-kgLrmRot2x/yBR/HMHKt1S1Q0gIFOt6JGwAqrowCFxtal0MLUrqwzOu1YUA59Uds85K/1dnw9xZrXCs/5FAFJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlmixed/htmlmixed.min.js" integrity="sha512-HN6cn6mIWeFJFwRN9yetDAMSh+AK9myHF1X9GlSlKmThaat65342Yw8wL7ITuaJnPioG0SYG09gy0qd5+s777w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/htmlembedded/htmlembedded.min.js" integrity="sha512-nZlYJlXg6ZqhEdMELUCY9QpeUZHLZh9JUUe2wnHmEvFSWer2gxmDO4xeQ4QlRM1zMzeZsTdm5oFw2IGhsmmLlA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/xml/xml.min.js" integrity="sha512-LarNmzVokUmcA7aUDtqZ6oTS+YXmUKzpGdm8DxC46A6AHu+PQiYCUlwEGWidjVYMo/QXZMFMIadZtrkfApYp/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/clike/clike.min.js" integrity="sha512-l8ZIWnQ3XHPRG3MQ8+hT1OffRSTrFwrph1j1oc1Fzc9UKVGef5XN9fdO0vm3nW0PRgQ9LJgck6ciG59m69rvfg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/php/php.min.js" integrity="sha512-jZGz5n9AVTuQGhKTL0QzOm6bxxIQjaSbins+vD3OIdI7mtnmYE6h/L+UBGIp/SssLggbkxRzp9XkQNA4AyjFBw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js" integrity="sha512-I6CdJdruzGtvDyvdO4YsiAq+pkWf2efgd1ZUSK2FnM/u2VuRASPC7GowWQrWyjxCZn6CT89s3ddGI+be0Ak9Fg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/css/css.min.js" integrity="sha512-rQImvJlBa8MV1Tl1SXR5zD2bWfmgCEIzTieFegGg89AAt7j/NBEe50M5CqYQJnRwtkjKMmuYgHBqtD1Ubbk5ww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
$(document).ready(function(){
  let editorConfig = {
      lineNumbers: true,
      theme: 'dracula',
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