@extends('layouts.app')

@section('content')
    <div class="actions">
        <div>
            <h1>{{ $meeting->title }}</h1>
            <p class="subtle">
                {{ $meeting->scheduled_for->format('d/m/Y H:i') }} · {{ $meeting->duration_minutes }} min
            </p>
        </div>
    </div>

    @if (! $canJoin)
        <div class="card">
            <p>La reunion estara disponible desde {{ $joinAvailableAt->format('d/m/Y H:i') }}.</p>
        </div>
    @else
        @php
            $rawDomain = config('services.jitsi.domain', 'meet.jit.si');
            $jitsiDomain = preg_replace('#^https?://#', '', rtrim($rawDomain, '/'));
        @endphp
        <div id="jitsi-container" class="meeting-room"></div>
        <script src="https://meet.jit.si/external_api.js"></script>
        <script>
            (function () {
                var container = document.getElementById('jitsi-container');
                if (!container) {
                    return;
                }
                var roomName = @json($meeting->daily_room_name);
                var displayName = @json(auth()->user()?->name);
                var api = new JitsiMeetExternalAPI(@json($jitsiDomain), {
                    roomName: roomName,
                    parentNode: container,
                    width: '100%',
                    height: '100%',
                    userInfo: {
                        displayName: displayName
                    },
                    configOverwrite: {
                        prejoinPageEnabled: false
                    }
                });
                var iframe = api.getIFrame();
                if (iframe) {
                    iframe.setAttribute('allow', 'camera; microphone; fullscreen; display-capture; autoplay');
                }
            })();
        </script>
    @endif
@endsection
