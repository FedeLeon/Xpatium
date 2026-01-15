@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin-errors.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1>Errores del sistema</h1>
            <p>Registro interno de errores tecnicos.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('admin.dashboard') }}">Volver</a>
    </div>

    <div class="card">
        @if ($logs->isEmpty())
            <p class="subtle">No hay errores registrados.</p>
        @else
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Codigo</th>
                            <th>Tipo</th>
                            <th>Mensaje</th>
                            <th>Usuario</th>
                            <th>URL</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->status_code ?? '---' }}</td>
                                <td>{{ class_basename($log->exception_class) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($log->message, 80) }}</td>
                                <td>{{ $log->user?->name ?? 'Anonimo' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($log->url ?? '-', 60) }}</td>
                                <td>
                                    <a class="btn btn-secondary" href="{{ route('admin.errors.show', $log) }}">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
