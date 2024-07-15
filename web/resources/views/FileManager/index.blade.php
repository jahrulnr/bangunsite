@extends('layout')
@section('head', 'File Manager')

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
            <button class="btn btn-default btn-sm" onclick="window.location.reload()">
                {!! setIcon('fas fa-sync-alt text-sm') !!}
            </button>
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#new-object">
                New
            </button>
        </div>
    </div>
    <ul class="list-group">
        @foreach ($browse as $list)
            @php
                $is_link = $list['link'] !== true;
                $shortlink = strlen($list['link']) > 20;
                $linked = $is_link && $shortlink 
                    ? '-> <span class="text-primary">...'. substr($list['link'], -20) ."</span>"
                    : ($is_link 
                    ? '-> <span class="text-primary">...'. $list['link'] ."</span>"
                    : '');
                
                $filepath = str_replace('//', '/', $fullPath.'/'.$list['name']);
                $mime = is_file($filepath) ? mime_content_type($filepath) : false;
                $perms = (int) substr(sprintf('%o', fileperms($filepath)), -4);
            @endphp
            <li class="list-group-item">
                <div class="row">
                    <div class="col-12 col-md-auto" {!! $list['type'] == 'directory' ? "onclick=\"window.location.assign('".route('filemanager')."?path={$filepath}')\" style=\"cursor: pointer\"" : '' !!}>
                        <span class="text-sm">
                            {!! $list['icon'] !!}
                            @if ($list['name'] != '..') 
                            [{{$list['permission']}}]
                            @endif
                        </span>
                        <span class="{{ $list['type'] == 'file' && preg_match('/77(7|5)/', $list['permission'])?'text-success':'' }}">
                            <span class="text-sm">{{ $list['name'] }} {!! $linked !!}</span>
                            {{-- {{$mime}} --}}
                        </span>
                    </div>
                    <div class="col-12 col-md-auto my-auto ml-auto">
                        @if ($list['name'] != '..')
                            @if ($list['type'] == 'file')
                            <span class="text-sm">[{{ $list['size'] }}]</span>
                            @if (str_contains($mime, 'shellscript'))
                            <a href="#execute" class="execute text-sm text-white" data-file="{{$list['name']}}">Execute</a> |
                            @endif
                            @if (str_starts_with($mime, 'text') || str_contains($mime, 'json'))
                            <a href="#readfile" class="readfile text-sm text-white" data-file="{{$list['name']}}">View</a> |
                            @endif
                            @endif
                            <a href="#copyfile" class="copyfile text-sm text-white" data-file="{{$list['name']}}">Copy</a> |
                            <a href="#chmodfile" class="chmodfile text-sm text-white" data-file="{{$list['name']}}">Chmod</a> |
                            <a href="#deletefile" class="deletefile text-sm text-white" data-file="{{$list['name']}}">Delete</a>
                            <data style="display:none">{!! htmlspecialchars(json_encode($list), ENT_NOQUOTES) !!}</data>                           
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
    @include('FileManager.read')

    <form action="{{route('filemanager.action')}}?path={{$fullPath}}" method="POST" class="">
    @include('FileManager.action')
    </form>
@endpush

@push('js')
    <script>
        $(document).ready(function(){
            $(".execute").click(function(){
                const data = JSON.parse($(this).parent().find('data').text())
                $.ajax({
                    url: '{{route('filemanager.action')}}?path={{$fullPath}}',
                    type: 'PATCH',
                    data: {
                        _token: '{{csrf_token()}}',
                        name: data.name,
                        type: 'execute'
                    },
                    success: function(resp){
                        toastr.success(resp)
                    },
                    error: function(xhr, err, errThrow){
                        toastr.error(xhr.responseText)
                    }
                })
            })
        })
    </script>
@endpush