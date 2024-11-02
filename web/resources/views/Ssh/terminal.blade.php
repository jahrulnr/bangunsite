@extends('layout')
@section('head', "Terminal")

@section('content')
<span style="display: none;" id="ssh-endpoint">{{$endpoint}}</span>
<div id="terminal"></div>
@endsection

@push('css')
<link rel="stylesheet" href="{{asset("assets/css/xterm.css")}}" />
<style>
    .terminal.xterm {
        padding: 10px;
    }
</style>
@endpush

@push('js')
<script src="{{asset("assets/js/xterm.js")}}"></script>
<script src="{{asset("assets/js/ssh.js")}}" type="text/javascript"></script>
@endpush