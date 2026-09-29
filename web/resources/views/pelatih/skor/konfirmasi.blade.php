@extends('layouts.app')

@section('title', 'Tinjau & Konfirmasi Skor Latihan')
@section('page-title', 'Konfirmasi Skor Latihan')

@section('content')
    {{-- Back Link --}}
    <div class="mb-4">
        <a href="{{ route('pelatih.skor.create') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Input Skor</span>
        </a>
    </div>

    <x-display.page-header title="Tinjau Sebelum Simpan"
        subtitle="{{ $sesiLatihan->nama_sesi ?? $event->nama_event ?? 'Latihan Rutin' }} · {{ \Carbon\Carbon::parse($data['tanggal_sesi'])->format('d/m/Y') }}{{ !empty($data['jarak_meter']) ? ' · Jarak ' . $data['jarak_meter'] . 'm' : '' }}" />

    {{-- Detail Sesi & Catatan Evaluasi Pelatih --}}
    @if (!empty($data['catatan_pelatih']))
        <div class="alert bg-primary/10 border border-primary/20 rounded-2xl mb-5 flex items-start gap-3 p-4">
            <div class="size-8 rounded-xl bg-primary/20 text-primary flex items-center justify-center shrink-0 mt-0.5">
                <x-display.icon name="academic" class="size-4" />
            </div>
            <div>
                <h4 class="text-xs sm:text-sm font-bold text-base-content">Catatan Evaluasi Pelatih</h4>
                <p class="text-xs text-base-content/80 mt-0.5">{{ $data['catatan_pelatih'] }}</p>
            </div>
        </div>
    @endif

    {{-- Preview per atlet --}}
    @foreach ($data['atlet_ids'] as $atletId)
        @if (!empty($data['skor'][$atletId]))
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-9 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                            {{ strtoupper(substr($atlets[$atletId]->nama_lengkap ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-base-content tracking-tight">
                                {{ $atlets[$atletId]->nama_lengkap ?? 'Atlet #' . $atletId }}
                            </h3>
                            <p class="text-[11px] text-base-content/50 mt-0.5">
                                {{ $atlets[$atletId]->sekolah->nama_sekolah ?? '-' }}
                                @if (!empty($atlets[$atletId]->kategori))
                                    • {{ $atlets[$atletId]->kategori->nama_kategori }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-base-content/50 block uppercase tracking-wider font-semibold">Total Poin</span>
                        <span class="font-bold text-base sm:text-lg text-primary font-mono">
                            {{ collect($data['skor'][$atletId])->flatten()->sum() }}
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-base-200/50 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                                <th class="py-2.5 px-3 sm:px-4 text-left">End</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">P1</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">P2</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">P3</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">P4</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">P5</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">P6</th>
                                <th class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
                            @foreach ($data['skor'][$atletId] as $ei => $panah)
                                <tr class="hover:bg-base-200/30 transition-colors">
                                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap align-middle">
                                        <span
                                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                                            End {{ $ei + 1 }}
                                        </span>
                                    </td>
                                    @foreach ($panah as $nilai)
                                        <td class="py-2.5 px-2 sm:px-4 text-center align-middle">
                                            <span
                                                class="inline-flex items-center justify-center size-7 rounded-lg bg-base-200/60 font-mono text-xs font-semibold text-base-content">
                                                {{ $nilai }}
                                            </span>
                                        </td>
                                    @endforeach
                                    <td class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap align-middle">
                                        <span class="font-bold text-sm text-primary tracking-tight font-mono">
                                            {{ array_sum($panah) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach

    {{-- Form Submit Hidden Inputs --}}
    <form method="POST" action="{{ route('pelatih.skor.selesai') }}">
        @csrf
        <input type="hidden" name="sesi_latihan_id" value="{{ $data['sesi_latihan_id'] ?? '' }}">
        <input type="hidden" name="event_id" value="{{ $data['event_id'] ?? '' }}">
        <input type="hidden" name="tanggal_sesi" value="{{ $data['tanggal_sesi'] }}">
        <input type="hidden" name="jarak_meter" value="{{ $data['jarak_meter'] ?? '' }}">
        <input type="hidden" name="catatan_pelatih" value="{{ $data['catatan_pelatih'] ?? '' }}">

        @foreach ($data['atlet_ids'] as $atletId)
            <input type="hidden" name="atlet_ids[]" value="{{ $atletId }}">
            @if (!empty($data['skor'][$atletId]))
                @foreach ($data['skor'][$atletId] as $ei => $panah)
                    @foreach ($panah as $si => $nilai)
                        <input type="hidden" name="skor[{{ $atletId }}][{{ $ei }}][{{ $si }}]"
                            value="{{ $nilai }}">
                    @endforeach
                @endforeach
            @endif
        @endforeach

        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-4 sm:p-5">
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <a href="{{ route('pelatih.skor.create') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                        <x-display.icon name="arrow-left" class="size-4 shrink-0" />
                        <span>Koreksi Input</span>
                    </a>

                    <x-form.btn-primary type="submit" size="sm" class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <x-display.icon name="check" class="size-4" />
                        <span>Konfirmasi &amp; Simpan Skor Latihan</span>
                    </x-form.btn-primary>
                </div>
            </div>
        </div>
    </form>
@endsection
