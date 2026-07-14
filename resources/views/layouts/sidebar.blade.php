<div class="main-sidebar">
    <aside id="sidebar-wrapper">

        <div class="sidebar-brand py-3">
            <a href="{{ url('/home') }}">
                <img src="{{ asset('stisla/assets/img/Logo.jpg') }}" alt="logo" style="max-width:170px;">
            </a>
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ url('/home') }}">
                <img src="{{ asset('stisla/assets/img/Logo.jpg') }}" width="35">
            </a>
        </div>
        <br><br>
        <ul class="sidebar-menu">

            @if(Auth::check() && Auth::user()->role)

                {{-- ================= ADMINISTRADOR ================= --}}
                @if(Auth::user()->role->nombre === 'Administrador')

                    <li class="menu-header">Administración</li>

                    <li class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('admin/clientes*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.clientes.index') }}">
                            <i class="fas fa-user-friends"></i>
                            <span>Clientes</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('admin/proveedores*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.proveedores.index') }}">
                            <i class="fas fa-truck"></i>
                            <span>Proveedores</span>
                        </a>
                    </li>

                    <li class="menu-header">Inventario</li>

                    <li class="{{ request()->is('admin/almacenes*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.almacenes.index') }}">
                            <i class="fas fa-warehouse"></i>
                            <span>Almacenes</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('admin/entradas*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.entradas.index') }}">
                            <i class="fas fa-arrow-down"></i>
                            <span>Entradas</span>
                        </a>
                    </li>

                    {{-- INSUMOS --}}
                    <li class="nav-item dropdown {{
                        request()->is('admin/tipo-insumos*') ||
                        request()->is('admin/insumos*') ? 'active' : ''
                    }}">

                        <a href="#" class="nav-link has-dropdown">
                            <i class="fas fa-boxes"></i>
                            <span>Insumos</span>
                        </a>

                        <ul class="dropdown-menu">

                            <li class="{{ request()->is('admin/tipo-insumos*') ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ route('admin.tipo-insumos.index') }}">
                                    Tipos de Insumo
                                </a>
                            </li>

                            <li class="{{ request()->is('admin/insumos*') ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ route('admin.insumos.index') }}">
                                    Insumos
                                </a>
                            </li>

                        </ul>

                    </li>

                    {{-- PRODUCTOS --}}
                    <li class="nav-item dropdown {{
                        request()->is('admin/tipo-productos*') ||
                        request()->is('admin/productos*') ? 'active' : ''
                    }}">

                        <a href="#" class="nav-link has-dropdown">
                            <i class="fas fa-box-open"></i>
                            <span>Productos</span>
                        </a>

                        <ul class="dropdown-menu">

                            <li class="{{ request()->is('admin/tipo-productos*') ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ route('admin.tipo-productos.index') }}">
                                    Tipos de Producto
                                </a>
                            </li>

                            <li class="{{ request()->is('admin/productos*') ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ route('admin.productos.index') }}">
                                    Productos
                                </a>
                            </li>

                        </ul>

                    </li>

                    <li class="menu-header">Cotización</li>

                    <li class="{{ request()->is('admin/cotizaciones') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.cotizaciones.index') }}">
                            <i class="fas fa-list"></i>
                            <span>Cotizaciones</span>
                        </a>
                    </li>
                    <li class="{{ request()->is('admin/cotizaciones/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.cotizaciones.create') }}">
                            <i class="fas fa-calculator"></i>
                            <span>Crear Cotización</span>
                        </a>
                    </li>

                {{-- ================= ALMACÉN ================= --}}
                @elseif(in_array(Auth::user()->role->nombre,['Almacén','Almacen']))

                    <li class="menu-header">Inventario</li>

                    <li class="{{ request()->is('admin/proveedores*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.proveedores.index') }}">
                            <i class="fas fa-truck"></i>
                            <span>Proveedores</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('admin/almacenes*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.almacenes.index') }}">
                            <i class="fas fa-warehouse"></i>
                            <span>Almacenes</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('admin/entradas*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.entradas.index') }}">
                            <i class="fas fa-arrow-down"></i>
                            <span>Entradas</span>
                        </a>
                    </li>

                {{-- ================= COTIZADOR ================= --}}
                @elseif(Auth::user()->role->nombre === 'Cotizador')

                    <li class="menu-header">Catálogos</li>

                    <li class="nav-item dropdown {{
                        request()->is('admin/tipo-insumos*') ||
                        request()->is('admin/insumos*') ? 'active' : ''
                    }}">

                        <a href="#" class="nav-link has-dropdown">
                            <i class="fas fa-boxes"></i>
                            <span>Insumos</span>
                        </a>

                        <ul class="dropdown-menu">

                            <li class="{{ request()->is('admin/insumos*') ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ route('admin.insumos.index') }}">
                                    Insumos
                                </a>
                            </li>

                        </ul>

                    </li>

                    <li class="nav-item dropdown {{
                        request()->is('admin/productos*') ||
                        request()->is('admin/tipo-productos*') ? 'active' : ''
                    }}">

                        <a href="#" class="nav-link has-dropdown">
                            <i class="fas fa-box-open"></i>
                            <span>Productos</span>
                        </a>

                        <ul class="dropdown-menu">

                            <li class="{{ request()->is('admin/productos*') ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ route('admin.productos.index') }}">
                                    Productos
                                </a>
                            </li>

                        </ul>

                    </li>

                    <li class="{{ request()->is('admin/clientes*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.clientes.index') }}">
                            <i class="fas fa-user-friends"></i>
                            <span>Clientes</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('admin/cotizaciones') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.cotizaciones.index') }}">
                            <i class="fas fa-list"></i>
                            <span>Cotizaciones</span>
                        </a>
                    </li>

                    <li class="{{ request()->is('admin/cotizaciones/create') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.cotizaciones.create') }}">
                            <i class="fas fa-calculator"></i>
                            <span>Crear Cotización</span>
                        </a>
                    </li>

                @endif

            @endif

        </ul>

    </aside>
</div>