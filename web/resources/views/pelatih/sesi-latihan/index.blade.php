@extends('layouts.app')

@section('title', 'Jadwal Sesi Latihan')
@section('page-title', 'Jadwal Sesi Latihan')

@section('content')
    <x-display.page-header title="Jadwal Sesi Latihan" subtitle="Pantau kehadiran dan evaluasi hasil latihan mingguan atlet" />

    {{-- Filter & Pencarian --}}
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between mb-5">
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('pelatih.sesi-latihan.index') }}"
                class="btn btn-xs sm:btn-sm {{ !$status ? 'bg-primary text-white hover:bg-primary-focus' : 'btn-ghost bg-base-200' }}">
                Semua
            </a>
            <a href="{{ route('pelatih.sesi-latihan.index', ['status' => 'berlangsung']) }}"
                class="btn btn-xs sm:btn-sm {{ $status === 'berlangsung' ? 'bg-primary text-white hover:bg-primary-focus' : 'btn-ghost bg-base-200' }}">
                Berlangsung
            </a>
            <a href="{{ route('pelatih.sesi-latihan.index', ['status' => 'mendatang']) }}"
                class="btn btn-xs sm:btn-sm {{ $status === 'mendatang' ? 'bg-primary text-white hover:bg-primary-focus' : 'btn-ghost bg-base-200' }}">
                Mendatang
            </a>
            <a href="{{ route('pelatih.sesi-latihan.index', ['status' => 'selesai']) }}"
                class="btn btn-xs sm:btn-sm {{ $status === 'selesai' ? 'bg-primary text-white hover:bg-primary-focus' : 'btn-ghost bg-base-200' }}">
                Selesai
            </a>
        </div>

        <form action="{{ route('pelatih.sesi-latihan.index') }}" method="GET" class="w-full sm:w-72">
            @if ($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative">
                <input type="text" name="cari" value="{{ $search ?? '' }}"
                    placeholder="Cari sesi atau lokasi..."
                    class="input input-sm sm:input-md w-full pr-8 border-base-300 focus:border-primary text-xs sm:text-sm">
                <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-base-content/50 hover:text-primary">
                    <x-display.icon name="search" class="size-4" />
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Sesi Latihan --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead class="bg-base-200/80 text-[11px] font-bold uppercase tracking-wider text-base-content/70">
                    <tr>
                        <th class="py-3 px-4">Tanggal & Jam</th>
                        <th class="py-3 px-4">Nama Sesi Latihan</th>
                        <th class="py-3 px-4 hidden md:table-cell">Lokasi & Tipe</th>
                        <th class="py-3 px-4 text-center">Peserta</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200 text-xs sm:text-sm">
                    @forelse ($sesiLatihans as $sesi)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            {{-- Tanggal & Jam --}}
                            <td class="py-3 px-4 whitespace-nowrap align-middle">
                                <div class="font-bold text-base-content">
                                    {{ $sesi->tanggal ? $sesi->tanggal->format('d M Y') : '-' }}
                                </div>
                                <div class="text-[11px] text-base-content/60 font-mono mt-0.5">
                                    {{ $sesi->jam_mulai ? substr($sesi->jam_mulai, 0, 5) : '08:00' }} -
                                    {{ $sesi->jam_selesai ? substr($sesi->jam_selesai, 0, 5) : 'Selesai' }} WIB
                                </div>
                            </td>

                            {{-- Nama Sesi --}}
                            <td class="py-3 px-4 align-middle">
                                <div class="font-semibold text-base-content">{{ $sesi->nama_sesi }}</div>
                                <div class="text-xs text-base-content/60 md:hidden mt-0.5">
                                    {{ $sesi->lokasi ?? 'Lapangan Utama' }} • {{ str_replace('_', ' ', ucfirst($sesi->jenis_latihan)) }}
                                </div>
                            </td>

                            {{-- Lokasi & Tipe --}}
                            <td class="py-3 px-4 hidden md:table-cell align-middle">
                                <div class="text-xs text-base-content/90 font-medium">{{ $sesi->lokasi ?? 'Lapangan Utama' }}</div>
                                <div class="text-[11px] text-base-content/50 capitalize mt-0.5">{{ str_replace('_', ' ', $sesi->jenis_latihan) }}</div>
                            </td>

                            {{-- Peserta --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap">
                                <span class="badge badge-sm font-semibold {{ $sesi->sesi_count > 0 ? 'bg-primary/10 text-primary border-primary/20' : 'badge-ghost' }}">
                                    {{ $sesi->sesi_count }} atlet
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap">
                                @if ($sesi->status === 'berlangsung')
                                    <span class="badge badge-sm font-semibold bg-primary text-white border-0">
                                        Berlangsung
                                    </span>
                                @elseif ($sesi->status === 'mendatang')
                                    <span class="badge badge-sm font-semibold badge-outline text-base-content/70">
                                        Mendatang
                                    </span>
                                @elseif ($sesi->status === 'selesai')
                                    <span class="badge badge-sm font-semibold bg-base-300 text-base-content/70 border-0">
                                        Selesai
                                    </span>
                                @else
                                    <span class="badge badge-sm font-semibold badge-ghost">
                                        {{ ucfirst($sesi->status) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-4 text-right align-middle whitespace-nowrap">
                                <a href="{{ route('pelatih.sesi-latihan.show', $sesi) }}"
                                    class="btn btn-xs sm:btn-sm btn-outline border-primary text-primary hover:bg-primary hover:text-white hover:border-primary gap-1">
                                    <x-display.icon name="eye" class="size-3.5" />
                                    <span>Evaluasi</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="6">
                            Tidak ada jadwal sesi latihan yang ditemukan.
                        </x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($sesiLatihans->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $sesiLatihans->links() }}
            </div>
        @endif
    </div>
@endsection
