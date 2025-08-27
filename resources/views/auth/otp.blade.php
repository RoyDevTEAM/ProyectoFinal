<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2>Ingresa el código OTP</h2>
                <p>Se ha enviado un código de 6 dígitos a tu correo electrónico.</p>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="otp" class="form-label">Código OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required maxlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary">Verificar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>