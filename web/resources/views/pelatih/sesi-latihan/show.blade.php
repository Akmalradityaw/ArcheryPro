@extends('layouts.app')

@section('title', 'Evaluasi Sesi Latihan')
@section('page-title', 'Evaluasi Sesi Latihan')

@section('content')
    <div class="mb-4">
        <a href="{{ route('pelatih.sesi-latihan.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Jadwal Latihan</span>
        </a>
    </div>

    {{-- Detail Sesi Latihan Header --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm mb-6">
        <div class="card-body p-5 sm:p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="badge badge-sm font-semibold {{ $sesiLatihan->status === 'berlangsung' ? 'bg-primary text-white border-0' : ($sesiLatihan->status === 'mendatang' || $sesiLatihan->status === 'terjadwal' ? 'badge-outline' : 'bg-base-300 text-base-content/70 border-0') }}">
                            {{ ucfirst($sesiLatihan->status) }}
                        </span>
                        <span class="badge badge-sm badge-ghost text-base-content/60">
                            {{ str_replace('_', ' ', ucfirst($sesiLatihan->jenis_latihan)) }}
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-base-content">{{ $sesiLatihan->nama_sesi }}</h1>
                    <p class="text-xs sm:text-sm text-base-content/60 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span class="inline-flex items-center gap-1.5">
                            <x-display.icon name="calendar" class="size-3.5 text-primary/70 shrink-0" />
                            <span>{{ $sesiLatihan->tanggal ? $sesiLatihan->tanggal->format('d F Y') : '-' }}</span>
                        </span>
                        @if ($sesiLatihan->jam_mulai)
                            <span class="inline-flex items-center gap-1.5">
                                <x-display.icon name="clock" class="size-3.5 text-primary/70 shrink-0" />
                                <span>{{ substr($sesiLatihan->jam_mulai, 0, 5) }} @if($sesiLatihan->jam_selesai) - {{ substr($sesiLatihan->jam_selesai, 0, 5) }} @endif WIB</span>
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5">
                            <x-display.icon name="pin" class="size-3.5 text-primary/70 shrink-0" />
                            <span>{{ $sesiLatihan->lokasi ?? 'Lapangan Utama' }}</span>
                        </span>
                        @if ($sesiLatihan->minggu_ke)
                            <span class="inline-flex items-center gap-1.5 font-medium text-base-content/80">
                                <x-display.icon name="target" class="size-3.5 text-primary/70 shrink-0" />
                                <span>Pekan ke-{{ $sesiLatihan->minggu_ke }} ({{ $sesiLatihan->tahun ?? now()->year }})</span>
                            </span>
                        @endif
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    @if ($sesiLatihan->fokus_latihan)
                        <div class="bg-base-200/60 p-3 rounded-lg border border-base-300 max-w-sm text-xs text-base-content/80">
                            <span class="font-semibold text-primary block mb-0.5">Fokus / Materi Latihan:</span>
                            {{ $sesiLatihan->fokus_latihan }}
                        </div>
                    @endif

                    @if ($sesiLatihan->status !== 'batal')
                        <x-form.btn-primary href="{{ route('pelatih.skor.create', ['sesi_latihan_id' => $sesiLatihan->id]) }}" size="sm" class="rounded-xl">
                            <x-display.icon name="plus" class="size-4 shrink-0" />
                            <span>Input Skor Sesi Ini</span>
                        </x-form.btn-primary>
                    @endif
                </div>
            </div>

            {{-- Ringkasan Metrik --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-base-200">
                <div class="bg-base-200/40 p-3 rounded-lg border border-base-200">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">Total Peserta Hadir</div>
                    <div class="text-xl font-bold text-primary mt-1">{{ $totalPeserta }} Atlet</div>
                </div>
                <div class="bg-base-200/40 p-3 rounded-lg border border-base-200">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">Skor Tertinggi</div>
                    <div class="text-xl font-bold text-base-content mt-1">{{ number_format($skorTertinggi) }} Poin</div>
                </div>
                <div class="bg-base-200/40 p-3 rounded-lg border border-base-200">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">Rata-rata Skor Sesi</div>
                    <div class="text-xl font-bold text-base-content mt-1">{{ number_format($rataSkor, 1) }} Poin</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Atlet & Form Evaluasi Catatan --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden" x-data="{
        modalOpen: false,
        activeSesiId: null,
        activeAtletName: '',
        activeCatatan: '',
        openModal(sesiId, atletName, catatan) {
            this.activeSesiId = sesiId;
            this.activeAtletName = atletName;
            this.activeCatatan = catatan || '';
            this.modalOpen = true;
        }
    }">
        <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-base-content">Hasil Scoring & Evaluasi Atlet</h2>
                <p class="text-xs text-base-content/50 mt-0.5">Tulis catatan bimbingan teknis untuk dibaca oleh masing-masing atlet</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead class="bg-base-200/80 text-[11px] font-bold uppercase tracking-wider text-base-content/70">
                    <tr>
                        <th class="py-3 px-4">Atlet</th>
                        <th class="py-3 px-4 text-center">Jarak</th>
                        <th class="py-3 px-4 text-center">End</th>
                        <th class="py-3 px-4 text-right">Total Skor</th>
                        <th class="py-3 px-4 text-right">Rata End</th>
                        <th class="py-3 px-4 text-center">Stabilitas (SD)</th>
                        <th class="py-3 px-4">Catatan Evaluasi Pelatih</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200 text-xs sm:text-sm">
                    @forelse ($peserta as $p)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            {{-- Atlet --}}
                            <td class="py-3 px-4 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="size-8 rounded-lg bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                                        {{ strtoupper(substr($p['atlet']?->nama_lengkap ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-base-content">{{ $p['atlet']?->nama_lengkap ?? 'Atlet Nonaktif' }}</div>
                                        <div class="text-[11px] text-base-content/50">
                                            {{ $p['atlet']?->sekolah?->nama_sekolah ?? '-' }} • {{ $p['atlet']?->kategori?->nama_kategori ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Jarak --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap font-mono text-xs">
                                {{ $p['jarak_meter'] }}m
                            </td>

                            {{-- End --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap font-mono text-xs">
                                {{ $p['jumlah_end'] }}
                            </td>

                            {{-- Total Skor --}}
                            <td class="py-3 px-4 text-right align-middle whitespace-nowrap">
                                <span class="font-bold text-sm sm:text-base text-primary">
                                    {{ number_format($p['total_skor']) }}
                                </span>
                            </td>

                            {{-- Rata End --}}
                            <td class="py-3 px-4 text-right align-middle whitespace-nowrap font-mono text-xs">
                                {{ $p['rata_end'] }}
                            </td>

                            {{-- Stabilitas SD --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap">
                                @if (!is_null($p['standar_deviasi']))
                                    <div class="font-mono text-xs font-semibold">
                                        ±{{ $p['standar_deviasi'] }}
                                    </div>
                                    <div class="text-[10px] text-base-content/50">
                                        {{ $p['standar_deviasi'] <= 2.2 ? 'Stabil' : ($p['standar_deviasi'] <= 4.0 ? 'Cukup' : 'Fluktuatif') }}
                                    </div>
                                @else
                                    <span class="text-base-content/40">-</span>
                                @endif
                            </td>

                            {{-- Catatan Pelatih & Status Baca --}}
                            <td class="py-3 px-4 align-middle max-w-xs">
                                @if ($p['catatan_pelatih'])
                                    <div class="text-xs text-base-content/90 line-clamp-2">
                                        "{{ $p['catatan_pelatih'] }}"
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        @if ($p['is_dibaca'])
                                            <span class="badge badge-xs bg-primary/20 text-primary border-primary/30 inline-flex items-center gap-1">
                                                <x-display.icon name="check" class="size-2.5 shrink-0" />
                                                <span>Dibaca atlet {{ $p['catatan_dibaca_at'] ? $p['catatan_dibaca_at']->format('d/m H:i') : '' }}</span>
                                            </span>
                                        @else
                                            <span class="badge badge-xs badge-ghost text-base-content/50">
                                                Belum dibaca
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-base-content/40 italic">Belum ada catatan</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-4 text-right align-middle whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                        @click="openModal({{ $p['sesi_id'] }}, '{{ addslashes($p['atlet']?->nama_lengkap ?? '') }}', '{{ addslashes($p['catatan_pelatih'] ?? '') }}')"
                                        class="btn btn-xs btn-outline border-primary text-primary hover:bg-primary hover:text-white hover:border-primary gap-1"
                                        title="Beri Catatan">
                                        <x-display.icon name="edit" class="size-3" />
                                        <span>Catatan</span>
                                    </button>

                                    @if ($p['atlet'])
                                        <a href="{{ route('pelatih.analisis.show', $p['atlet']) }}"
                                            class="btn btn-xs btn-ghost text-base-content/60 hover:text-primary hover:bg-primary/10"
                                            title="Grafik Lengkap">
                                            <x-display.icon name="chart-line" class="size-3.5" />
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="8">
                            Belum ada atlet yang diinput nilainya pada sesi latihan ini.
                        </x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Modal Catatan Evaluasi --}}
        <div x-show="modalOpen" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @keydown.escape.window="modalOpen = false">
            <div class="bg-base-100 rounded-xl border border-base-300 shadow-xl max-w-lg w-full p-5 sm:p-6"
                @click.outside="modalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-base-200">
                    <h3 class="font-bold text-base text-base-content">
                        Catatan Evaluasi: <span class="text-primary" x-text="activeAtletName"></span>
                    </h3>
                    <button type="button" @click="modalOpen = false" class="btn btn-ghost btn-xs btn-circle">✕</button>
                </div>

                <form :action="'{{ url('pelatih/sesi') }}/' + activeSesiId + '/catatan'" method="POST" class="mt-4">
                    @csrf
                    <div class="form-control">
                        <label class="label text-xs font-semibold text-base-content">
                            <span>Saran Koreksi Teknik & Evaluasi Mental</span>
                        </label>
                        <textarea name="catatan_pelatih" x-model="activeCatatan" rows="4" maxlength="1000"
                            placeholder="Contoh: Release lebih diperhalus, jangan terburu-buru saat ekspansi kliker. Posisi elbow sudah stabil..."
                            class="textarea textarea-bordered w-full border-base-300 focus:border-primary text-xs sm:text-sm"></textarea>
                        <label class="label text-[11px] text-base-content/50">
                            <span>Catatan ini akan langsung tampil di dashboard personal atlet.</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 mt-4 pt-3 border-t border-base-200">
                        <button type="button" @click="modalOpen = false" class="btn btn-sm btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-sm bg-primary text-white hover:bg-primary-focus border-0">
                            Simpan Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
