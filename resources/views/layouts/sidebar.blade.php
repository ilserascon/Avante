<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="{{ url('/home') }}"><img src="{{ asset('stisla/assets/img/Logo.jpg') }}" alt="logo" width="50%"></a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="{{ url('/home') }}"></a>
    </div>
    <ul class="sidebar-menu">
      <li class="menu-header" style="color: #1a8683">Menú</li>
      @if (Auth::check() && Auth::user()->role && Auth::user()->role->nombre === 'Administrador')
        <li class="{{ request()->is('admin/users*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> <span>Usuarios</span></a>
        </li>
        <li class="{{ request()->is('admin/clientes*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.clientes.index') }}">
            <i class="fas fa-address-book"></i> <span>Clientes</span>
          </a>
        </li>
        <li class="{{ request()->is('admin/proveedores*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.proveedores.index') }}">
            <i class="fas fa-briefcase"></i> <span>Proveedores</span>
          </a>
        </li>
        <li class="{{ request()->is('admin/productos*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.productos.index') }}">
            <i class="fas fa-box"></i> <span>Productos</span>
          </a>
        </li>
        <li class="{{ request()->is('admin/almacenes*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.almacenes.index') }}">
            <i class="fas fa-warehouse"></i> <span>Almacenes</span>
          </a>
        </li>
        <li class="{{ request()->is('admin/tipo-insumos*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.tipo-insumos.index') }}">
            <i class="fas fa-cogs"></i> <span>Tipos de Insumo</span>
          </a>
        </li>
        <li class="{{ request()->is('admin/insumos*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.insumos.index') }}">
            <i class="fas fa-boxes"></i> <span>Insumos</span>
          </a>    
        </li>
        <li class="{{ request()->is('admin/entradas*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.entradas.index') }}">
            <i class="fas fa-boxes"></i> <span>Entradas</span>
          </a>    
        </li>
        <li class="{{ request()->is('admin/cotizaciones*') ? 'active' : '' }}">
          <a class="nav-link" href="{{ route('admin.cotizaciones.index') }}">
            <i class="fas fa-calculator"></i> <span>Cotizador</span>
          </a>
        </li>
      @endif
    </ul>
  </aside>
</div>
