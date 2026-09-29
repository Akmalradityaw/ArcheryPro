@extends('layouts.app')

@section('title', 'Konfirmasi Skor')
@section('page-title', 'Konfirmasi Skor')

@section('content')
    {{-- Back link --}}
    <div class="mb-4">
        <a href="{{ route('scoring.input.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Input Skor</span>
        </a>
    </div>

    <x-display.page-header title="Review Sebelum Simpan"
        subtitle="{{ $sesiLatihan->nama_sesi ?? $event->nama_event ?? 'Latihan Rutin' }} · {{ \Carbon\Carbon::parse($data['tanggal_sesi'])->format('d/m/Y') }}{{ !empty($data['jarak_meter']) ? ' · Jarak ' . $data['jarak_meter'] . 'm' : '' }}" />

    {{-- Preview per atlet --}}
    @foreach ($data['atlet_ids'] as $atletId)
        @if (!empty($data['skor'][$atletId]))
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-base-200 flex items-center gap-3">
                    <div
                        class="size-9 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                        {{ strtoupper(substr($atlets[$atletId]->nama_lengkap ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-base-content tracking-tight">
                            {{ $atlets[$atletId]->nama_lengkap ?? 'Atlet #' . $atletId }}
                        </h3>
                        <p class="text-xs text-base-content/50 mt-0.5">
                            Total: <span class="font-bold text-primary">
                                {{ collect($data['skor'][$atletId])->flatten()->sum() }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-base-200/50 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                                <th class="py-2.5 px-2 sm:px-4 text-left">End</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">1</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">2</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">3</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">4</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">5</th>
                                <th class="py-2.5 px-2 sm:px-4 text-center">6</th>
                                <th class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
                            @foreach ($data['skor'][$atletId] as $ei => $panah)
                                <tr class="hover:bg-base-200/30 transition-colors">
                                    <td class="py-2.5 px-2 sm:px-4 whitespace-nowrap align-middle">
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
                                    <td class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap align-middle">
                                        <span class="font-bold text-sm text-primary tracking-tight">
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

    {{-- Form Simpan --}}
    <form method="POST" action="{{ route('scoring.input.selesai') }}">
        @csrf
        @if (!empty($data['sesi_latihan_id']))
            <input type="hidden" name="sesi_latihan_id" value="{{ $data['sesi_latihan_id'] }}">
        @endif
        @if (!empty($data['event_id']))
            <input type="hidden" name="event_id" value="{{ $data['event_id'] }}">
        @endif
        @if (!empty($data['jarak_meter']))
            <input type="hidden" name="jarak_meter" value="{{ $data['jarak_meter'] }}">
        @endif
        @if (!empty($data['catatan_pelatih']))
            <input type="hidden" name="catatan_pelatih" value="{{ $data['catatan_pelatih'] }}">
        @endif
        <input type="hidden" name="tanggal_sesi" value="{{ $data['tanggal_sesi'] }}">
        @foreach ($data['atlet_ids'] as $atletId)
            <input type="hidden" name="atlet_ids[]" value="{{ $atletId }}">
            @foreach ($data['skor'][$atletId] ?? [] as $ei => $panah)
                @foreach ($panah as $si => $nilai)
                    <input type="hidden" name="skor[{{ $atletId }}][{{ $ei }}][{{ $si }}]"
                        value="{{ $nilai }}">
                @endforeach
            @endforeach
        @endforeach

        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-4 sm:p-5">
                <div class="flex items-center justify-end">
                    <x-form.btn-primary type="submit" size="sm" class="gap-1.5 rounded-xl">
                        <span>Simpan Skor</span>
                    </x-form.btn-primary>
                </div>
            </div>
        </div>
    </form>
@endsection
