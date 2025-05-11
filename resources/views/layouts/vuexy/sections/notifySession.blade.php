@php
    $notifications = collect([
        session('vuexy_notification') ? ['channel' => 'vuexy', 'data' => session('vuexy_notification')] : null,
        session('vuexy_toastr') ? ['channel' => 'toastr', 'data' => session('vuexy_toastr')] : null,
        session('vuexy_notyf') ? ['channel' => 'notyf', 'data' => session('vuexy_notyf')] : null,
        session('vuexy_swal') ? ['channel' => 'sweetalert', 'data' => session('vuexy_swal')] : null,
        session('vuexy_pnotify') ? ['channel' => 'pnotify', 'data' => session('vuexy_pnotify')] : null,
    ])->filter();
@endphp

@if ($notifications->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const notifications = @json($notifications);

            for (const n of notifications) {
                try {
                    const { NotifyChannelService } = await import('/build/js/notify-channel-service.js');
                    await NotifyChannelService.notify({ channel: n.channel, ...n.data });
                } catch (e) {
                    console.error('❌ Error al enviar notificación por canal: ' + n.channel, e);
                }
            }
        });
    </script>
@endif
