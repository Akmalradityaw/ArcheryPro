@extends('layouts.app')

@section('title', 'Detail Event')
@section('page-title', 'Detail Event')

@section('content')
    {{-- Back link — konsisten dengan pelatih/event/edit & pelatih/atlet/show --}}
    <div class="mb-4">
        <a href="{{ route('pelatih.event.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Daftar Event</span>
        </a>
    </div>

    {{-- Page header — aksi navigasi saja, badge pindah ke card profil --}}
    <x-display.page-header title="{{ $event->nama_event }}" subtitle="Detail dan ringkasan sesi event">
        <x-slot:actions>
            <a href="{{ route('pelatih.event.edit', $event) }}" class="btn btn-primary btn-sm">
                Edit
            </a>
        </x-slot:actions>
    </x-display.page-header>

    {{-- Card profil event — konsisten dengan pelatih/atlet/show --}}
    <div class="card bg-base-100 border border-base-200 shadow-xs rounded-2xl p-5 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="size-14 rounded-2xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xl uppercase shadow-xs shrink-0">
                    {{ strtoupper(substr($event->nama_event, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-base-content tracking-tight">{{ $event->nama_event }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <x-display.badge-status :status="$event->status" />
                        <span class="text-xs text-base-content/50">•</span>
                        <span class="text-xs text-base-content/60 font-mono">
                            {{ $event->tanggal->format('d F Y') }}
                        </span>
                        @if ($event->lokasi)
                            <span class="text-xs text-base-content/50">•</span>
                            <span class="text-xs text-base-content/60">{{ $event->lokasi }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div
                class="grid grid-cols-2 sm:flex sm:flex-row sm:items-center gap-2.5 w-full sm:w-auto sm:justify-end border-t sm:border-t-0 pt-4 sm:pt-0 border-base-200">
                <a href="{{ route('pelatih.event.edit', $event) }}"
                    class="btn btn-outline btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center flex-nowrap whitespace-nowrap gap-1.5">
                    <span>Edit Event</span>
                </a>
                <a href="{{ route('pelatih.event.index') }}"
                    class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center flex-nowrap whitespace-nowrap gap-1.5">
                    <span>Daftar Event</span>
                </a>
            </div>
        </div>

        {{-- Info ringkas: tanggal, lokasi, visibilitas --}}
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mt-5 pt-5 border-t border-base-200">
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">Tanggal</dt>
                <dd class="font-semibold mt-0.5">{{ $event->tanggal->format('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">Lokasi</dt>
                <dd class="font-semibold mt-0.5">{{ $event->lokasi ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-base-content/50">Tampil Publik</dt>
                <dd class="font-semibold mt-0.5">
                    @if ($event->is_publik)
                        <span class="inline-flex items-center gap-1.5 text-success">
                            <x-display.icon name="check-circle" class="size-4" />
                            Ya
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-base-content/50">
                            <x-display.icon name="x" class="size-4" />
                            Tidak
                        </span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    {{-- Grid stat card — konsisten dengan pelatih/atlet/show (mb-6) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-display.card-stat title="Total Sesi" :value="$ringkasan['sesi']" icon="calendar" />
        <x-display.card-stat title="Skor Tertinggi" :value="$ringkasan['terbaik']" icon="users" color="success" />
        <x-display.card-stat title="Rata-rata" :value="$ringkasan['rata']" icon="clock" color="info" />
    </div>

    {{-- Tabel Sesi Terbaru — header & body disamakan dengan pelatih/event/index --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-base-content tracking-tight">Sesi Terbaru</h2>
                <p class="text-xs text-base-content/50 mt-0.5">5 sesi terakhir pada event ini</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                        <th class="py-2.5 px-2 sm:px-4 text-left">Tanggal</th>
                        <th class="py-2.5 px-3 sm:px-4 text-left">Atlet</th>
                        <th class="py-2.5 px-2 sm:px-4 text-right w-px whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
                    @forelse ($sesiTerbaru as $sesi)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="py-2.5 px-2 sm:px-4 whitespace-nowrap">
                                <span
                                    class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                                    {{ $sesi->tanggal_sesi->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 sm:px-4">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="size-7 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[10px] uppercase">
                                        {{ strtoupper(substr($sesi->atlet->nama_lengkap ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-base-content truncate max-w-[200px] sm:max-w-xs">
                                        {{ $sesi->atlet->nama_lengkap ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap">
                                <span class="font-semibold text-sm text-base-content tracking-tight">
                                    {{ number_format($sesi->total_skor ?? 0) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="3">Belum ada sesi pada event ini.</x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
