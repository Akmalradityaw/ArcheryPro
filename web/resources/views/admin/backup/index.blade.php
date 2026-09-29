@extends('layouts.app')

@section('title', 'Backup')
@section('page-title', 'Backup')

@section('content')
    <x-display.page-header title="Backup & Restore"
        subtitle="Unduh salinan data dalam 5 format, atau restore dari file JSON" />

    {{-- AKSI --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        {{-- Backup Manual --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-5 sm:p-6">
                <div class="flex items-start gap-4">
                    <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                        <x-display.icon name="download" class="size-6 text-primary" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-base">Backup Manual</h3>
                        <p class="text-sm text-base-content/60 mt-1">Unduh salinan data dalam format pilihan Anda.</p>

                        <form method="POST" action="{{ route('admin.backup.run') }}" class="mt-4">
                            @csrf
                            <div class="flex flex-wrap items-center gap-2">
                                <select name="format"
                                    class="select select-bordered select-sm w-32 py-0 h-8 leading-normal rounded-xl"
                                    aria-label="Format backup">
                                    <option value="json">JSON</option>
                                    <option value="pdf">PDF</option>
                                    <option value="docx">Word</option>
                                    <option value="xlsx">Excel</option>
                                    <option value="txt">TXT</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm gap-1 rounded-xl">
                                    <x-display.icon name="download" class="size-4" />
                                    Backup
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Restore --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-5 sm:p-6">
                <div class="flex items-start gap-4">
                    <div class="size-12 rounded-lg bg-warning/10 flex items-center justify-center shrink-0">
                        <x-display.icon name="upload" class="size-6 text-warning" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-base">Restore dari JSON</h3>
                        <p class="text-sm text-base-content/60 mt-1">Pulihkan data dari file backup JSON.</p>

                        <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data"
                            class="mt-4 space-y-2">
                            @csrf
                            <input type="file" name="file" accept=".json" required
                                class="file-input file-input-bordered file-input-sm w-full rounded-xl">
                            <button type="submit" class="btn btn-warning btn-sm gap-1 rounded-xl">
                                <x-display.icon name="refresh" class="size-4" />
                                Restore
                            </button>
                            <x-form.input-error :messages="$errors->get('file')" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- WARNING --}}
    <x-feedback.alert type="warning" icon="warning-triangle" class="mb-6">
        <div class="text-xs">
            <p class="font-semibold text-warning-content">Perhatian saat Restore</p>
            <p class="text-base-content/70 mt-0.5">
                Restore akan <strong>menimpa</strong> data yang ada. Pastikan Anda sudah membuat backup
                sebelum melakukan restore.
            </p>
        </div>
    </x-feedback.alert>

    {{-- RIWAYAT --}}
    <x-table.data-table :paginator="$riwayat" :cari="true" placeholder="Cari jenis / format...">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap">Waktu</th>
                <th class="py-2.5 px-3 sm:px-4 text-left">Oleh</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Jenis</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Format</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-right whitespace-nowrap">Ukuran</th>
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap w-px">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($riwayat as $b)
                <tr class="hover:bg-base-200/30 transition-colors">
                    <td class="py-2.5 px-2 sm:px-4 whitespace-nowrap align-middle">
                        <span
                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                            {{ $b->created_at->format('d/m/Y H:i') }}
                        </span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 align-middle">
                        <span class="text-xs text-base-content/80">{{ $b->user->username ?? '-' }}</span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span class="badge badge-ghost badge-sm">{{ $b->jenis_backup }}</span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 align-middle">
                        @php
                            $formatClass = match (strtolower($b->format)) {
                                'json' => 'badge-info',
                                'pdf' => 'badge-error',
                                'docx' => 'badge-primary',
                                'xlsx' => 'badge-success',
                                'txt' => 'badge-neutral',
                                default => 'badge-ghost',
                            };
                        @endphp
                        <span class="badge {{ $formatClass }} badge-sm font-mono">{{ strtoupper($b->format) }}</span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-right whitespace-nowrap align-middle">
                        <span class="font-mono text-xs text-base-content/70">{{ $b->ukuran_file ?? '-' }}</span>
                    </td>
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap w-px align-middle">
                        <a href="{{ route('admin.backup.download', $b) }}"
                            class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                            title="Unduh">
                            <x-display.icon name="download" class="size-3.5 sm:size-4" />
                        </a>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="6">
                    Belum ada riwayat backup. Klik "Backup" untuk memulai.
                </x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>
@endsection
