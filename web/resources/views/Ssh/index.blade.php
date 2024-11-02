@extends('layout')
@section('head', "Ssh")

@section('content')
<div class="card card-body">
    <div class="table-responsive">
        <button class="btn btn-primary mb-3" data-toggle="modal" onclick="addSsh()" data-target="#add-ssh">
            {!! setIcon('fas fa-sm fa-plus mr-2') !!} New
        </button>
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Host</th>
                    <th>User</th>
                    <th>Port</th>
                    <th class="text-center align-middle">Action</th>
                </tr>
            </thead>
            <tbody>
                @if ($accounts->count() == 0)
                <tr>
                    <td colspan="4" class="text-sm">
                        Ssh account not available, 
                        <a href="#" class="text-success font-weight-bold" data-toggle="modal" onclick="addSsh()" data-target="#add-ssh">
                            create new account.
                        </a>
                    </td>
                </tr>
                @endif
                @foreach ($accounts as $account)
                <tr>
                    <td class="align-middle">
                        {{$account["host"]}}
                    </td>
                    <td class="align-middle">
                        {{$account["user"]}}
                    </td>
                    <td class="align-middle">
                        {{$account["port"]}}
                    </td>
                    <td align="middle" class="text-center">
                        <div class="btn-group">
                            <a class="btn btn-success" href="{{route("ssh.connect", $account->id)}}" target="_blank">
                                <span class="fas fa-play"></span>
                            </a>
                            <button class="btn btn-primary" onclick="editSsh(this)">
                                <data style="display: none">{!! json_encode($account) !!}</data>
                                <span class="fas fa-pencil-alt"></span>
                            </button>
                            <a class="btn btn-danger" href="{{route("ssh.delete", ["id"=>$account["id"]])}}" onclick="return confirm('Are you sure to delete this mount config?')">
                                <span class="fas fa-trash"></span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('footer')
    <form action="{{route('ssh.add')}}" id="ssh-form" method="POST">
        @include('Widget.modal', [
            'id' => 'add-ssh',
            'title' => 'New Ssh Account',
            'body' => view('Ssh.form'),
            'button' => '<button class="btn btn-primary" type="submit">Add</button>'
        ])
    </form>
@endpush

@push("js")
<script>
    const addSsh = function(){
        $("#ssh-form").attr("action", "{{route("ssh.add")}}")
        $("input[name='id']").val("")
        $("input[name='host']").val("")
        $("input[name='port']").val("")
        $("input[name='user']").val("")
        $("input[name='pass']").val("")
        $("input[name='pass']").attr("required", true)
        $("#add-ssh button[type='submit']").text("Add")

    }

    const editSsh = function(el) {
        const data = JSON.parse(
            $(el).find("data").text()
        )
        $("#ssh-form").attr("action", "{{route("ssh.update")}}")
        $("input[name='id']").val(data.id)
        $("input[name='host']").val(data.host)
        $("input[name='port']").val(data.port)
        $("input[name='user']").val(data.user)
        $("input[name='pass']").val("")
        $("input[name='pass']").attr("required", false)
        $("#add-ssh button[type='submit']").text("Update")

        $("#add-ssh").modal("show")
    }
</script>
@endpush
