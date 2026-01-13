<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Iniciar sesion</title>
    </head>
    <body>
        <main>
            <h1>Iniciar sesion</h1>

            @if ($errors->any())
                <div>
                    <p>Revisa los datos.</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label for="password">Contrasena</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <button type="submit">Entrar</button>
            </form>

            <p>
                No tenes cuenta?
                <a href="{{ route('register') }}">Registrate</a>
            </p>
        </main>
    </body>
</html>
