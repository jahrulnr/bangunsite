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
    'body' => '',
    'button' => '<button type="button" class="btn btn-primary refresh">Refresh</button>'
])

@push('js')
<script>
let logDomain
let logDomainType
const getLog = function(logType, domain){
    return $.ajax({
        url: '{{route("logs.get")}}?domain='+domain+'&type='+logType,
        cache: false,
        success: function(resp){
            logDomain=domain
            logDomainType=logType
            const textarea = $('#mirror-modal-log')
            textarea.val(resp)
            textarea.trigger('change')
        },
        error: function(xhr, status, errThrow){
            console.error(errThrow)
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

$('.refresh').click(function(){
    if (logDomain == null || logDomainType == null)
        return

    getLog(logDomainType, logDomain)
    .done(function(data){
        toastr.success("Log refresh successfully")
    })
    .fail(function(jqXHR, status){
        toastr.error("Log refresh failed! see console for details")
    })
})
</script>
@endpush