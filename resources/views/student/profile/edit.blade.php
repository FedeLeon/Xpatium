@extends('layouts.app')

@section('content')
    <div class="profile-hero">
        <div>
            <h1>Perfil</h1>
            <p>Actualiza tus datos personales y credenciales.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form class="profile-form" method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card card-spaced profile-card profile-card--compact">
            <div class="profile-card__title">
                <h3>Avatar</h3>
                <span class="subtle">Foto de perfil</span>
            </div>
            <div class="profile-avatar">
                <label class="avatar-circle" for="avatar">
                    @if ($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Foto de perfil">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}</span>
                    @endif
                </label>
                <input id="avatar" name="avatar" type="file" accept="image/*">
                <p class="subtle">Hace clic en el circulo para subir una imagen.</p>
            </div>
        </div>

        <div class="card card-spaced profile-card">
            <div class="profile-card__title">
                <h3>Datos personales</h3>
                <span class="subtle">Informacion de contacto y residencia.</span>
            </div>
            <div class="profile-grid">
                <div class="profile-field">
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}">
                </div>
                <div class="profile-field">
                    <label for="last_name">Apellido</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}">
                </div>
                <div class="profile-field">
                    <label for="birth_date">Fecha de nacimiento</label>
                    <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}">
                </div>
                <div class="profile-field">
                    <label for="phone">Telefono</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}">
                </div>
                <div class="profile-field">
                    <label for="address">Direccion</label>
                    <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}">
                </div>
                <div class="profile-field">
                    <label for="country">Pais</label>
                    <input id="country" name="country" type="text" value="{{ old('country', $user->country) }}">
                </div>
                <div class="profile-field">
                    <label for="state">Provincia / Estado</label>
                    <input id="state" name="state" type="text" value="{{ old('state', $user->state) }}">
                </div>
                <div class="profile-field">
                    <label for="city">Ciudad / Localidad</label>
                    <input id="city" name="city" type="text" value="{{ old('city', $user->city) }}">
                </div>
                <div class="profile-field">
                    <label for="postal_code">Codigo postal</label>
                    <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $user->postal_code) }}">
                </div>
            </div>
        </div>

        <div class="card card-spaced profile-card">
            <div class="profile-card__title">
                <h3>Credenciales</h3>
                <span class="subtle">Acceso y seguridad de la cuenta.</span>
            </div>
            <div class="profile-grid">
                <div class="profile-field">
                    <label for="email">Correo electronico</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}">
                </div>
                <div class="profile-field">
                    <label for="password">Nueva contrasena</label>
                    <input id="password" name="password" type="password">
                </div>
                <div class="profile-field">
                    <label for="password_confirmation">Confirmar contrasena</label>
                    <input id="password_confirmation" name="password_confirmation" type="password">
                </div>
            </div>
        </div>

        <div class="actions profile-actions">
            <div></div>
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
        </div>
    </form>
@endsection
