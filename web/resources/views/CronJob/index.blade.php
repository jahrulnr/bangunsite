@extends('layout')
@section('head', 'Settings')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>CronJobs</h4>
        </div>
        <div class="card-body table-responsive">
            <div class="mb-3 text-sm">
                <button class="btn btn-sm btn-primary mr-1" onclick="createCron()">
                    <i class="fas fa-plus mr-1"></i>
                    New
                </button>
            </div>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Command</th>
                        <th>Run Every</th>
                        <th>Last Executed</th>
                        <th class="text-center" align="middle">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($crons) == 0)
                        <tr>
                            <td colspan="6" class="text-sm">
                                Cron is not available, 
                                <a href="#" class="text-success font-weight-bold" onclick="createCron()">
                                    create new cron.
                                </a>
                            </td>
                        </tr>
                    @else    
                    @foreach ($crons as $cron)
                    <tr>
                        <td style="vertical-align: middle">{{$cron->name}}</td>
                        <td style="vertical-align: middle;">
                            <code class="text-nowrap d-block" style="overflow: hidden; text-overflow: ellipsis; max-width: 400px">
                                {{$cron->payload}}
                            </code>
                        </td>
                        <td style="vertical-align: middle">{{ucfirst($cron->run_every)}}</td>
                        <td style="vertical-align: middle">{{$cron->executed_at}}</td>
                        <td class="w-auto text-nowrap" align="middle">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-success text-sm btn-execute" data-id="{{$cron->id}}">
                                    <i class="fas fa-play mr-1"></i>
                                    Execute
                                </button>
                                <button class="btn btn-sm btn-primary text-sm btn-edit" data-id="{{$cron->id}}">
                                    <i class="fas fa-pencil-alt mr-1"></i>
                                    Update
                                </button>
                                <button class="btn btn-sm btn-danger text-sm btn-delete" data-id="{{$cron->id}}">
                                    <i class="fas fa-trash mr-1"></i>
                                    Delete
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
@endsection

@push('footer')
@include('Widget.modal-form', [
    'id' => 'form',
    'method' => 'POST',
    'action' => '#',
    'title' => 'Create Cron',
    'body' => <<<HTML
            <input type="hidden" name="_method" value="">
            <div class="form-group mb-3">
                <label>Name</label>
                <input class="form-control" name="name" placeholder="LetsEncrypt" required/>
            </div>
            <div class="form-group mb-3">
                <label>Command</label>
                <input class="form-control" name="payload" placeholder="echo 'Hallo'" required/>
            </div>
            <div class="form-group mb-3">
                <label>Run Every</label>
                <select class="form-control" name="run_every" required>
                    <option value="-">Not Set</option>
                    <option value="min">Minute</option>
                    <option value="hour">Hour</option>
                    <option value="day">Day</option>
                    <option value="month">Month</option>
                </select>
            </div>
        HTML,
    'button' => '<button class="btn btn-primary">Save</button>'
])
@include('Widget.log', [
    'id' => 'log',
    'title' => 'Logs',
    'body' => "",
])
@include('Widget.modal-form', [
    'id' => 'deleteForm',
    'method' => 'POST',
    'title' => 'Delete Cron',
    'body' => '<input type="hidden" name="_method" value="DELETE"> '
        .'Are you sure to delete <code></code> from cronjobs?',
    'button' => '<button class="btn btn-danger" onclick=""><i class="fas fa-trash mr-1"></i> Delete</button>'
])
@endpush

@push('js')
<script>
    const form = $('#form')
    const createCron = function(){
        form.attr('action', "{{route('cronjob.store')}}")
        form.find('input[name="_method"]').val('')
        form.find('.modal-title').text('Create Cron')
        form.modal('show')
    }

    let clicked = false
    let start = "true"
    let progress = "false"
    $('.btn-execute').click(function(e){
        const id = $(this).attr('data-id')
        const modal = $('#log')
        e.preventDefault()
        modal.modal('show')

        let textarea = $('#mirror-log')
        if (clicked == false)
            clicked = setInterval(() => {
                $.ajax({
                    url: ('{{route("cronjob.run", '--id--')}}?start='+start+'&progress='+progress).replace('--id--', id),
                    type: 'post',
                    data: '_token={{csrf_token()}}',
                    success: function(resp){
                        start = "false"
                        progress = "true"
                        textarea.val(resp).trigger('change')

                        if(resp.includes("-- Task Done --")){
                            clearInterval(clicked)
                            clicked = false
                            start = "true"
                            progress = "false"
                        }
                    }
                })
            }, 1000);
        

        modal.on('hidden.bs.modal', function(){
            if (clicked != false){
                clearInterval(clicked)
                clicked = false
            }
        })
    })

    $('.btn-edit').click(function(){
        const id = $(this).attr('data-id')
        form.attr('action', "{{route('cronjob.update', '--id--')}}".replace('--id--', id))
        form.find('input[name="_method"]').val('PUT')
        form.find('.modal-title').text('Update Cron')
        form.modal('show')

        const tr = $(this).parents('tr')
        const name = tr.find('td')[0].innerText
        const payload = tr.find('code')[0].innerText
        const run_every = tr.find('td')[2].innerText.toLowerCase()
        form.find('input[name="name"]').val(name)
        form.find('input[name="payload"]').val(payload)
        form.find('select[name="run_every"]').val(run_every)
    })

    $('.btn-delete').click(function(){
        const id = $(this).attr('data-id')
        const tr = $(this).parents('tr')
        const name = tr.find('td')[0].innerText
        const formDelete = $('#deleteForm')
        formDelete.attr('action', "{{route('cronjob.destroy', '--id--')}}".replace('--id--', id))
        formDelete.find('.modal-title').text('Delete Cron')
        formDelete.find('.modal-body code').text(name)
        formDelete.modal('show')
    })
</script>
@endpush