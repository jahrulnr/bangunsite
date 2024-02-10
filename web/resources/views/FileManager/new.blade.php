@extends('Widget.modal')
@section('modal-id', 'new-object')
@section('modal-title', 'New File/Directory')

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/dracula.min.css" integrity="sha512-gFMl3u9d0xt3WR8ZeW05MWm3yZ+ZfgsBVXLSOiFz2xeVrZ8Neg0+V1kkRIo9LikyA/T9HuS91kDfc2XWse0K0A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/nginx/nginx.min.js" integrity="sha512-kgLrmRot2x/yBR/HMHKt1S1Q0gIFOt6JGwAqrowCFxtal0MLUrqwzOu1YUA59Uds85K/1dnw9xZrXCs/5FAFJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
var editor = CodeMirror.fromTextArea(document.getElementById('content'), {
  lineNumbers: true,
  mode: 'text',
  theme: 'dracula'
});
$('#vert-new-file').click(function(){
	setTimeout(() => {
		editor.refresh()
	}, 200);
})
</script>
@endpush

@section('modal-body')
@csrf
<div class="row">
  <div class="col-5 col-sm-3">
    <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
      <a class="nav-link text-light active" id="vert-new-directory" data-toggle="pill" href="#vert-tab-directory" role="tab" aria-controls="vert-tab-directory" aria-selected="false">
        New Directory
      </a>
      <a class="nav-link text-light" id="vert-new-file" data-toggle="pill" href="#vert-tab-new-file" role="tab" aria-controls="vert-tab-new-file" aria-selected="true">
        New File
      </a>
      <a class="nav-link text-light" id="vert-remote-download" data-toggle="pill" href="#vert-tab-remote-download" role="tab" aria-controls="vert-tab-remote-download" aria-selected="false">
        Remote Download
      </a>
      <a class="nav-link text-light" id="vert-upload-file" data-toggle="pill" href="#vert-tab-upload-file" role="tab" aria-controls="vert-tab-upload-file" aria-selected="false">
        Upload File
      </a>
    </div>
  </div>
  <div class="col-7 col-sm-9">
    <div class="tab-content" id="vert-tabs-tabContent">
      <form action="{{route('filemanager.new')}}" method="POST" class="tab-pane text-left fade show active" id="vert-tab-directory" role="tabpanel" aria-labelledby="vert-new-directory">
        <div class="mb-3">
          @csrf
          <input type="hidden" name="type" value="directory">
          <input type="hidden" name="base" value="{{$fullPath}}">
          <div class="form-group mb-3">
            <label>Directory Name</label>
            <input type="text" name="name" value="" class="form-control" placeholder="Directory name" required>
          </div>
          <div class="form-group mb-3">
            <label>Permission</label>
            <input type="number" min="700" max="777" name="permission" value="755" class="form-control" required>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
      </form>
      <form action="{{route('filemanager.new')}}" method="POST" class="tab-pane fade" id="vert-tab-new-file" role="tabpanel" aria-labelledby="vert-new-file">
        <div class="mb-3">
          @csrf
          <input type="hidden" name="type" value="file">
          <input type="hidden" name="base" value="{{$fullPath}}">
          <div class="form-group mb-3">
            <label>File Name</label>
            <input type="text" name="name" value="" class="form-control" placeholder="File name" required>
          </div>
          <div class="form-group mb-3">
            <label>Content</label>
            <textarea name="content" id="content"></textarea>
          </div>
          <div class="form-group mb-3">
            <label>Permission</label>
            <input type="number" min="600" max="777" name="permission" value="644" class="form-control" required>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
      </form>
      <form action="{{route('filemanager.new')}}" method="POST"  class="tab-pane fade" id="vert-tab-remote-download" role="tabpanel" aria-labelledby="vert-remote-download">
        <div class="mb-3">
          @csrf
          <input type="hidden" name="type" value="remote">
          <input type="hidden" name="base" value="{{$fullPath}}">
          <div class="form-group mb-3">
            <label>File Name</label>
            <input type="text" name="name" value="" class="form-control" placeholder="File name" required>
          </div>
          <div class="form-group mb-3">
            <label>URL</label>
            <input type="url" name="url" value="" class="form-control" placeholder="URL" required>
          </div>
          <div class="form-group mb-3">
            <label>Permission</label>
            <input type="number" min="600" max="777" name="permission" value="644" class="form-control" required>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Start</button>
      </form>
      <form action="{{route('filemanager.new')}}" method="POST" enctype="multipart/form-data" class="tab-pane fade" id="vert-tab-upload-file" role="tabpanel" aria-labelledby="vert-upload-file">
        <div class="mb-3">
          @csrf
          <input type="hidden" name="type" value="upload">
          <input type="hidden" name="base" value="{{$fullPath}}">
          <div class="form-group mb-3">
            <label>File</label>
            <input type="file" name="file" value="" class="form-control" placeholder="File" required>
            <span class="text-muted">File max. {{ini_get('upload_max_filesize')}}</span>
          </div>
          <div class="form-group mb-3">
            <label>Permission</label>
            <input type="number" min="600" max="777" name="permission" value="644" class="form-control" required>
          </div>
          <div id="upload-proggress" class="fade mb-3" style="display: none">
            <div class="progress">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
            </div>
          </div>
        </div>
        <button class="btn btn-primary" type="submit">Upload</button>
      </form>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="{{asset('assets/plugins/jquery/jquery.form.min.js')}}"></script>
<script>
$(document).ready(function () {
  $('#vert-tab-upload-file').ajaxForm({
    beforeSend: function (data, myForm) {
      var percentage = '0';
      $('#upload-proggress').show().addClass('show')
      $('.progress .progress-bar').css("width", percentage+'%', function() {
        return $(this).attr("aria-valuenow", percentage) + "%";
      })
    },
    uploadProgress: function (event, position, total, percentComplete) {
      $('.progress .progress-bar').css("width", percentComplete+'%', function() {
        return $(this).attr("aria-valuenow", percentComplete) + "%";
      })
    },
    complete: function (xhr) {
      $('.progress .progress-bar').css("width", '100%', function() {
        return $(this).attr("aria-valuenow", 100) + "%";
      })
    }
  });
});
</script>
@endpush