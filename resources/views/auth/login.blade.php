<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #667eea, #764ba2);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .login-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
      padding: 40px;
      width: 100%;
      max-width: 420px;
      animation: fadeIn 0.6s ease-in-out;
    }
    .login-card h2 {
      text-align: center;
      margin-bottom: 20px;
      font-weight: 700;
      color: #333;
    }
    .form-control {
      border-radius: 10px;
      padding: 12px;
    }
    .btn-primary {
      background: linear-gradient(135deg, #667eea, #764ba2);
      border: none;
      border-radius: 10px;
      padding: 12px;
      width: 100%;
      font-weight: bold;
      transition: transform 0.2s ease-in-out;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg, #5a67d8, #6b46c1);
    }
    .extra-links {
      text-align: center;
      margin-top: 15px;
    }
    .extra-links a {
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
      margin: 0 8px;
    }
    .extra-links a:hover {
      text-decoration: underline;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>🔐 Iniciar Sesión</h2>
    
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Correo electrónico" required value="{{ old('email') }}">
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
      </div>
      <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>

    <div class="extra-links">
      <a href="{{ route('register') }}">Crear cuenta</a> | 
      <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
    </div>
  </div>
</body>
</html>
