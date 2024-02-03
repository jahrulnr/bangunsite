@extends('layout')
@section('head', "Website")

@section('content')
<div class="card card-body">
    <div class="table-responsive">
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#create-site">
            {!! setIcon('fas fa-sm fa-plus mr-2') !!} New
        </button>
        <button class="btn btn-primary mb-3">
            {!! setIcon('fas fa-sm fa-project-diagram mr-2') !!} Nginx
        </button>
        <button class="btn btn-primary mb-3">
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
                    <th>Status</th>
                    <th align="middle" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @if (count($website) == 0)
                    <tr>
                        <td colspan="6">
                            Website is not available, 
                            <a href="#" class="text-success font-weight-bold" data-toggle="modal" data-target="#create-site">
                                create new site.
                            </a>
                        </td>
                    </tr>
                @else                    
                @foreach ($website as $data)
                    <tr>
                        <td>
                            <span class="text-light font-weight-bold">
                                {{$data->name}}
                            </span>
                        </td>
                        <td>
                            <a href="#" target="_blank" rel="noopener noreferrer" class="text-light">
                                {{$data->domain}}
                            </a>
                        </td>
                        <td>
                            <a href="#path={{$data->path}}" target="_blank" rel="noopener noreferrer" class="text-light">
                                {{strpos($data->path, 30) ? substr($data->path, 27).'...' : $data->path}}
                            </a>
                        </td>
                        <td>
                            <span class="text-{{$data->ssl ? "success":"danger"}}">
                                {{$data->ssl ? "ACTIVE" : "DISABLED"}}
                            </span>
                        </td>
                        <td>
                            <span class="text-{{$data->active ? "success":"danger"}}">
                                {{$data->active ? "ACTIVE" : "DISABLED"}}
                            </span>
                        </td>
                        <td align="middle" class="w-auto text-nowrap">
                            <data style="display: none" id="{{$data->domain}}">{!! json_encode($data) !!}</data>
                            <div class="btn-group" data-site="{{$data->domain}}">
                                <a href="#" class="btn btn-sm btn-primary" data-act="site-config">Config</a>
                                <a href="#" class="btn btn-sm btn-warning" data-act="site-disable">Disable</a>
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
@endpush