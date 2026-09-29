@extends('layouts.app')

@section('title', 'Event')
@section('page-title', 'Event')

@section('content')
    <x-display.page-header title="Daftar Event" subtitle="Kelola dan pantau seluruh event yang sedang berjalan" />

    <x-table.data-table :paginator="$events" :cari="true" placeholder="Cari nama event atau lokasi...">
        @if (Route::has('pelatih.event.create'))
            <x-slot:actions>
                <x-form.btn-primary href="{{ route('pelatih.event.create') }}" size="sm"
                    class="gap-1.5 rounded-xl text-xs sm:text-sm">
                    <x-display.icon name="plus" class="size-4 shrink-0" />
                    <span class="hidden sm:inline">Tambah Event</span>
                    <span class="sm:hidden">Tambah</span>
                </x-form.btn-primary>
            </x-slot:actions>
        @endif

        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left">Event</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left whitespace-nowrap">Tanggal</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Lokasi</th>
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap">Status</th>
                {{-- HEADER AKSI: text-left, sama seperti Status --}}
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($events as $event)
                <tr class="hover:bg-base-200/30 transition-colors">
                    {{-- Nama & Avatar Event --}}
                    <td class="py-2 px-2 sm:py-3 sm:px-4 min-w-0 align-middle">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div
                                class="size-7 sm:size-9 rounded-lg sm:rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[11px] sm:text-xs uppercase shadow-xs">
                                {{ strtoupper(substr($event->nama_event, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-xs sm:text-sm text-base-content truncate">
                                    {{ $event->nama_event }}
                                </div>
                                <div class="text-[10px] sm:text-[11px] text-base-content/50 lg:hidden mt-0.5 truncate">
                                    {{ $event->tanggal->format('d/m/Y') }}@if ($event->lokasi)
                                        • {{ $event->lokasi }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Tanggal (Desktop) --}}
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap hidden lg:table-cell align-middle">
                        <span
                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                            {{ $event->tanggal->format('d/m/Y') }}
                        </span>
                    </td>

                    {{-- Lokasi (Desktop) --}}
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span class="text-xs text-base-content/80 truncate block max-w-[180px] xl:max-w-[250px]">
                            {{ $event->lokasi ?? '-' }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="py-2 px-2 sm:py-3 sm:px-4 whitespace-nowrap align-middle">
                        <x-display.badge-status :status="$event->status" />
                    </td>

                    {{-- AKSI: text-left, wrapper flex justify-start, sejajar dengan Status --}}
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap align-middle">
                        <div class="flex items-center justify-start gap-0.5 sm:gap-1">
                            @if (Route::has('pelatih.event.show'))
                                <a href="{{ route('pelatih.event.show', $event) }}"
                                    class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                    title="Lihat Detail">
                                    <x-display.icon name="eye" class="size-3.5 sm:size-4" />
                                </a>
                            @endif

                            <a href="{{ route('pelatih.event.edit', $event) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                title="Edit Event">
                                <x-display.icon name="pencil" class="size-3.5 sm:size-4" />
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                    <x-table.table-empty colspan="5">Belum ada data event yang terdaftar.</x-table.table-empty>
                @endforelse
            </tbody>
        </x-table.data-table>
    @endsection
