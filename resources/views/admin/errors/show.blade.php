@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin-errors.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1>Detalle del error</h1>
            <p class="subtle">{{ $errorLog->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('admin.errors.index') }}">Volver</a>
    </div>

    <div class="card error-detail">
        <div class="error-grid">
            <div>
                <strong>Tipo</strong>
                <p>{{ $errorLog->exception_class }}</p>
            </div>
            <div>
                <strong>Codigo</strong>
                <p>{{ $errorLog->status_code ?? '---' }}</p>
            </div>
            <div>
                <strong>Archivo</strong>
                <p>{{ $errorLog->file ?? '---' }}</p>
            </div>
            <div>
                <strong>Linea</strong>
                <p>{{ $errorLog->line ?? '---' }}</p>
            </div>
            <div>
                <strong>URL</strong>
                <p>{{ $errorLog->url ?? '---' }}</p>
            </div>
            <div>
                <strong>Metodo</strong>
                <p>{{ $errorLog->method ?? '---' }}</p>
            </div>
            <div>
                <strong>Usuario</strong>
                <p>{{ $errorLog->user?->name ?? 'Anonimo' }}</p>
            </div>
            <div>
                <strong>IP</strong>
                <p>{{ $errorLog->ip ?? '---' }}</p>
            </div>
        </div>

        <div class="error-block">
            <strong>Mensaje</strong>
            <pre>{{ $errorLog->message }}</pre>
        </div>

        @if ($errorLog->trace)
            <div class="error-block">
                <strong>Trace</strong>
                <pre>{{ $errorLog->trace }}</pre>
            </div>
        @endif
    </div>
@endsection
