@extends('layouts.app')

@section('title', 'Jadwal Latihan Mingguan')
@section('page-title', 'Jadwal Latihan')

@section('content')
    <x-display.page-header title="Jadwal Latihan Mingguan" subtitle="Kelola agenda latihan rutin klub, sesi scoring, dan fokus materi latihan">
        <x-slot:actions>
            <x-form.btn-primary href="{{ route('admin.sesi-latihan.create') }}" size="sm"
                class="gap-1.5 rounded-xl text-xs sm:text-sm">
                <x-display.icon name="plus" class="size-4 shrink-0" />
                <span class="hidden sm:inline">Tambah Jadwal Latihan</span>
                <span class="sm:hidden">Tambah</span>
            </x-form.btn-primary>
        </x-slot:actions>
    </x-display.page-header>

    {{-- Kartu Ringkasan Status --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <x-display.card-stat title="Total Jadwal" :value="$ringkasan['total']" icon="calendar" color="primary" />
        <x-display.card-stat title="Terjadwal" :value="$ringkasan['terjadwal']" icon="clock" color="info" />
        <x-display.card-stat title="Berlangsung" :value="$ringkasan['berlangsung']" icon="target" color="warning" />
        <x-display.card-stat title="Selesai" :value="$ringkasan['selesai']" icon="check-circle" color="success" />
    </div>

    <x-table.data-table :paginator="$sesiLatihans" :cari="true" placeholder="Cari jadwal / materi / lokasi...">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-3 sm:px-4 text-left">Sesi Latihan</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left whitespace-nowrap">Tanggal & Jam</th>
                <th class="py-2.5 px-3 sm:px-4 hidden sm:table-cell text-left">Jenis</th>
                <th class="py-2.5 px-3 sm:px-4 hidden xl:table-cell text-left">Lokasi & Fokus</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Status</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap w-px">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($sesiLatihans as $sesi)
                <tr class="hover:bg-base-200/30 transition-colors">
                    <td class="py-2 px-2 sm:py-3 sm:px-4 min-w-0 align-middle">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div
                                class="size-7 sm:size-9 rounded-lg sm:rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[11px] sm:text-xs uppercase shadow-xs">
                                <x-display.icon name="target" class="size-4 sm:size-5 text-primary" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-xs sm:text-sm text-base-content truncate">
                                    {{ $sesi->nama_sesi }}
                                </div>
                                <div class="text-[10px] sm:text-[11px] text-base-content/50 lg:hidden mt-0.5 truncate">
                                    {{ $sesi->tanggal->format('d/m/Y') }}
                                    @if ($sesi->jam_mulai)
                                        • {{ substr($sesi->jam_mulai, 0, 5) }}
                                    @endif
                                    • {{ str_replace('_', ' ', ucfirst($sesi->jenis_latihan)) }}
                                </div>
                                @if ($sesi->sesi_count > 0)
                                    <div class="text-[10px] text-primary font-medium mt-0.5">
                                        {{ $sesi->sesi_count }} atlet tercatat
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap hidden lg:table-cell align-middle">
                        <div class="flex flex-col">
                            <span class="font-mono text-xs text-base-content/80 font-bold">
                                {{ $sesi->tanggal->format('d/m/Y') }}
                            </span>
                            <span class="text-[10px] text-base-content/50">
                                @if ($sesi->jam_mulai)
                                    {{ substr($sesi->jam_mulai, 0, 5) }} @if($sesi->jam_selesai) - {{ substr($sesi->jam_selesai, 0, 5) }} @endif WIB
                                @else
                                    Waktu Fleksibel
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden sm:table-cell align-middle">
                        <span class="badge badge-sm font-semibold capitalize
                            {{ $sesi->jenis_latihan === 'rutin_mingguan' ? 'badge-primary badge-outline' : 'badge-ghost' }}">
                            {{ str_replace('_', ' ', $sesi->jenis_latihan) }}
                        </span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden xl:table-cell align-middle">
                        <div class="max-w-[220px]">
                            <div class="text-xs text-base-content/80 font-medium truncate flex items-center gap-1.5">
                                <x-display.icon name="pin" class="size-3 text-primary/70 shrink-0" />
                                <span>{{ $sesi->lokasi ?? 'Lapangan Utama' }}</span>
                            </div>
                            @if ($sesi->fokus_latihan)
                                <div class="text-[11px] text-base-content/50 truncate mt-0.5" title="{{ $sesi->fokus_latihan }}">
                                    {{ $sesi->fokus_latihan }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap align-middle">
                        <x-display.badge-status :status="$sesi->status" />
                    </td>
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap w-px align-middle">
                        <div class="flex items-center justify-start gap-1">
                            {{-- Tombol Duplikasi Sesi (Copy Jadwal Pekan Lalu) --}}
                            <form method="POST" action="{{ route('admin.sesi-latihan.duplicate', $sesi) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                    title="Duplikasi Jadwal ke Pekan Depan (+7 hari)">
                                    <x-display.icon name="document-duplicate" class="size-3.5 sm:size-4" />
                                </button>
                            </form>

                            <a href="{{ route('admin.sesi-latihan.edit', $sesi) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                title="Edit Jadwal">
                                <x-display.icon name="pencil" class="size-3.5 sm:size-4" />
                            </a>

                            <button type="button"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-error hover:bg-error/10 rounded-lg transition-colors"
                                title="Hapus Jadwal"
                                onclick="document.getElementById('del-sesi-{{ $sesi->id }}').showModal()">
                                <x-display.icon name="trash" class="size-3.5 sm:size-4" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="6">Belum ada jadwal latihan.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>

    @push('modals')
        @foreach ($sesiLatihans as $sesi)
            <x-feedback.modal-confirm id="del-sesi-{{ $sesi->id }}" title="Hapus Jadwal Latihan" :message="'Hapus jadwal ' . $sesi->nama_sesi . '?'" :action="route('admin.sesi-latihan.destroy', $sesi)"
                method="DELETE" />
        @endforeach
    @endpush
@endsection
