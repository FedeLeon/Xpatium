@php
    $flashMessages = [
        'error' => session('error'),
        'warning' => session('warning'),
        'success' => session('success'),
        'status' => session('status'),
        'info' => session('info'),
    ];
@endphp

@if ($errors->any() || collect($flashMessages)->filter()->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal === 'undefined') {
                return;
            }

            var alerts = [];

            @if ($errors->any())
                alerts.push({
                    icon: 'error',
                    title: 'Revisa los datos',
                    html: (function () {
                        var items = @json($errors->all());
                        return '<ul>' + items.map(function (message) {
                            return '<li>' + message + '</li>';
                        }).join('') + '</ul>';
                    })()
                });
            @endif

            @if (session('error'))
                alerts.push({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error'))
                });
            @endif

            @if (session('warning'))
                alerts.push({
                    icon: 'warning',
                    title: 'Atencion',
                    text: @json(session('warning'))
                });
            @endif

            @if (session('success'))
                alerts.push({
                    icon: 'success',
                    title: 'Listo',
                    text: @json(session('success'))
                });
            @endif

            @if (session('status'))
                alerts.push({
                    icon: 'success',
                    title: 'Listo',
                    text: @json(session('status'))
                });
            @endif

            @if (session('info'))
                alerts.push({
                    icon: 'info',
                    title: 'Info',
                    text: @json(session('info'))
                });
            @endif

            if (!alerts.length) {
                return;
            }

            var showNext = function () {
                if (!alerts.length) {
                    return;
                }
                Swal.fire(alerts.shift()).then(showNext);
            };

            showNext();
        });
    </script>
@endif
