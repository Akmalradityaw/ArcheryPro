@extends('layouts.app')

@section('title', 'Input Skor Latihan')
@section('page-title', 'Input Skor Latihan')

@section('content')
    <x-display.page-header title="Input Skor Latihan Rutin" subtitle="Pilih jadwal latihan mingguan, atlet peserta, dan masukkan skor tembakan per end" />

    <div x-data="inputSkor(@js($atlets->keyBy('id')))" x-init="initDraft()">
        {{-- Banner Notifikasi Pemulihan Draft (Offline-First Recovery) --}}
        <div x-show="hasDraft" x-cloak class="mb-5">
            <div class="alert bg-warning/15 border border-warning/30 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-4">
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-xl bg-warning/20 text-warning flex items-center justify-center shrink-0">
                        <x-display.icon name="clock" class="size-5" />
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-base-content">Draf Input Skor Tersimpan Lokal</h4>
                        <p class="text-[11px] sm:text-xs text-base-content/70">
                            Ditemukan data draf yang belum terkirim dari sesi sebelumnya (<span x-text="draftTimestamp"></span>).
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <button type="button" @click="restoreDraft()" class="btn btn-warning btn-xs sm:btn-sm rounded-xl">
                        Pulihkan Draf
                    </button>
                    <button type="button" @click="clearDraft()" class="btn btn-ghost btn-xs sm:btn-sm rounded-xl text-base-content/60">
                        Abaikan
                    </button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('scoring.input.konfirmasi') }}" @submit="onFormSubmit">
            @csrf

            {{-- Section: Info Sesi Latihan --}}
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5">
                <div class="card-body p-5 sm:p-6 lg:p-8">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-base-200">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-base-content/60">
                                Informasi Sesi Latihan
                            </h3>
                            <span class="text-xs text-primary font-medium flex items-center gap-1">
                                <span class="size-2 rounded-full bg-success inline-block"></span>
                                Auto-save aktif
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <x-form.input-field name="sesi_latihan_id" type="select" label="Jadwal Latihan Mingguan" required
                                    x-model="selectedSesi" @change="saveDraft()">
                                    <option value="">- Pilih Jadwal Latihan Mingguan -</option>
                                    @foreach ($sesiLatihans as $sl)
                                        <option value="{{ $sl->id }}">
                                            {{ $sl->tanggal ? $sl->tanggal->format('d/m/Y') : '-' }} — {{ $sl->nama_sesi }} ({{ ucfirst(str_replace('_', ' ', $sl->jenis_latihan)) }})
                                        </option>
                                    @endforeach
                                </x-form.input-field>
                            </div>

                            <div>
                                <x-form.input-field name="tanggal_sesi" type="date" label="Tanggal Latihan" required
                                    :value="date('Y-m-d')" x-model="tanggalSesi" @change="saveDraft()" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                            <div>
                                <x-form.input-field name="jarak_meter" type="select" label="Jarak Tembak (Meter)"
                                    x-model="jarakMeter" @change="saveDraft()"
                                    :options="[
                                        '10' => '10 Meter (Bantalan Dasar)',
                                        '15' => '15 Meter',
                                        '18' => '18 Meter (Indoor)',
                                        '20' => '20 Meter',
                                        '30' => '30 Meter (Standar Pemula)',
                                        '40' => '40 Meter',
                                        '50' => '50 Meter',
                                        '60' => '60 Meter',
                                        '70' => '70 Meter',
                                    ]" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-form.input-field name="catatan_pelatih" label="Catatan Evaluasi Sesi (Opsional)"
                                    placeholder="Contoh: Evaluasi akurasi & kestabilan rilis"
                                    x-model="catatanPelatih" @input="saveDraft()" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Pilih Atlet --}}
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5">
                <div class="card-body p-5 sm:p-6 lg:p-8">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3 pb-2 border-b border-base-200">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-base-content/60">
                                    Pilih Atlet Hadir Latihan
                                </h3>
                                <p class="text-[11px] text-base-content/50">Centang atlet yang mengikuti skoring latihan ini</p>
                            </div>

                            {{-- Checkbox Pilih Semua --}}
                            <label for="pilih-semua-atlet"
                                class="inline-flex items-center gap-2 cursor-pointer select-none rounded-lg px-2.5 py-1.5 hover:bg-base-200/60 transition-colors">
                                <input type="checkbox" id="pilih-semua-atlet"
                                    class="checkbox checkbox-primary checkbox-sm rounded-md"
                                    :checked="order.length > 0 && order.length === totalAtlet"
                                    @change="toggleSemua($event.target.checked)">
                                <span class="text-xs font-semibold text-base-content/80">Pilih Semua</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($atlets as $atlet)
                                <x-form.checkbox-atlet :id="$atlet->id" :nama="$atlet->nama_lengkap"
                                    :sub="($atlet->sekolah->nama_sekolah ?? '-') . ($atlet->kategori ? ' • ' . $atlet->kategori->nama_kategori : '')"
                                    x-bind:checked="open[{{ $atlet->id }}] === true"
                                    @change="toggle({{ $atlet->id }}, $event.target.checked)" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section: Input Skor per End (Alpine Template) --}}
            <template x-for="id in order" :key="id">
                <div x-show="open[id]" class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5 transition-all">
                    <div class="card-body p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-3 mb-4 pb-3 border-b border-base-200">
                            <div class="flex items-center gap-3">
                                <div
                                    class="size-9 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                                    <span x-text="names[id].charAt(0).toUpperCase()"></span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-base-content tracking-tight" x-text="names[id]"></h3>
                                    <p class="text-[10px] text-base-content/50" x-text="`Total: ${hitungTotalAtlet(id)} poin`"></p>
                                </div>
                            </div>

                            <span class="badge badge-sm badge-ghost font-mono" x-text="`${ends[id].length} End`"></span>
                        </div>

                        <template x-for="(end, ei) in ends[id]" :key="ei">
                            <div class="mb-4 p-3 bg-base-200/30 rounded-xl border border-base-200">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-base-content/60">
                                        End <span x-text="ei + 1"></span>
                                        <span class="text-[10px] font-normal text-base-content/50 ml-2"
                                            x-text="`Subtotal: ${hitungSubtotalEnd(id, ei)} poin`"></span>
                                    </p>

                                    <button type="button" x-show="ends[id].length > 1" @click="hapusEnd(id, ei)"
                                        class="btn btn-ghost btn-xs rounded-lg gap-1 text-error hover:bg-error/10"
                                        title="Hapus end ini">
                                        <x-display.icon name="trash" class="size-3.5 shrink-0" />
                                        <span class="hidden sm:inline">Hapus End</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-6 gap-1 sm:gap-2">
                                    <template x-for="si in 6" :key="si">
                                        <input type="number" min="0" max="10" required
                                            x-model="ends[id][ei][si - 1]" :name="`skor[${id}][${ei}][${si - 1}]`"
                                            placeholder="-"
                                            @input="onScoreInput($event, id, ei, si - 1)"
                                            class="input input-bordered input-sm w-full text-center px-0.5 sm:px-1 text-xs sm:text-sm font-bold text-base-content rounded-lg focus:input-primary">
                                    </template>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addEnd(id)"
                            class="btn btn-ghost btn-sm rounded-xl gap-1.5 text-primary hover:bg-primary/10">
                            <x-display.icon name="plus" class="size-3.5 shrink-0" />
                            <span>Tambah End</span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- Footer Actions --}}
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                <div class="card-body p-4 sm:p-5">
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2.5">
                        <a href="{{ route('scoring.dashboard') }}"
                            class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                            Kembali ke Dashboard
                        </a>

                        <x-form.btn-primary type="submit" size="sm"
                            class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                            <x-display.icon name="check" class="size-4" />
                            <span>Lanjut &amp; Konfirmasi Skor</span>
                        </x-form.btn-primary>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function inputSkor(atlets) {
            const DRAFT_KEY = 'archerypro_scoring_draft';
            const names = {};
            Object.values(atlets).forEach(a => names[a.id] = a.nama_lengkap);

            return {
                names,
                open: {},
                ends: {},
                order: [],
                totalAtlet: Object.keys(atlets).length,
                selectedSesi: '',
                tanggalSesi: '{{ date('Y-m-d') }}',
                jarakMeter: '30',
                catatanPelatih: '',
                hasDraft: false,
                draftTimestamp: '',

                initDraft() {
                    try {
                        const raw = localStorage.getItem(DRAFT_KEY);
                        if (raw) {
                            const d = JSON.parse(raw);
                            if (d && d.order && d.order.length > 0) {
                                this.hasDraft = true;
                                this.draftTimestamp = d.savedAt || 'beberapa saat lalu';
                            }
                        }
                    } catch (e) {
                        console.warn('Gagal membaca draf lokal', e);
                    }
                },

                restoreDraft() {
                    try {
                        const raw = localStorage.getItem(DRAFT_KEY);
                        if (raw) {
                            const d = JSON.parse(raw);
                            this.open = d.open || {};
                            this.ends = d.ends || {};
                            this.order = d.order || [];
                            this.selectedSesi = d.selectedSesi || '';
                            this.tanggalSesi = d.tanggalSesi || '{{ date('Y-m-d') }}';
                            this.jarakMeter = d.jarakMeter || '30';
                            this.catatanPelatih = d.catatanPelatih || '';
                        }
                    } catch (e) {
                        console.error('Gagal memulihkan draf', e);
                    }
                    this.hasDraft = false;
                },

                clearDraft() {
                    localStorage.removeItem(DRAFT_KEY);
                    this.hasDraft = false;
                },

                saveDraft() {
                    try {
                        const payload = {
                            open: this.open,
                            ends: this.ends,
                            order: this.order,
                            selectedSesi: this.selectedSesi,
                            tanggalSesi: this.tanggalSesi,
                            jarakMeter: this.jarakMeter,
                            catatanPelatih: this.catatanPelatih,
                            savedAt: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                        };
                        localStorage.setItem(DRAFT_KEY, JSON.stringify(payload));
                    } catch (e) {
                        console.warn('Gagal menyimpan draf lokal', e);
                    }
                },

                onFormSubmit() {
                    // Bersihkan draf setelah berhasil submit ke konfirmasi
                    localStorage.removeItem(DRAFT_KEY);
                },

                toggle(id, on) {
                    this.open[id] = on;
                    if (on && !this.ends[id]) {
                        this.ends[id] = [
                            [null, null, null, null, null, null]
                        ];
                        this.order.push(id);
                    }
                    if (!on) {
                        delete this.ends[id];
                        this.order = this.order.filter(x => x !== id);
                    }
                    this.saveDraft();
                },

                toggleSemua(on) {
                    Object.keys(this.names).forEach(id => {
                        const numericId = Number(id);
                        if (on && !this.open[numericId]) {
                            this.toggle(numericId, true);
                        } else if (!on && this.open[numericId]) {
                            this.toggle(numericId, false);
                        }
                    });
                },

                addEnd(id) {
                    this.ends[id].push([null, null, null, null, null, null]);
                    this.saveDraft();
                },

                hapusEnd(id, ei) {
                    this.ends[id].splice(ei, 1);
                    if (this.ends[id].length === 0) {
                        this.toggle(id, false);
                    }
                    this.saveDraft();
                },

                onScoreInput(event, id, ei, si) {
                    let val = event.target.value;
                    if (val !== '') {
                        let parsed = parseInt(val) || 0;
                        parsed = Math.max(0, Math.min(10, parsed));
                        this.ends[id][ei][si] = parsed;
                        event.target.value = parsed;
                    } else {
                        this.ends[id][ei][si] = null;
                    }
                    this.saveDraft();
                },

                hitungSubtotalEnd(id, ei) {
                    if (!this.ends[id] || !this.ends[id][ei]) return 0;
                    return this.ends[id][ei].reduce((acc, cur) => acc + (parseInt(cur) || 0), 0);
                },

                hitungTotalAtlet(id) {
                    if (!this.ends[id]) return 0;
                    return this.ends[id].reduce((total, end) => {
                        return total + end.reduce((acc, cur) => acc + (parseInt(cur) || 0), 0);
                    }, 0);
                }
            };
        }
    </script>
@endpush
