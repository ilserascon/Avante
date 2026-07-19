<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión — {{ config('app.name', 'Avante') }}</title>
  <link rel="stylesheet" href="{{ asset('stisla/assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/modules/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('stisla/assets/css/components.css') }}">
  <style>
    :root {
      --login-accent: #1a8683;
    }

    body {
      min-height: 100vh;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-wrapper {
      width: 100%;
      max-width: 420px;
      padding: 1.5rem;
    }

    .login-brand {
      text-align: center;
      margin-bottom: 1.75rem;
    }

    .login-brand img {
      max-width: 220px;
      width: 100%;
      height: auto;
    }

    .login-brand p {
      color: #6c757d;
      margin-top: 0.75rem;
      margin-bottom: 0;
      font-size: 0.95rem;
      letter-spacing: 0.02em;
    }

    .login-card.card-primary {
      border: 2px solid var(--login-accent);
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    .login-card .card-header {
      background: #fff;
      border-bottom: 1px solid #f0f0f0;
      padding: 1.25rem 1.5rem 0.75rem;
    }

    .login-card .card-header h4 {
      margin: 0;
      font-weight: 600;
      color: #34395e;
    }

    .login-card .card-body {
      padding: 1.25rem 1.5rem 1.5rem;
    }

    .form-group {
      margin-bottom: 1.1rem;
    }

    .form-group label {
      font-weight: 600;
      color: #34395e;
      font-size: 0.875rem;
    }

    .input-group-text {
      background: #f8f9fa;
      border-right: none;
      color: var(--login-accent);
    }

    .input-group .form-control {
      border-left: none;
      padding-left: 0;
    }

    .input-group .form-control:focus {
      box-shadow: none;
      border-color: #ced4da;
    }

    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
      border-color: var(--login-accent);
    }

    .btn-login {
      background: var(--login-accent);
      border: 1px solid var(--login-accent);
      font-weight: 600;
      letter-spacing: 0.03em;
      padding: 0.75rem;
      transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
    }

    .btn-login:hover,
    .btn-login:focus,
    .btn-login:active {
      transform: translateY(-1px);
      box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
      background: #6c757d !important;
      border-color: #6c757d !important;
      color: #fff !important;
    }

    .custom-control-label {
      font-size: 0.875rem;
      color: #6c757d;
      cursor: pointer;
    }

    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
      color: #adb5bd;
      font-size: 0.8rem;
    }

    .alert-login {
      border-radius: 10px;
      font-size: 0.875rem;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-brand">
      <img src="{{ asset('stisla/assets/img/Logo.jpg') }}" alt="{{ config('app.name', 'Avante') }}">
    </div>

    <div class="card login-card card-primary">
      <div class="card-header">
        <h4>Iniciar Sesión</h4>
      </div>

      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger alert-login">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
          @csrf

          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
              </div>
              <input id="email" type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email') }}"
                     placeholder="tu@correo.com"
                     required autofocus tabindex="1">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
              </div>
              <input id="password" type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password"
                     placeholder="••••••••"
                     required tabindex="2">
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-group mb-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="remember" value="1"
                     class="custom-control-input" id="remember-me"
                     tabindex="3" @checked(old('remember'))>
              <label class="custom-control-label" for="remember-me">Recordarme en este equipo</label>
            </div>
          </div>

          <div class="form-group mb-0">
            <button type="submit" class="btn btn-primary btn-lg btn-block btn-login" tabindex="4">
              <i class="fas fa-sign-in-alt mr-1"></i> Iniciar Sesión
            </button>
          </div>
        </form>
      </div>
    </div>

    
  </div>

  <script src="{{ asset('stisla/assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/popper.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('stisla/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('stisla/assets/js/scripts.js') }}"></script>
</body>
</html>
