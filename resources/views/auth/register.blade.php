<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background: linear-gradient(135deg, #43cea2, #185a9d);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .register-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.15);
      padding: 40px;
      width: 100%;
      max-width: 500px;
      animation: fadeIn 0.6s ease-in-out;
    }
    .register-card h2 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: 700;
      color: #333;
    }
    .form-control, .form-select {
      border-radius: 10px;
      padding: 12px;
    }
    .btn-primary {
      background: linear-gradient(135deg, #43cea2, #185a9d);
      border: none;
      border-radius: 10px;
      padding: 12px;
      width: 100%;
      font-weight: bold;
      transition: transform 0.2s ease-in-out;
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg, #36d1dc, #5b86e5);
    }
    .extra-links {
      text-align: center;
      margin-top: 15px;
    }
    .extra-links a {
      color: #185a9d;
      text-decoration: none;
      font-size: 14px;
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
  <div class="register-card">
    <h2>📝 Registro de Usuario</h2>

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

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="mb-3">
        <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario" required value="{{ old('username') }}">
      </div>
      <div class="mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="Correo electrónico" required value="{{ old('email') }}">
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
      </div>
      <div class="mb-3">
        <select class="form-select" id="rol_id" name="rol_id" required>
          <option value="">Selecciona un rol</option>
          @foreach ($roles as $rol)
            <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>

    <div class="extra-links">
      <a href="{{ route('login') }}">Ya tengo cuenta</a>
    </div>
  </div>
</body>
</html>
