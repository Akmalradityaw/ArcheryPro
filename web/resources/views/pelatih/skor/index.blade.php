@extends('layouts.app')

@section('title', 'Input Skor Latihan Mingguan')
@section('page-title', 'Input Skor Latihan')

@section('content')
    {{-- Back Link --}}
    <div class="mb-4">
        <a href="{{ route('pelatih.sesi-latihan.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Jadwal Latihan</span>
        </a>
    </div>

    <x-display.page-header title="Input Skor Latihan Mingguan"
        subtitle="Catat perolehan poin panahan atlet per end, evaluasi teknik tembakan, dan sertakan catatan kepelatihan" />

    <div x-data="pelatihInputSkor(@js($atlets->keyBy('id')), '{{ $selectedSesiId ?? '' }}')" x-init="initDraft()">
        {{-- Banner Notifikasi Pemulihan Draft (Offline-First Recovery) --}}
        <div x-show="hasDraft" x-cloak class="mb-5">
            <div
                class="alert bg-warning/15 border border-warning/30 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-4">
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-xl bg-warning/20 text-warning flex items-center justify-center shrink-0">
                        <x-display.icon name="clock" class="size-5" />
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-base-content">Draf Input Skor Pelatih Tersimpan Lokal</h4>
                        <p class="text-[11px] sm:text-xs text-base-content/70">
                            Ditemukan draf skoring latihan sebelumnya yang belum tersimpan (<span x-text="draftTimestamp"></span>).
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <button type="button" @click="restoreDraft()" class="btn btn-warning btn-xs sm:btn-sm rounded-xl">
                        Pulihkan Draf
                    </button>
                    <button type="button" @click="clearDraft()"
                        class="btn btn-ghost btn-xs sm:btn-sm rounded-xl text-base-content/60">
                        Abaikan
                    </button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('pelatih.skor.konfirmasi') }}" @submit="onFormSubmit">
            @csrf

            {{-- Section 1: Pengaturan Sesi Latihan --}}
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5">
                <div class="card-body p-5 sm:p-6 lg:p-8">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-base-200">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-base-content/60">
                                Informasi Sesi Latihan
                            </h3>
                            <span class="text-xs text-primary font-medium flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-success inline-block"></span>
                                Auto-save draf aktif
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <x-form.input-field name="sesi_latihan_id" type="select" label="Jadwal Latihan Mingguan" required
                                    x-model="selectedSesi" @change="saveDraft()">
                                    <option value="">- Pilih Sesi Latihan Mingguan -</option>
                                    @foreach ($sesiLatihans as $sl)
                                        <option value="{{ $sl->id }}">
                                            {{ $sl->tanggal ? $sl->tanggal->format('d/m/Y') : '-' }} — {{ $sl->nama_sesi }}
                                            ({{ ucfirst(str_replace('_', ' ', $sl->jenis_latihan)) }})
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
                                <x-form.input-field name="jarak_meter" type="select" label="Jarak Sasaran (Meter)"
                                    x-model="jarakMeter" @change="saveDraft()"
                                    :options="[
                                        '10' => '10 Meter (Bantalan Dasar)',
                                        '15' => '15 Meter',
                                        '18' => '18 Meter (Indoor World Archery)',
                                        '20' => '20 Meter',
                                        '30' => '30 Meter (Standar Pemula/Nasional)',
                                        '40' => '40 Meter',
                                        '50' => '50 Meter (Standar Barebow/Compound)',
                                        '60' => '60 Meter',
                                        '70' => '70 Meter (Standar Recurve Olimpiade)',
                                    ]" />
                            </div>

                            <div class="sm:col-span-2">
                                <x-form.input-field name="catatan_pelatih" label="Catatan &amp; Evaluasi Kepelatihan"
                                    placeholder="Contoh: Fokus perbaiki anchor point & konsistensi ritme release"
                                    x-model="catatanPelatih" @input="saveDraft()" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Pilih Atlet --}}
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5">
                <div class="card-body p-5 sm:p-6 lg:p-8">
                    <div class="space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-base-200">
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-base-content/60">
                                    Pilih Atlet Peserta Latihan
                                </h3>
                                <p class="text-[11px] text-base-content/50">Centang atlet yang hadir dan mengikuti scoring tembakan hari ini</p>
                            </div>

                            <div class="flex items-center gap-3">
                                {{-- Quick Search Atlet --}}
                                <div class="relative w-44 sm:w-56">
                                    <input type="text" x-model="searchAtlet" placeholder="Cari nama atlet..."
                                        class="input input-bordered input-xs sm:input-sm w-full pl-8 rounded-xl" />
                                    <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-base-content/40">
                                        <x-display.icon name="search" class="size-3.5" />
                                    </div>
                                </div>

                                {{-- Checkbox Pilih Semua --}}
                                <label for="pilih-semua-atlet-pelatih"
                                    class="inline-flex items-center gap-2 cursor-pointer select-none rounded-lg px-2.5 py-1.5 hover:bg-base-200/60 transition-colors shrink-0">
                                    <input type="checkbox" id="pilih-semua-atlet-pelatih"
                                        class="checkbox checkbox-primary checkbox-sm rounded-md"
                                        :checked="order.length > 0 && order.length === totalAtlet"
                                        @change="toggleSemua($event.target.checked)">
                                    <span class="text-xs font-semibold text-base-content/80">Semua</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach ($atlets as $atlet)
                                <div x-show="matchesSearch('{{ strtolower($atlet->nama_lengkap) }}', '{{ strtolower($atlet->sekolah->nama_sekolah ?? '') }}')">
                                    <x-form.checkbox-atlet :id="$atlet->id" :nama="$atlet->nama_lengkap"
                                        :sub="($atlet->sekolah->nama_sekolah ?? '-') . ($atlet->kategori ? ' • ' . $atlet->kategori->nama_kategori : '')"
                                        x-bind:checked="open[{{ $atlet->id }}] === true"
                                        @change="toggle({{ $atlet->id }}, $event.target.checked)" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Input Skor per End (Alpine Template) --}}
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
                                    <p class="text-[11px] text-base-content/50" x-text="`Total: ${hitungTotalAtlet(id)} poin`"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="badge badge-sm badge-ghost font-mono" x-text="`${ends[id].length} End`"></span>
                                <span class="badge badge-sm badge-primary text-white font-mono font-bold" x-text="`${hitungTotalAtlet(id)} Poin`"></span>
                            </div>
                        </div>

                        <template x-for="(end, ei) in ends[id]" :key="ei">
                            <div class="mb-4 p-3.5 bg-base-200/30 rounded-xl border border-base-200">
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <p class="text-xs font-bold uppercase tracking-wider text-base-content/70">
                                        End <span x-text="ei + 1"></span>
                                        <span class="text-[11px] font-normal text-base-content/50 ml-2"
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
                        <a href="{{ route('pelatih.sesi-latihan.index') }}"
                            class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                            Kembali ke Jadwal
                        </a>

                        <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                            <button type="submit" formaction="{{ route('pelatih.skor.store') }}"
                                class="btn btn-outline btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                                <x-display.icon name="document-duplicate" class="size-4" />
                                <span>Simpan Langsung</span>
                            </button>

                            <x-form.btn-primary type="submit" size="sm"
                                class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                                <x-display.icon name="check" class="size-4" />
                                <span>Lanjut &amp; Tinjau Konfirmasi</span>
                            </x-form.btn-primary>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function pelatihInputSkor(atlets, defaultSesiId) {
            const DRAFT_KEY = 'archerypro_pelatih_skor_draft';
            const names = {};
            Object.values(atlets).forEach(a => names[a.id] = a.nama_lengkap);

            return {
                names,
                open: {},
                ends: {},
                order: [],
                searchAtlet: '',
                totalAtlet: Object.keys(atlets).length,
                selectedSesi: defaultSesiId || '',
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
                        console.warn('Gagal membaca draf lokal pelatih', e);
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
                            if (!this.selectedSesi && d.selectedSesi) {
                                this.selectedSesi = d.selectedSesi;
                            }
                            this.tanggalSesi = d.tanggalSesi || '{{ date('Y-m-d') }}';
                            this.jarakMeter = d.jarakMeter || '30';
                            this.catatanPelatih = d.catatanPelatih || '';
                        }
                    } catch (e) {
                        console.error('Gagal memulihkan draf pelatih', e);
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
                        console.warn('Gagal menyimpan draf pelatih', e);
                    }
                },

                matchesSearch(nama, sekolah) {
                    if (!this.searchAtlet) return true;
                    const q = this.searchAtlet.toLowerCase().trim();
                    return nama.includes(q) || sekolah.includes(q);
                },

                onFormSubmit() {
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
