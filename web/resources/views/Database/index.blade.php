@extends('layout')
@section('head', 'Database')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Database</h4>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="text-center" align="middle">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($tables) == 0)
                        <tr>
                            <td colspan="6" class="text-sm">
                                Something wrong, tables not detected.
                            </td>
                        </tr>
                    @else    
                    @foreach ($tables as $table)
                    <tr>
                        <td style="vertical-align: middle">{{$table}}</td>
                        <td class="w-auto text-nowrap" align="middle">
                            <div class="btn-group text-center">
                                <a class="btn btn-sm btn-success text-sm btn-show" href="{{route('database.show', $table)}}">
                                    <i class="fas fa-eye mr-1"></i>
                                    Show
                                </a>
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