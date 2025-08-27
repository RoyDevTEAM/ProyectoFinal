<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\CodigoOtp;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use SendGrid\Mail\Mail as SendGridMail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use App\Models\TokenResetPassword;


class AuthController extends Controller
{
    public function showRegisterForm()
    {
        $roles = Rol::whereIn('nombre', ['Profesor', 'Estudiante'])->get();
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*\d)/',
            'username' => 'required|string|unique:usuarios,username',
            'rol_id' => 'required|exists:roles,id',
        ], [
            'password.regex' => 'La contraseña debe contener al menos una mayúscula y un número.',
            'rol_id.exists' => 'El rol seleccionado no es válido.',
        ]);

        $usuario = Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'verificado' => false,
        ]);

        // Generar token de verificación (como en la consigna original)
        $token = \Illuminate\Support\Str::random(60);
        \App\Models\TokenVerificacion::create([
            'usuario_id' => $usuario->id,
            'token' => $token,
            'expires_at' => now()->addDay(),
        ]);

        // Enviar email de verificación con SendGrid
        $email = new SendGridMail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject('Verifica tu cuenta');
        $email->addTo($usuario->email);
        $email->addContent("text/plain", "Haz clic para verificar: " . url("/verify-email/$token"));
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($email);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'Error al enviar el email de verificación.']);
        }

        return redirect()->route('login')->with('success', 'Registro exitoso. Revisa tu correo para verificar tu cuenta.');
    }

    public function verifyEmail($token)
    {
        $tokenVerificacion = \App\Models\TokenVerificacion::where('token', $token)
            ->where('expires_at', '>=', now())
            ->first();

        if ($tokenVerificacion) {
            $usuario = Usuario::find($tokenVerificacion->usuario_id);
            $usuario->update(['verificado' => true]);
            $tokenVerificacion->delete();
            return redirect()->route('login')->with('success', 'Email verificado. Por favor, inicia sesión.');
        }

        return redirect()->route('login')->withErrors(['error' => 'Token inválido o expirado.']);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login-attempts:' . $request->email . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->back()->withErrors(['email' => "Demasiados intentos fallidos. Intenta de nuevo en $seconds segundos."]);
        }

        $usuario = Usuario::where('email', $request->email)->first();

        if ($usuario && Hash::check($request->password, $usuario->password_hash)) {
            if (!$usuario->verificado) {
                return redirect()->back()->withErrors(['email' => 'Por favor, verifica tu correo antes de iniciar sesión.']);
            }

            $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiraEn = now()->addSeconds(60);

            CodigoOtp::create([
                'usuario_id' => $usuario->id,
                'codigo' => $otp,
                'expira_en' => $expiraEn,
                'utilizado' => false,
            ]);

            // Enviar OTP con SendGrid
            $email = new SendGridMail();
            $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $email->setSubject('Código OTP para inicio de sesión');
            $email->addTo($usuario->email);
            $email->addContent("text/plain", "Tu código OTP es: $otp. Expira en 60 segundos.");
            $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
            try {
                $sendgrid->send($email);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['email' => 'Error al enviar el OTP. Por favor, intenta de nuevo.']);
            }

            Session::put('otp_usuario_id', $usuario->id);
            return redirect()->route('otp.form');
        }

        RateLimiter::hit($key, 300); // 5 minutos
        return redirect()->back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function showOtpForm()
    {
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $usuarioId = Session::get('otp_usuario_id');
        if (!$usuarioId) {
            return redirect()->route('login')->withErrors(['otp' => 'Sesión expirada. Por favor, inicia sesión nuevamente.']);
        }

        $otp = CodigoOtp::where('usuario_id', $usuarioId)
            ->where('codigo', $request->otp)
            ->where('expira_en', '>=', now())
            ->where('utilizado', false)
            ->first();

        if ($otp) {
            $otp->update(['utilizado' => true]);
            Session::put('usuario_id', $usuarioId);
            Session::forget('otp_usuario_id');

            \App\Models\LogAcceso::create([
                'usuario_id' => $usuarioId,
                'accion' => 'Login exitoso',
                'ip' => $request->ip(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Inicio de sesión exitoso.');
        }

        return redirect()->back()->withErrors(['otp' => 'Código OTP inválido o expirado.']);
    }

    public function dashboard()
    {
        if (!Session::has('usuario_id')) {
            return redirect()->route('login');
        }

        $usuario = Usuario::find(Session::get('usuario_id'));
        return view('auth.dashboard', compact('usuario'));
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Sesión cerrada.');
    }

    public function showPasswordResetRequestForm()
    {
        return view('auth.password_reset_request');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        $token = \Illuminate\Support\Str::random(60);
        TokenResetPassword::create([
            'usuario_id' => $usuario->id,
            'token' => $token,
            'expires_at' => now()->addHour(),
        ]);

        $email = new SendGridMail();
        $email->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $email->setSubject('Restablecer Contraseña');
        $email->addTo($usuario->email);
        $email->addContent("text/plain", "Haz clic para restablecer tu contraseña: " . url("/password/reset/$token"));
        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($email);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'Error al enviar el enlace de restablecimiento.']);
        }

        return redirect()->route('login')->with('success', 'Enlace de restablecimiento enviado. Revisa tu correo.');
    }

    public function showPasswordResetForm($token)
    {
        $tokenReset = TokenResetPassword::where('token', $token)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$tokenReset) {
            return redirect()->route('login')->withErrors(['error' => 'Token inválido o expirado.']);
        }

        return view('auth.password_reset', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*\d)/|confirmed',
        ], [
            'password.regex' => 'La contraseña debe contener al menos una mayúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $tokenReset = TokenResetPassword::where('token', $request->token)
            ->where('expires_at', '>=', now())
            ->first();

        if ($tokenReset) {
            $usuario = Usuario::find($tokenReset->usuario_id);
            $usuario->update(['password_hash' => Hash::make($request->password)]);

            \App\Models\LogCambioPassword::create([
                'usuario_id' => $usuario->id,
                'accion' => 'Contraseña restablecida',
                'ip' => $request->ip(),
            ]);

            $tokenReset->delete();
            return redirect()->route('login')->with('success', 'Contraseña restablecida. Por favor, inicia sesión.');
        }

        return redirect()->route('login')->withErrors(['error' => 'Token inválido o expirado.']);
    }

}