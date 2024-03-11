@extends('layout')
@section('head', 'Settings')

@push('css')
@css(asset('assets/plugins/codemirror/codemirror.css'))
@css(asset('assets/plugins/codemirror/theme/dracula.css'))
@css(asset('assets/plugins/codemirror/addon/dialog/dialog.css'))
@css(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.css'))
@endpush
@push('js')
@js(asset('assets/plugins/codemirror/codemirror.js'))
@js(asset('assets/plugins/codemirror/keymap/sublime.js'))
@js(asset('assets/plugins/codemirror/addon/dialog/dialog.js'))
@js(asset('assets/plugins/codemirror/addon/search/searchcursor.js'))
@js(asset('assets/plugins/codemirror/addon/search/search.js'))
@js(asset('assets/plugins/codemirror/addon/display/autorefresh.js')))
@js(asset('assets/plugins/codemirror/addon/scroll/annotatescrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/matchesonscrollbar.js'))
@js(asset('assets/plugins/codemirror/addon/search/jump-to-line.js'))
@endpush

@section('content')
    <div id="config-card">
        <div class="card">
            <div class="card-header" id="profile-tab">
                <h5 class="mb-0">
                    <span data-toggle="collapse" data-target="#profile" aria-expanded="true" aria-controls="profile">
                    Profile
                    </span>
                </h5>
            </div>
        
            <div id="profile" class="collapse" aria-labelledby="profile-php" data-parent="#config-card">
                <form action="{{route('setting.profile.update')}}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$user->id}}">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{$user->name}}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{$user->email}}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" value="" autocomplete="new-password">
                        </div>
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="php-config">
                <h5 class="mb-0">
                    <span data-toggle="collapse" data-target="#phpconfig" aria-expanded="true" aria-controls="phpconfig">
                    PHP Config
                    </span>
                </h5>
            </div>
        
            <div id="phpconfig" class="collapse" aria-labelledby="php-config" data-parent="#config-card">
                <form action="{{route('setting.php.update')}}" method="post">
                    @csrf
                    <div class="card-body">
                        <textarea name="php-config" id="php-editor">{{$phpConfig}}</textarea>
                        @push('js')
                        <script>
                            var phpEditor = CodeMirror.fromTextArea(document.getElementById('php-editor'), {
                                lineNumbers: true,
                                keymap: 'sublime',
                                theme: 'dracula',
                                autoRefresh: true,
                                extraKeys: {"Alt-F": "findPersistent"}
                            });
                            $('#php-config').click(function(){
                                setTimeout(() => {
                                    phpEditor.refresh()
                                }, 200);
                            })
                        </script>
                        @endpush
                        <div class="d-flex justify-content-end my-3">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="fpm-config">
                <h5 class="mb-0">
                    <span data-toggle="collapse" data-target="#fpmconfig" aria-expanded="true" aria-controls="fpmconfig">
                    FPM Config
                    </span>
                </h5>
            </div>
        
            <div id="fpmconfig" class="collapse" aria-labelledby="fpm-config" data-parent="#config-card">
                <form action="{{route('setting.fpm.update')}}" method="post">
                    @csrf
                    <div class="card-body">
                        <textarea name="fpm-config" id="fpm-editor">{{$fpmConfig}}</textarea>
                        @push('js')
                        <script>
                            var fpmEditor = CodeMirror.fromTextArea(document.getElementById('fpm-editor'), {
                                lineNumbers: true,
                                keymap: 'sublime',
                                theme: 'dracula',
                                autoRefresh: true,
                                extraKeys: {"Alt-F": "findPersistent"}
                            });
                            $('#fpm-config').click(function(){
                                setTimeout(() => {
                                    fpmEditor.refresh()
                                }, 200);
                            })
                        </script>
                        @endpush
                        <div class="d-flex justify-content-end my-3">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="pool-config">
                <h5 class="mb-0">
                    <span data-toggle="collapse" data-target="#poolconfig" aria-expanded="true" aria-controls="poolconfig">
                    Pool Config
                    </span>
                </h5>
            </div>
        
            <div id="poolconfig" class="collapse" aria-labelledby="pool-config" data-parent="#config-card">
                <form action="{{route('setting.pool.update')}}" method="post">
                    @csrf
                    <div class="card-body">
                        <textarea name="pool-config" id="pool-editor">{{$poolConfig}}</textarea>
                        @push('js')
                        <script>
                            var poolEditor = CodeMirror.fromTextArea(document.getElementById('pool-editor'), {
                                lineNumbers: true,
                                keymap: 'sublime',
                                theme: 'dracula',
                                autoRefresh: true,
                                extraKeys: {"Alt-F": "findPersistent"}
                            });
                            $('#pool-config').click(function(){
                                setTimeout(() => {
                                    poolEditor.refresh()
                                }, 200);
                            })
                        </script>
                        @endpush
                        <div class="d-flex justify-content-end my-3">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="about-php">
                <h5 class="mb-0">
                    <span data-toggle="collapse" data-target="#aboutphp" aria-expanded="true" aria-controls="aboutphp">
                    About PHP
                    </span>
                </h5>
            </div>
        
            <div id="aboutphp" class="collapse show" aria-labelledby="about-php" data-parent="#config-card">
                <form action="{{route('setting.profile.update', $user->id)}}" method="post">
                    @csrf
                    <div class="card-body">
                        <code class="text-sm">@php
                            echo trim(str_replace(["  ", "\n"], ['', '<br/>'], shell_exec('php -v')));
                        @endphp</code>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
