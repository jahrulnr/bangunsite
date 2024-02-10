@extends('layout')
@section('head', 'FileManager')

@push('css')
<style>
    .list-group-item > i {
        min-width: 16px;
        align-self: center
    }
</style>
@endpush

@section('content')
<div class="card card-body">
    <div class="d-flex justify-content-between mb-3">
        <div class="my-auto">
            Path: <span id="path">{{$fullPath}}</span>
        </div>
        <div class="btn-group">
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#new-object">
                New
            </button>
        </div>
    </div>
    <ul class="list-group">
        @foreach ($browse as $list)
            @php
                $is_link = $list[4] !== true;
                $shortlink = strlen($list[4]) > 20;
                $linked = $is_link && $shortlink 
                    ? '-> <span class="text-primary">...'. substr($list[4], -20) ."</span>"
                    : ($is_link 
                    ? '-> <span class="text-primary">...'. $list[4] ."</span>"
                    : '')
            @endphp
            <li class="list-group-item">
                <div class="row">
                    <div class="col-12 col-md-auto">
                        <span class="text-sm">{!! $list[2] !!} [{{$list[3]}}]</span>
                        <span class="{{ $list[1] == 'file' && preg_match('/77(7|5)/', $list[3])?'text-success':'' }}">
                            @if ($list[1] == 'directory')
                            <a href="{{route('filemanager')}}?path={{str_replace('//', '/', $fullPath.'/'.$list[0])}}" class="text-sm" style="color: unset !important">
                                {{ $list[0] }} {!! $linked !!}
                            </a>
                            @else
                            <span class="text-sm">{{ $list[0] }} {!! $linked !!}</span>
                            @endif
                        </span>
                    </div>
                    <div class="col-12 col-md-auto my-auto ml-auto">
                        @if($list[1] == 'file')
                        <a href="#readfile" class="readfile text-sm text-white" data-file="{{$list[0]}}">View</a> |
                        <a href="#copyfile" class="copyfile text-sm text-white" data-file="{{$list[0]}}">Copy</a> |
                        <a href="#chmodfile" class="chmodfile text-sm text-white" data-file="{{$list[0]}}">Chmod</a> |
                        <a href="#deletefile" class="deletefile text-sm text-white" data-file="{{$list[0]}}">Delete</a>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection

@push('footer')
    @include('FileManager.new')
@endpush