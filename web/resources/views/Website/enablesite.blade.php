@extends('Widget.modal')
@section('modal-id'){{"enable-site-modal"}}@overwrite
@section('modal-title')
Enable/Disable Site
@overwrite

@section('modal-body')
@csrf
@method('OPTIONS')
Are you sure to <span id='enable-site-text'>enable/disable</span>?
@overwrite

@section('modal-button')
<button class="btn btn-primary" type="submit">Yes</button>
@overwrite

@push('js')
<script src="{{asset('assets/plugins/toastr/toastr.min.js')}}"></script>
<script>
$(document).ready(function(){
  $('a[data-act="site-disable"],a[data-act="site-delete"]').click(function(e){
    e.preventDefault();
    const that = $(this)
    const data = JSON.parse($(this).parents('td').find('data').text())
    const modal = $('#enable-site-modal')
    const isActive = data.active
    const isDelete = that.attr('data-act').includes('delete')
    const action = modal.parent().attr('action')
    modal.find('input[name="_method"]').val(isDelete ? 'DELETE' : 'OPTIONS')
    modal.parent().attr('action', action.replace('example', data.domain))
    modal.find('.modal-title').html(
      (isDelete 
        ? "Delete"
        : (isActive ? "Disable":"Activate"))
      +" "+ "<code>"+data.domain+"</code>")
    modal.find('#enable-site-text').html(
      (isDelete 
        ? "Delete"
        : (isActive ? "Disable":"Activate"))
      +" "+ "<code>"+data.domain+"</code>")
    modal.modal('show')
  })

  $('#enable-site-modal').find('button[type="submit"]').click(function(e){
    e.preventDefault()
    const modal = $('#enable-site-modal')
    const form = new FormData(modal.parent()[0])
    $.ajax({
      url: modal.parent().attr('action')+`?_token=${modal.find('input[name="_token"]').val()}`,
      type: modal.find('input[name="_method"]').val(),
      data: form,
      processData: false,
      success: function(resp){
        toastr.success(resp.msg)
        setTimeout(() => {
          window.location.reload()
        }, 1000);
      },
    }).fail(function (xhr, err, errorThrow) {
      console.error("Status:", xhr.status, errorThrow)
      switch (xhr.status) {
        case 400:
          toastr.error(xhr.responseJSON.msg)
          return
        case 405:
          toastr.error("Request not valid!")
          return
        case 500:
          toastr.error("Internal server Error! check your log!")
          return
        case 504:
          toastr.error("Timeout! try again or decrase max timeout setting");
          return
      }

      if (errorThrow.length == 0)
        toastr.error('Unexpected error')
    });
  })
})
</script>
@endpush