@extends('layouts.app')

@section('title', 'Grafik Performa Latihan')
@section('page-title', 'Grafik Performa Latihan')

@section('content')
    <div class="mb-4">
        <a href="{{ route('atlet.dashboard') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <x-display.page-header title="Grafik Performa Latihan" subtitle="Pantau kenaikan akurasi skor dan kestabilan tembakanmu" />

    {{-- Grid 4 Kartu Metrik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-display.card-stat title="Sesi Terakhir" :value="number_format($ringkasan['skor_terakhir']) . ' poin'" icon="target" color="primary"
            :trend="$ringkasan['selisih_terakhir'] > 0 ? ('+' . $ringkasan['selisih_terakhir'] . ' poin') : ($ringkasan['selisih_terakhir'] < 0 ? ($ringkasan['selisih_terakhir'] . ' poin') : null)"
            :trendType="$ringkasan['selisih_terakhir'] >= 0 ? 'up' : 'down'"
            :description="$ringkasan['selisih_terakhir'] == 0 ? 'Sama dengan sesi lalu' : 'Dibandingkan sesi lalu'" />

        <x-display.card-stat title="Rata-rata Skor" :value="number_format($ringkasan['skor_rata'], 1)" icon="chart-line" color="info"
            description="Akumulasi seluruh sesi" />

        <x-display.card-stat title="Skor Tertinggi" :value="number_format($ringkasan['skor_terbaik'])" icon="star" color="warning"
            description="Rekor latihan personal" />

        <x-display.card-stat title="Total Sesi" :value="$ringkasan['total_sesi'] . ' sesi'" icon="calendar" color="success"
            description="Tercatat di sistem" />
    </div>

    @if ($data->isEmpty())
        <x-feedback.empty-state title="Belum Ada Data Latihan" description="Skor latihanmu akan tampil di grafik ini setelah dicatat oleh pelatih atau petugas scoring." />
    @else
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="p-4 sm:p-5 border-b border-base-200">
                <h2 class="text-base font-bold text-base-content">Tren Akurasi & Moving Average 4 Sesi</h2>
                <p class="text-xs text-base-content/50 mt-0.5">Garis hijau solid menunjukkan skor aktual tiap sesi, garis putus-putus menunjukkan kestabilan rata-rata 4 sesi</p>
            </div>
            <div class="card-body p-4 sm:p-6">
                <div class="relative h-64 sm:h-80 lg:h-96">
                    <canvas id="grafik"></canvas>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script type="module">
        const grafikEl = document.getElementById('grafik');
        if (grafikEl) {
            new window.Chart(grafikEl, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [
                        {
                            label: 'Total Skor Sesi',
                            data: @json($data),
                            borderColor: '#2E7D32',
                            backgroundColor: '#2E7D32',
                            tension: 0.2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2.5,
                        },
                        {
                            label: 'Tren 4 Sesi (Moving Average)',
                            data: @json($movingAvg),
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
