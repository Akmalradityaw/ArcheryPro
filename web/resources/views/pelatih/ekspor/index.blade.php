@extends('layouts.app')

@section('title', 'Ekspor Laporan')
@section('page-title', 'Ekspor Laporan')

@section('content')
    <x-display.page-header title="Ekspor Laporan" subtitle="Unduh rekap performa semua atlet dalam format Excel atau PDF" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('pelatih.ekspor.performa') }}">
                @csrf

                <div class="space-y-6">
                    {{-- Section: Filter --}}
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Filter Jadwal Latihan
                        </h3>

                        <x-form.input-field name="sesi_latihan_id" type="select" label="Jadwal Sesi Latihan" hint="opsional"
                            :options="['' => 'Semua sesi latihan mingguan'] + $sesiLatihans->pluck('nama_sesi', 'id')->toArray()" />
                    </div>

                    {{-- Section: Format Unduhan --}}
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Format Unduhan
                        </h3>

                        <x-feedback.alert type="neutral" icon="info-circle">
                            <div class="text-xs">
                                <p class="font-medium text-base-content/80">Pilih format yang diinginkan:</p>
                                <ul class="list-disc list-inside mt-1 space-y-0.5 text-base-content/70">
                                    <li><strong>Excel</strong> — untuk analisis data lebih lanjut</li>
                                    <li><strong>PDF</strong> — untuk cetak atau arsip laporan</li>
                                </ul>
                            </div>
                        </x-feedback.alert>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-2">
                            <x-form.btn-primary type="submit" size="sm" name="format" value="excel"
                                class="w-full sm:w-auto justify-center gap-2 rounded-xl">
                                <span>Unduh Excel</span>
                            </x-form.btn-primary>

                            <x-form.btn-primary type="submit" size="sm" name="format" value="pdf"
                                variant="outline" class="w-full sm:w-auto justify-center gap-2 rounded-xl">
                                <span>Unduh PDF</span>
                            </x-form.btn-primary>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
