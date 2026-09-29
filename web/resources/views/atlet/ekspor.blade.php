@extends('layouts.app')

@section('title', 'Ekspor Data')
@section('page-title', 'Ekspor Data')

@section('content')
    <x-display.page-header title="Ekspor Data Pribadi" subtitle="Unduh seluruh riwayat skormu dalam format pilihan" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <div class="space-y-6">
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
                                <li><strong>PDF</strong> — untuk cetak atau arsip</li>
                                <li><strong>Excel</strong> — untuk analisis data lebih lanjut</li>
                                <li><strong>Word</strong> — untuk dokumen yang bisa diedit</li>
                            </ul>
                        </div>
                    </x-feedback.alert>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-2">
                        <form method="POST" action="{{ route('atlet.ekspor.pdf') }}" class="w-full sm:w-auto">
                            @csrf
                            <x-form.btn-primary type="submit" size="sm"
                                class="w-full sm:w-auto justify-center gap-2 rounded-xl">
                                <span>Unduh PDF</span>
                            </x-form.btn-primary>
                        </form>

                        <form method="POST" action="{{ route('atlet.ekspor.excel') }}" class="w-full sm:w-auto">
                            @csrf
                            <x-form.btn-primary type="submit" size="sm" variant="outline"
                                class="w-full sm:w-auto justify-center gap-2 rounded-xl">
                                <span>Unduh Excel</span>
                            </x-form.btn-primary>
                        </form>

                        <form method="POST" action="{{ route('atlet.ekspor.word') }}" class="w-full sm:w-auto">
                            @csrf
                            <x-form.btn-primary type="submit" size="sm" variant="outline"
                                class="w-full sm:w-auto justify-center gap-2 rounded-xl">
                                <span>Unduh Word</span>
                            </x-form.btn-primary>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
