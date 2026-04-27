@extends('layouts.app')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin-plans.css') }}">
    @endpush

    <div class="actions">
        <div>
            <h1>Gestion de planes</h1>
            <p>Asigna planes y pruebas gratis a lideres.</p>
        </div>
        <a class="btn btn-secondary" href="{{ route('admin.dashboard') }}">Volver</a>
    </div>

    <div class="card">
        @if ($teachers->isEmpty())
            <p class="subtle">No hay lideres registrados.</p>
        @else
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Lider</th>
                            <th>Plan actual</th>
                            <th>Uso</th>
                            <th>Asignar plan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teachers as $teacher)
                            @php
                                $usedBytes = (int) ($teacher->assets_sum_size ?? 0);
                                $limitBytes = $teacher->storageLimitBytes();
                                $usedGb = $usedBytes / (1024 * 1024 * 1024);
                                $limitGb = $limitBytes / (1024 * 1024 * 1024);
                                $percent = $limitBytes > 0 ? min(100, (int) round(($usedBytes / $limitBytes) * 100)) : 0;
                            @endphp
                            <tr>
                                <td>{{ $teacher->name }} {{ $teacher->last_name }}</td>
                                <td>{{ $teacher->storagePlanLabel() }}</td>
                                <td>{{ number_format($usedGb, 2) }} / {{ number_format($limitGb, 0) }} GB ({{ $percent }}%)</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.plans.update', $teacher) }}" class="plan-form">
                                        @csrf
                                        @method('PUT')
                                        <select name="storage_plan">
                                            @foreach ($planOptions as $key => $plan)
                                                <option value="{{ $key }}" {{ $teacher->storagePlanKey() === $key ? 'selected' : '' }}>
                                                    {{ $plan['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-secondary" type="submit">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
