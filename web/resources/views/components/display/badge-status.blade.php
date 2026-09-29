@props(['status' => null])

@php
    $statusKey = is_string($status) ? strtolower(trim($status)) : '';

    $map = [
        // Event / Turnamen
        'draft' => [
            'class' => 'bg-warning/15 text-warning border-warning/20',
            'dot' => 'bg-warning',
            'label' => 'Draft',
        ],
        'berlangsung' => [
            'class' => 'bg-success/15 text-success border-success/20',
            'dot' => 'bg-success animate-pulse',
            'label' => 'Berlangsung',
        ],
        'selesai' => ['class' => 'bg-info/15 text-info border-info/20', 'dot' => 'bg-info', 'label' => 'Selesai'],

        // Atlet / Member
        'aktif' => [
            'class' => 'bg-emerald-500/15 text-emerald-600 border-emerald-500/20',
            'dot' => 'bg-emerald-500',
            'label' => 'Aktif',
        ],
        'tidak_aktif' => [
            'class' => 'bg-error/15 text-error border-error/20',
            'dot' => 'bg-error',
            'label' => 'Tidak Aktif',
        ],

        // User / Verifikasi
        'pending' => [
            'class' => 'bg-warning/15 text-warning border-warning/20',
            'dot' => 'bg-warning animate-pulse',
            'label' => 'Pending',
        ],
        'verified' => [
            'class' => 'bg-primary/15 text-primary border-primary/20',
            'dot' => 'bg-primary',
            'label' => 'Terverifikasi',
        ],
    ];

    $item = $map[$statusKey] ?? [
        'class' => 'bg-base-200 text-base-content/70 border-base-300',
        'dot' => 'bg-base-content/40',
        'label' => $status ? ucfirst(str_replace('_', ' ', $status)) : '-',
    ];
@endphp

<span
    {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-semibold rounded-full border shadow-2xs transition-colors {$item['class']}"]) }}>
    <span class="size-1.5 rounded-full {{ $item['dot'] }}"></span>
    <span>{{ $item['label'] }}</span>
</span>
