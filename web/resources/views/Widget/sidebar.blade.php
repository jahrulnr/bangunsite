<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-lightblue elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="{{asset('assets/favicon.png')}}" alt="BangunSite Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{env('APP_NAME')}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{asset('assets/icon.svg')}}" 
                class="img-circle elevation-2 bg-white" alt="User Image" referrerpolicy="no-referrer|no-referrer-when-downgrade|origin|origin-when-cross-origin|unsafe-url">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{auth()->user()?->name}}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('website.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-globe"></i>
                        <p>
                            Website
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('filemanager')}}" class="nav-link">
                        <i class="nav-icon fas fa-folder"></i>
                        <p>
                            FileManager
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('cronjob.index')}}" class="nav-link">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>
                            CronJob
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('logs')}}" class="nav-link">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>
                            Logs
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('setting')}}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Settings
                        </p>
                    </a>
                </li>
                <li class="nav-header"></li>
                <li class="nav-item">
                    <a href="{{route('lockscreen')}}" class="nav-link">
                        <i class="nav-icon fas fa-lock"></i>
                        <p>
                            Sign out
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
<!-- /.sidebar -->
</aside>