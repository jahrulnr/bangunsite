@extends('layout')
@section('head', 'Logs')

@section('content')
<ul class="list-group">
    <li class="list-group-item">
        <div class="d-flex justify-content-between">
            <div class="my-auto">
                Global
            </div>
            <div>
                <button class="btn btn-sm btn-primary access-log" data-name="default" data-toggle="modal" data-target="#modal-log">
                    View Access Logs
                </button>
                <button class="btn btn-sm btn-primary error-log" data-name="default" data-toggle="modal" data-target="#modal-log">
                    View Error Logs
                </button>
            </div>
        </div>
    </li>
    @foreach ($sites as $site)
    <li class="list-group-item">
        <div class="d-flex justify-content-between">
            <div class="my-auto">
                {{$site->name}} [{{$site->domain}}]
            </div>
            <div>
                <button class="btn btn-sm btn-primary access-log" data-name="{{$site->domain}}" data-toggle="modal" data-target="#modal-log">
                    View Access Logs
                </button>
                <button class="btn btn-sm btn-primary error-log" data-name="{{$site->domain}}" data-toggle="modal" data-target="#modal-log">
                    View Error Logs
                </button>
            </div>
        </div>
    </li>
    @endforeach
</ul>
@endsection

@include('Widget.log', [
    'id' => 'modal-log',
    'title' => 'Log',
    'body' => ''
])

@push('js')
<script>
const getLog = function(logType, domain){
    $.ajax({
        url: '{{route("logs.get")}}?domain='+domain+'&type='+logType,
        success: function(resp){
            const textarea = $('#mirror-modal-log')
            textarea.val(resp)
            textarea.trigger('change')
        },
        error: function(xhr, status, errThrow){
            console.error(status)
            toastr.error("Error: "+xhr.statusText)
        }
    })
}

$('.btn.access-log, .btn.error-log').click(function(){
    const that = $(this)
    const logType = that.attr('class').includes('access-log') ? 'accesslog':'errorlog'
    const domain = that.attr('data-name')

    getLog(logType, domain)
})
</script>
@endpush