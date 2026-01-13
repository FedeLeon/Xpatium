<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Registro</title>
    </head>
    <body>
        <main>
            <h1>Crear cuenta</h1>

            @if ($errors->any())
                <div>
                    <p>Revisa los datos.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <div>
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label for="password">Contrasena</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <div>
                    <label for="password_confirmation">Confirmar contrasena</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                </div>
                <button type="submit">Registrarme</button>
            </form>

            <p>
                Ya tenes cuenta?
                <a href="{{ route('login') }}">Iniciar sesion</a>
            </p>
        </main>
    </body>
</html>
