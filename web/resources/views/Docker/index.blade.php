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
                                <button onclick="restartContainer('{{$td[0]}}')" class="btn btn-sm btn-primary">Restart</button>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
<script>
    function restartContainer(id){
        console.log(id)
        $.ajax({
            url: "{{route("docker.restart", "--id--")}}".replace('--id--', id),
            success: function(resp){
                console.log(resp)
            },
            error: function(xhr, status, e){
                console.log(e)
            }
        })
    }
</script>
@endpush