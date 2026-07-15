@php
    $config = match($status) {
        'menunggu'   => ['class' => 'badge-status--warning',  'icon' => 'bi-hourglass-split',   'label' => 'Menunggu'],
        'disetujui'  => ['class' => 'badge-status--success',  'icon' => 'bi-check-circle-fill', 'label' => 'Disetujui'],
        'ditolak'    => ['class' => 'badge-status--danger',   'icon' => 'bi-x-circle-fill',     'label' => 'Ditolak'],
        'dibatalkan' => ['class' => 'badge-status--secondary', 'icon' => 'bi-x-circle',         'label' => 'Dibatalkan'],
        default      => ['class' => 'badge-status--secondary','icon' => 'bi-circle',             'label' => ucfirst($status)],
    };
@endphp
<span class="badge-status {{ $config['class'] }}">
    <i class="bi {{ $config['icon'] }} me-1"></i>{{ $config['label'] }}
</span>