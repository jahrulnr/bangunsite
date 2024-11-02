@extends('layout')
@section('head', "Mount Manager")

@section('content')
<div class="card card-body">
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Device</th>
                    <th>Dir</th>
                    <th>Type</th>
                    <th>Options</th>
                    <th>Dump</th>
                    <th>FSCK</th>
                    <th class="text-center align-middle">Action</th>
                </tr>
            </thead>
            <tbody>               
                @foreach ($fstabs as $data)
                <tr class="p-0 m-0"><td colspan="8" class="p-0 m-0">
                    <form action="{{route("mount.update")}}" method="post">
                        @csrf
                        <input type="hidden" name="mount_device" value="{{$data[0]}}">
                        <input type="hidden" name="mount_dir" value="{{$data[1]}}">
                        <table class="table table-borderless m-0">
                            <td class="align-middle">
                                {{$data[99]}}
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Device" required name="device" value="{{$data[0]}}">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Dir" required name="dir" value="{{$data[1]}}">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Type" required name="type" value="{{$data[2]}}">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Options" required name="option" value="{{$data[3]}}">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Dump" required name="dump" value="{{$data[4]}}">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="FSCK" required name="fsck" value="{{$data[5]}}">
                            </td>
                            <td align="middle" class="text-center">
                                <div class="btn-group">
                                    <a class="btn btn-success" href="{{route("mount.enable")}}?device={{$data[0]}}&dir={{$data[1]}}">
                                    @if($data[99] == "Disabled")
                                        <span class="fas fa-play"></span>
                                    @else
                                        <span class="fas fa-stop-circle"></span>
                                    @endif
                                    </a>
                                    <button class="btn btn-primary">
                                        <span class="fas fa-save"></span>
                                    </button>
                                    <a class="btn btn-danger" href="{{route("mount.destroy")}}?device={{$data[0]}}&dir={{$data[1]}}" onclick="return confirm('Are you sure to delete this mount config?')">
                                        <span class="fas fa-trash"></span>
                                    </a>
                                </div>
                            </td>
                        </table>
                    </form>
                </td></tr>
                @endforeach
                <tr class="p-0 m-0"><td colspan="8" class="p-0 m-0">
                    <form action="" method="post">
                        @csrf
                        <table class="table table-borderless m-0">
                            <td class="align-middle" style="white-space: nowrap">
                                Add New
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Device" required name="device" value="">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Dir" required name="dir" value="">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Type" required name="type" value="">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Options" required name="option" value="">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="Dump" required name="dump" value="">
                            </td>
                            <td class="align-middle">
                                <input type="text" class="form-control form-control-sm" placeholder="FSCK" required name="fsck" value="">
                            </td>
                            <td align="middle" class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-primary">
                                        <span class="fas fa-plus"></span>
                                    </button>
                                </div>
                            </td>
                        </table>
                    </form>
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection