<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#"></a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item px-3">
            <a class="nav-link" href="#">Escritorio</a>
        </li>
        <li class="nav-item px-3">
            <a class="nav-link" href="#">Configuraciones</a>
        </li>
    </ul>
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <img src="vendors/img/avatars/6.jpg" class="img-avatar" alt="admin@bootstrapmaster.com">
                <span class="d-md-down-none">{{Auth::user()->usuario}}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="dropdown-item" href="{{ route('logout') }}" 
            onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
            <i class="fa fa-lock"></i> Cerrar sesión</a>
            <form action="{{ route('logout') }}" id="logout-form" style="display: none" method="post">
                @csrf
            </form>
        </li>
    </ul>
</header>