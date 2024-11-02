@extends('layout')
@section('head', "Website")

@section('content')
<div class="card card-body">
    <div class="table-responsive">
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#create-site">
            {!! setIcon('fas fa-sm fa-plus mr-2') !!} New
        </button>
        <button class="btn btn-primary mb-3" id="show-nginx-modal" data-toggle="modal" data-target="#nginx-editor-modal">
            {!! setIcon('fas fa-sm fa-project-diagram mr-2') !!} Nginx
        </button>
        <button class="btn btn-primary mb-3" id="show-defconf-modal" data-toggle="modal" data-target="#defconf-editor-modal">
            {!! setIcon('fas fa-sm fa-location-arrow mr-2') !!} Default Web
        </button>
        <button class="btn btn-info mb-3 ml-3">
            {!! setIcon('fas fa-sm fa-info-circle mr-2') !!} About
        </button>

        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Site Name</th>
                    <th>Domain</th>
                    <th>Path</th>
                    <th>SSL</th>
                    <th>Running</th>
                    <th align="middle" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @if (count($website) == 0)
                    <tr>
                        <td colspan="6" class="text-sm">
                            Website is not available, 
                            <a href="#" class="text-success font-weight-bold" data-toggle="modal" data-target="#create-site">
                                create new site.
                            </a>
                        </td>
                    </tr>
                @else
                @foreach ($website as $data)
                    <tr>
                        <td style="vertical-align: middle">
                            <span class="text-light font-weight-bold text-sm">
                                {{$data->name}}
                            </span>
                        </td>
                        <td style="vertical-align: middle">
                            <a href="http://{{$data->domain}}" target="_blank" rel="noopener noreferrer" class="text-light text-sm">
                                {{$data->domain}}
                            </a>
                        </td>
                        <td style="vertical-align: middle">
                            <a href="{{route('filemanager')}}?path={{$data->path}}" target="_blank" rel="noopener noreferrer" class="text-light text-sm">
                                {{strpos($data->path, 30) ? substr($data->path, 27).'...' : $data->path}}
                            </a>
                        </td>
                        <td align="middle" style="vertical-align: middle">
                            @php
                                $isEnabled = App\Libraries\SSL::checkSSL($data->domain);
                            @endphp
                            <span class="text-{{$isEnabled ? "success":"danger"}} text-sm">
                                {!! setIcon($isEnabled ? 'fas fa-check-circle' : 'fas fa-times-circle') !!}
                            </span>
                        </td>
                        <td align="middle" style="vertical-align: middle">
                            <span class="text-{{$data->active ? "success":"danger"}} text-sm">
                                {!! setIcon($data->active ? 'fas fa-check-circle' : 'fas fa-times-circle') !!}
                            </span>
                        </td>
                        <td align="middle" class="w-auto text-nowrap">
                            <data style="display: none" id="{{$data->domain}}">{!! json_encode($data) !!}</data>
                            <div class="btn-group" data-site="{{$data->domain}}">
                                <a href="#" class="btn btn-sm btn-warning" data-act="site-disable">
                                    {{$data->active ? 'Disable' : 'Enable'}}
                                </a>
                                <a href="{{route('website.edit', $data->domain)}}" class="btn btn-sm btn-primary" data-act="site-config">Config</a>
                                <a href="#" class="btn btn-sm btn-danger" data-act="site-delete">Delete</a>
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
    <form action="{{route('website.store')}}" method="POST">
        @include('Widget.modal', [
            'id' => 'create-site',
            'title' => 'New Site',
            'body' => view('Website.form'),
            'button' => '<button class="btn btn-primary" type="submit">Create</button>'
        ])
    </form>
    <form action="{{route('website.updateNginx')}}" method="POST">
        @include('Website.nginx')
    </form>
    <form action="{{route('website.updateConfig', 'default')}}" method="POST">
        @include('Website.defaultconf')
    </form>
    <form action="{{route('website.enableSite', 'example')}}" method="POST">
        @include('Website.enablesite')
    </form>
@endpush