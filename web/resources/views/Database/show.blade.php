@extends('layout')
@section('head', 'Show'. $name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Table {{$name}}</h4>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        @foreach ($cols as $col)
                        <th>{{$col}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @if (count($rows) == 0)
                        <tr>
                            <td colspan="6" class="text-sm">
                                Empty
                            </td>
                        </tr>
                    @else    
                    @foreach($rows as $row)
                    <tr>
                        @foreach($row as $col => $value)
                        <td style="vertical-align: middle">{{$value}}</td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection