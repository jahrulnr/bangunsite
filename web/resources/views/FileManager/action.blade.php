@extends('Widget.modal')
@section('modal-id')action-object @overwrite
@section('modal-title') @overwrite

@section('modal-body')
@csrf
<input type="hidden" name="_method" value="">
<div class="mb-3">
  <input type="hidden" name="type" value="">
  <input type="hidden" name="path" value="{{$fullPath}}">
  <input type="hidden" name="name" value="">

  <div id="copyObjectMsg" class="d-none">
    <div class="mb-3 form-group">
      <label>
        Copy 
        <code id="filecopy">{{str_replace('//', '/', $fullPath.'/')}}File name</code> 
        to
      </label>
      <input type="text" class="form-control" name="to" value="{{env('WEB_PATH')}}" required>
    </div>
  </div>

  <div id="chmodObjectMsg" class="d-none">
    <div class="mb-3 form-group">
      <label>Fullpath</label>
      <input type="text" class="form-control" name="fullpath" value="" readonly>
    </div>
    <div class="mb-3 form-group">
      <label>Permission</label>
      <input type="number" min="600" max="777" class="form-control" name="permission" value="644" required>
    </div>
  </div>

  <div id="deleteObjectMsg" class="d-none">
    This action will delete 
    <code>{{str_replace('//', '/', $fullPath.'/')}}<span id="filedelete">File name</span></code>
    permanently! Are you sure?
  </div>
</div>
@overwrite

@section('modal-button')
<button class="btn btn-primary" type="submit"></button>
@endsection

@push('js')
<script>
$(document).ready(function(){
  // laravel section must be use @override for multiple include modal widget
  // So, I must using getElementById to get element for jQuery
  const actionModal = document.getElementById('action-object ')

  $('.deletefile').click(function(){
    const data = JSON.parse($(this).parent().find('data').text())
    const actModal = $(actionModal)
    actModal.find('#copyObjectMsg').addClass('d-none')
    actModal.find('#chmodObjectMsg').addClass('d-none')
    actModal.find('#deleteObjectMsg').removeClass('d-none')
    actModal.find('input[name="_method"]').val('DELETE')
    actModal.find('input[name="type"]').val('delete')
    actModal.find('input[name="name"]').val(data.name)
    actModal.find('.modal-title').html(`Delete <code>${data.name}</code>`)
    actModal.find('.modal-body #filedelete').text(data.name)
    actModal.find('.modal-footer button[type="submit"]').text('Delete it!')
    actModal.modal('show')
  })

  $('.chmodfile').click(function(){
    const data = JSON.parse($(this).parent().find('data').text())
    const actModal = $(actionModal)
    actModal.find('#copyObjectMsg').addClass('d-none')
    actModal.find('#chmodObjectMsg').removeClass('d-none')
    actModal.find('#deleteObjectMsg').addClass('d-none')
    actModal.find('input[name="_method"]').val('PATCH')
    actModal.find('input[name="type"]').val('chmod')
    actModal.find('input[name="name"]').val(data.name)
    actModal.find('input[name="fullpath"]').val("{{str_replace('//', '/', $fullPath.'/')}}"+data.name)
    actModal.find('input[name="permission"]').val(data.permission)
    actModal.find('.modal-title').html(`Chage permission <code>${data.name}</code>`)
    actModal.find('.modal-footer button[type="submit"]').text('Update')
    actModal.modal('show')
  })

  $('.copyfile').click(function(){
    const data = JSON.parse($(this).parent().find('data').text())
    const actModal = $(actionModal)
    console.log(data);
    actModal.find('#copyObjectMsg').removeClass('d-none')
    actModal.find('#chmodObjectMsg').addClass('d-none')
    actModal.find('#deleteObjectMsg').addClass('d-none')
    actModal.find('input[name="_method"]').val('PATCH')
    actModal.find('input[name="type"]').val('copy')
    actModal.find('input[name="name"]').val(data.name)
    actModal.find('#filecopy').text("{{str_replace('//', '/', $fullPath.'/')}}"+data.name)
    actModal.find('input[name="to"]').val("{{env('WEB_PATH')}}")
    actModal.find('.modal-title').html(`Copy <code>${data.name}</code>`)
    actModal.find('.modal-footer button[type="submit"]').text('Do it!')
    actModal.modal('show')
  })
})
</script>
@endpush