@extends('layouts.app')

@section('title', 'Analisis Performa Atlet')
@section('page-title', 'Analisis Performa Atlet')

@section('content')
    <div class="mb-4">
        <a href="{{ route('pelatih.analisis.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Daftar Analisis</span>
        </a>
    </div>

    {{-- Profil Atlet Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="size-12 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-lg uppercase shadow-sm">
                {{ strtoupper(substr($atlet->nama_lengkap, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-base-content">{{ $atlet->nama_lengkap }}</h1>
                <p class="text-xs sm:text-sm text-base-content/60">
                    {{ $atlet->sekolah?->nama_sekolah ?? 'Tanpa Sekolah' }} • {{ $atlet->kategori?->nama_kategori ?? 'Umum' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Grid 4 Kartu Metrik Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-display.card-stat title="Rata-rata Skor" :value="number_format($ringkasan['rata_skor'], 1)" icon="chart-line" color="primary"
            :description="'Skor Terbaik: ' . number_format($ringkasan['skor_terbaik'])" />

        <x-display.card-stat title="Stabilitas (Standar Deviasi)" :value="!is_null($ringkasan['stabilitas_umum']) ? '±' . $ringkasan['stabilitas_umum'] : '-'" icon="target"
            :color="!is_null($ringkasan['stabilitas_umum']) && $ringkasan['stabilitas_umum'] <= 2.2 ? 'success' : 'warning'"
            :description="!is_null($ringkasan['stabilitas_umum']) ? ($ringkasan['stabilitas_umum'] <= 2.2 ? 'Sangat Stabil (Konsisten)' : ($ringkasan['stabilitas_umum'] <= 4.0 ? 'Cukup Konsisten' : 'Perlu Peningkatan')) : 'Belum cukup sesi'" />

        <x-display.card-stat title="Volume Bulan Ini" :value="number_format($ringkasan['panah_bulan_ini']) . ' panah'" icon="award" color="info"
            :description="'Total Akumulasi: ' . number_format($ringkasan['panah_total']) . ' panah'" />

        <x-display.card-stat title="Kehadiran Sesi" :value="$ringkasan['total_sesi'] . ' sesi'" icon="calendar" color="success"
            description="Tercatat di sistem latihan" />
    </div>

    {{-- Grafik Tren 4 Minggu / Moving Average --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm mb-6">
        <div class="card-body p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <div>
                    <h2 class="card-title text-base font-bold text-base-content">Tren Performa & Moving Average 4 Sesi</h2>
                    <p class="text-xs text-base-content/50">Garis hijau solid menunjukkan skor aktual, garis putus-putus menunjukkan tren stabilitas 4 sesi</p>
                </div>
            </div>

            @if ($dataSkor->isEmpty())
                <div class="py-12 text-center text-sm text-base-content/50">
                    Belum ada data sesi latihan untuk atlet ini.
                </div>
            @else
                <div class="relative h-64 sm:h-80">
                    <canvas id="analisisChart"></canvas>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabel Riwayat Sesi Latihan & Evaluasi --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden" x-data="{
        modalOpen: false,
        activeSesiId: null,
        activeNamaSesi: '',
        activeCatatan: '',
        openModal(sesiId, namaSesi, catatan) {
            this.activeSesiId = sesiId;
            this.activeNamaSesi = namaSesi;
            this.activeCatatan = catatan || '';
            this.modalOpen = true;
        }
    }">
        <div class="p-4 sm:p-5 border-b border-base-200">
            <h2 class="card-title text-base font-bold text-base-content">Riwayat Sesi & Evaluasi Teknis</h2>
            <p class="text-xs text-base-content/50 mt-0.5">Analisis rinci per sesi tembakan dan catatan pelatih</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead class="bg-base-200/80 text-[11px] font-bold uppercase tracking-wider text-base-content/70">
                    <tr>
                        <th class="py-3 px-4">Tanggal & Sesi</th>
                        <th class="py-3 px-4 text-center">Jarak</th>
                        <th class="py-3 px-4 text-center">End</th>
                        <th class="py-3 px-4 text-right">Total Skor</th>
                        <th class="py-3 px-4 text-right">Rata End</th>
                        <th class="py-3 px-4 text-center">Stabilitas (SD)</th>
                        <th class="py-3 px-4 text-center">Panah</th>
                        <th class="py-3 px-4">Catatan Pelatih</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200 text-xs sm:text-sm">
                    @forelse ($sesiList as $s)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            {{-- Tanggal & Nama Sesi --}}
                            <td class="py-3 px-4 align-middle whitespace-nowrap">
                                <div class="font-semibold text-base-content">{{ $s['nama'] }}</div>
                                <div class="text-[11px] text-base-content/60 font-mono mt-0.5">
                                    {{ $s['tanggal'] ? $s['tanggal']->format('d M Y') : '-' }}
                                </div>
                            </td>

                            {{-- Jarak --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap font-mono text-xs">
                                {{ $s['jarak'] }}m
                            </td>

                            {{-- End --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap font-mono text-xs">
                                {{ $s['jumlah_end'] }}
                            </td>

                            {{-- Total Skor --}}
                            <td class="py-3 px-4 text-right align-middle whitespace-nowrap">
                                <span class="font-bold text-sm text-primary">
                                    {{ number_format($s['total_skor']) }}
                                </span>
                            </td>

                            {{-- Rata End --}}
                            <td class="py-3 px-4 text-right align-middle whitespace-nowrap font-mono text-xs">
                                {{ $s['rata_end'] }}
                            </td>

                            {{-- Stabilitas SD --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap">
                                @if (!is_null($s['standar_deviasi']))
                                    <span class="badge badge-xs sm:badge-sm {{ $s['stabilitas_class'] }} font-mono font-medium">
                                        ±{{ $s['standar_deviasi'] }}
                                    </span>
                                @else
                                    <span class="text-base-content/40">-</span>
                                @endif
                            </td>

                            {{-- Total Panah --}}
                            <td class="py-3 px-4 text-center align-middle whitespace-nowrap font-mono text-xs">
                                {{ $s['total_panah'] }}
                            </td>

                            {{-- Catatan Pelatih & Status Baca --}}
                            <td class="py-3 px-4 align-middle max-w-xs">
                                @if ($s['catatan_pelatih'])
                                    <div class="text-xs text-base-content/90 line-clamp-2">
                                        "{{ $s['catatan_pelatih'] }}"
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        @if ($s['is_dibaca'])
                                            <span class="badge badge-xs bg-primary/20 text-primary border-primary/30 inline-flex items-center gap-1">
                                                <x-display.icon name="check" class="size-2.5 shrink-0" />
                                                <span>Dibaca atlet {{ $s['catatan_dibaca_at'] ? $s['catatan_dibaca_at']->format('d/m H:i') : '' }}</span>
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

                            {{-- Aksi Edit Catatan --}}
                            <td class="py-3 px-4 text-right align-middle whitespace-nowrap">
                                <button type="button"
                                    @click="openModal({{ $s['sesi_id'] }}, '{{ addslashes($s['nama']) }}', '{{ addslashes($s['catatan_pelatih'] ?? '') }}')"
                                    class="btn btn-xs btn-outline border-primary text-primary hover:bg-primary hover:text-white hover:border-primary gap-1"
                                    title="Edit Catatan">
                                    <x-display.icon name="edit" class="size-3" />
                                    <span>Catatan</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="9">
                            Belum ada riwayat sesi latihan untuk atlet ini.
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
                        Catatan Evaluasi: <span class="text-primary" x-text="activeNamaSesi"></span>
                    </h3>
                    <button type="button" @click="modalOpen = false" class="btn btn-ghost btn-xs btn-circle">✕</button>
                </div>

                <form :action="'{{ url('pelatih/sesi') }}/' + activeSesiId + '/catatan'" method="POST" class="mt-4">
                    @csrf
                    <div class="form-control">
                        <label class="label text-xs font-semibold text-base-content">
                            <span>Saran Koreksi Teknik & Catatan Performa</span>
                        </label>
                        <textarea name="catatan_pelatih" x-model="activeCatatan" rows="4" maxlength="1000"
                            placeholder="Tuliskan catatan teknis (sikap berdiri, release, kliker, konsistensi tarikan)..."
                            class="textarea textarea-bordered w-full border-base-300 focus:border-primary text-xs sm:text-sm"></textarea>
                        <label class="label text-[11px] text-base-content/50">
                            <span>Catatan akan dikirim ke dashboard atlet dan status 'sudah dibaca' akan di-reset.</span>
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

@push('scripts')
    <script type="module">
        const chartEl = document.getElementById('analisisChart');
        if (chartEl) {
            new window.Chart(chartEl, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Total Skor Sesi',
                            data: @json($dataSkor),
                            borderColor: '#2E7D32',
                            backgroundColor: '#2E7D32',
                            tension: 0.2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2.5,
                        },
                        {
                            label: 'Tren 4 Sesi (Moving Average)',
                            data: @json($dataMovingAvg),
                            borderColor: '#15803D',
                            backgroundColor: '#15803D',
                            borderDash: [6, 4],
                            tension: 0.3,
                            pointRadius: 2,
                            borderWidth: 2,
                        }
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            padding: 10,
                            bodyFont: {
                                size: 12
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                },
            });
        }
    </script>
@endpush
