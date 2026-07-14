<nav class="navbar navbar-expand-lg main-navbar">

    {{-- Botón Sidebar --}}
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li>
                <a href="#" class="nav-link nav-link-lg" data-toggle="sidebar" style="color: #009688;">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        {{-- Título del sistema --}}
        <span class="navbar-text font-weight-bold text-secondary d-none d-md-inline">
            Sistema de Cotizaciones
        </span>
    </form>

    {{-- Usuario --}}
    <ul class="navbar-nav navbar-right">

        <li class="dropdown">

            <a href="#"
               data-toggle="dropdown"
               class="nav-link dropdown-toggle nav-link-lg nav-link-user">

                

                <div class="d-sm-none d-lg-inline-block" style="color: #009688;">
                    Hola, <strong>{{ Auth::user()->name }}</strong>
                </div>

            </a>

            <div class="dropdown-menu dropdown-menu-right">

                <div class="dropdown-title">
                    Sesión iniciada
                </div>

                <a href="#"
                   class="dropdown-item has-icon">

                    <i class="far fa-user"></i>
                    {{ Auth::user()->name }} {{ Auth::user()->apellido_paterno }} {{ Auth::user()->apellido_materno }}
                    <br><br>
                    
                    [ {{ Auth::user()->role->nombre }} ]

                </a>

                <div class="dropdown-divider"></div>

                <a href="{{ route('logout') }}"
                   class="dropdown-item has-icon text-danger"
                   onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">

                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión

                </a>

                <form id="logout-form"
                      action="{{ route('logout') }}"
                      method="POST"
                      class="d-none">
                    @csrf
                </form>

            </div>

        </li>

    </ul>

</nav>