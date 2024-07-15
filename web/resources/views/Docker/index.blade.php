@extends('layout')
@section('head', 'Docker')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Container</h4>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover border-top border-dark table-bordered text-sm">
                <thead>
                    <tr>
                        @foreach ($head as $tr)
                            <th style="vertical-align: middle" class="text-nowrap text-center">{{$tr}}</th>
                        @endforeach
                        <th style="vertical-align: middle" class="text-nowrap text-center">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($data) == 0)
                    <tr>
                        <td colspan="6" class="text-sm">
                            Container doesn't exists.
                        </td>
                    </tr>
                    @else
                        @foreach ($data as $td)
                        <tr>
                            @foreach ($td as $col)
                            <td>{!!str_replace(",", "<br/>", $col)!!}</td>
                            @endforeach
                            @for($i=0;$i<$maxCol-count($td);$i++)
                                <td></td>
                            @endfor

                            <td class="text-center">
                                <div class="btn-group">
                                    <button onclick="restartContainer('{{$td[0]}}')" class="btn btn-sm btn-warning">
                                        <i class="fas fa-redo-alt"></i>
                                    </button>
                                    <button onclick="stopContainer('{{$td[0]}}')" class="btn btn-sm btn-danger">
                                        <i class="fas fa-stop"></i>
                                    </button>
                                    <button onclick="logContainer('{{$td[0]}}')" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-log">
                                        <i class="fas fa-file-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @include('Widget.log', [
        'id' => 'modal-log',
        'title' => 'Log',
        'button' => '<button type="button" class="btn btn-primary" onClick="refreshLog()">Refresh</button>'
    ])

@endsection

@push('js')
<script>
let logContainerid
function restartContainer(id){
    toastr.info("Restarting Container ...")
    $.ajax({
        url: "{{route("docker.restart", "--id--")}}".replace('--id--', id),
        success: function(resp){
            console.log(resp)
            toastr.success("Container restarted successfully")
        },
        error: function(xhr, status, e){
            console.log(e)
            toastr.warning("Container restarted abnormally")
        },
        done: function(){
            location.reload()
        }
    }).done(function(){
        setTimeout(() => {
            location.reload()
        }, 500);
    })
}

function logContainer(id){
    logContainerId=id
    return $.ajax({
        url: "{{route("docker.log", "--id--")}}".replace('--id--', id),
        success: function(resp){
            console.log(resp)
            const textarea = $('#mirror-modal-log')
            textarea.val(resp)
            textarea.trigger('change')
        },
        error: function(xhr, status, e){
            console.log(e)
            toastr.error("Error: "+xhr.statusText)
        }
    })
}

function refreshLog(){
    if (logContainerId != null){
        logContainer(logContainerId)
        .done(function(data){
            toastr.success("Log refresh successfully")
        })
        .fail(function(jqXHR, status){
            toastr.error("Log refresh failed! see console for details")
        })
    }
}

function stopContainer(id){
    toastr.info("Stoping Container ...")
    $.ajax({
        url: "{{route("docker.stop", "--id--")}}".replace('--id--', id),
        success: function(resp){
            console.log(resp)
            toastr.success("Container stoped successfully")
        },
        error: function(xhr, status, e){
            console.log(e)
            toastr.warning("Container stoped abnormally")
        },
    }).done(function(){
        setTimeout(() => {
            location.reload()
        }, 500);
    })
}
</script>
@endpush