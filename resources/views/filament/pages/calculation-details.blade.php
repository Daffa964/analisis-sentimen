<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px; font-family: inherit;">
        
        <!-- Selectors Card -->
        <x-filament::section>
            <x-slot name="heading">
                <span style="font-size: 16px; font-weight: 700;">Filter Analisis Kualitas Pelayanan (AHP-SAW)</span>
            </x-slot>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-top: 12px;">
                <!-- Period Selector -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: gray;">Pilih Periode Survei</label>
                    <select wire:model.live="selectedPeriodId" 
                            style="width: 100%; padding: 8px 12px; font-size: 13px; border-radius: 8px; border: 1px solid rgba(128,128,128,0.3); background: transparent; color: inherit;">
                        <option value="" style="background: var(--gray-900); color: #fff;">-- Pilih Periode --</option>
                        @foreach($periods as $p)
                            <option value="{{ $p['id'] }}" style="background: var(--gray-900); color: #fff;">
                                {{ $p['name'] }} ({{ \Carbon\Carbon::parse($p['start_date'])->format('d M Y') }} - {{ \Carbon\Carbon::parse($p['end_date'])->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Selector (Regular / VIP) -->
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: gray;">Kategori Bangsal</label>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" 
                                wire:click="$set('selectedCategory', 'regular')"
                                style="flex: 1; padding: 8px 12px; font-size: 13px; border-radius: 8px; border: 1px solid {{ $selectedCategory === 'regular' ? 'var(--primary-600)' : 'rgba(128,128,128,0.3)' }}; background: {{ $selectedCategory === 'regular' ? 'var(--primary-600)' : 'transparent' }}; color: {{ $selectedCategory === 'regular' ? '#fff' : 'inherit' }}; font-weight: bold; cursor: pointer; transition: all 0.2s;">
                            Regular (Non-VIP)
                        </button>
                        <button type="button" 
                                wire:click="$set('selectedCategory', 'vip')"
                                style="flex: 1; padding: 8px 12px; font-size: 13px; border-radius: 8px; border: 1px solid {{ $selectedCategory === 'vip' ? 'var(--primary-600)' : 'rgba(128,128,128,0.3)' }}; background: {{ $selectedCategory === 'vip' ? 'var(--primary-600)' : 'transparent' }}; color: {{ $selectedCategory === 'vip' ? '#fff' : 'inherit' }}; font-weight: bold; cursor: pointer; transition: all 0.2s;">
                            VIP / VVIP
                        </button>
                    </div>
                </div>
            </div>
        </x-filament::section>

        @if(!$hasData)
            <!-- Empty State Card -->
            <x-filament::section style="text-align: center; padding: 48px 24px;">
                <div style="display: inline-flex; padding: 16px; background: rgba(128,128,128,0.05); border-radius: 50%; color: gray; margin-bottom: 16px;">
                    <svg style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Tidak Ada Data Penilaian</h3>
                <p style="font-size: 13px; color: gray; max-width: 400px; margin: 0 auto; line-height: 1.5;">
                    Belum ada responden yang mengisi kuesioner untuk periode dan kategori bangsal ini, atau data kriteria/bobot belum diatur. Silakan unggah kuesioner terlebih dahulu.
                </p>
            </x-filament::section>
        @else
            <!-- SAW Results & Rankings -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Rank Results -->
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 12px;">
                            <span style="font-size: 16px; font-weight: 700;">Hasil Perangkingan Kualitas Pelayanan (Metode SAW)</span>
                            <a href="{{ route('report.print', ['period_id' => $selectedPeriodId, 'category' => $selectedCategory]) }}" 
                               target="_blank"
                               style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 16px; font-size: 13px; font-weight: bold; background: var(--primary-600); color: white; border-radius: 8px; border: none; cursor: pointer; text-decoration: none; transition: background 0.2s;">
                                <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Laporan
                            </a>
                        </div>
                    </x-slot>

                    <div style="overflow-x: auto; margin-top: 12px;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 500px; border: 1px solid rgba(128,128,128,0.2);">
                            <thead>
                                <tr style="background: rgba(128,128,128,0.05); border-bottom: 2px solid rgba(128,128,128,0.2);">
                                    <th style="padding: 12px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: center; width: 80px;">Peringkat</th>
                                    <th style="padding: 12px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left;">Nama Bangsal</th>
                                    <th style="padding: 12px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: center; width: 180px;">Nilai Preferensi (V)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sawResults['rankings'] as $item)
                                    <tr style="border-bottom: 1px solid rgba(128,128,128,0.15);">
                                        <td style="padding: 10px; border: 1px solid rgba(128,128,128,0.2); text-align: center;">
                                            @if($item['rank'] == 1)
                                                <span style="display: inline-flex; align-items: center; justify-content: center; w-8 h-8; width: 28px; height: 28px; rounded-full; border-radius: 50%; bg-amber-100; background: rgba(245,158,11,0.2); text-amber-800; color: #f59e0b; font-extrabold; font-weight: 800; font-size: 13px; border: 1px solid rgba(245,158,11,0.4);">1</span>
                                            @elseif($item['rank'] == 2)
                                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(128,128,128,0.2); color: inherit; font-weight: 800; font-size: 13px; border: 1px solid rgba(128,128,128,0.4);">2</span>
                                            @elseif($item['rank'] == 3)
                                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(249,115,22,0.2); color: #f97316; font-weight: 800; font-size: 13px; border: 1px solid rgba(249,115,22,0.4);">3</span>
                                            @else
                                                <span style="font-weight: 700; color: gray;">{{ $item['rank'] }}</span>
                                            @endif
                                        </td>
                                        <td style="padding: 12px; border: 1px solid rgba(128,128,128,0.2); font-weight: 600;">{{ $item['ward_name'] }}</td>
                                        <td style="padding: 12px; border: 1px solid rgba(128,128,128,0.2); text-align: center; font-family: monospace; font-weight: 700; color: var(--primary-600); background: rgba(128,128,128,0.02);">
                                            {{ number_format($item['preference_value'], 4) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>

                <!-- Step 1: Decision Matrix -->
                <x-filament::section>
                    <x-slot name="heading">
                        <span style="font-size: 16px; font-weight: 700;">Langkah 1: Matriks Keputusan (X)</span>
                    </x-slot>
                    <x-slot name="description">
                        Matriks keputusan dibentuk dari nilai rata-rata penilaian responden pada setiap bangsal untuk masing-masing kriteria (C1 s/d C6).
                    </x-slot>

                    <div style="overflow-x: auto; margin-top: 12px;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 500px; text-align: center; border: 1px solid rgba(128,128,128,0.2);">
                            <thead>
                                <tr style="background: rgba(128,128,128,0.05); border-bottom: 2px solid rgba(128,128,128,0.2);">
                                    <th style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left;">Alternatif (Bangsal)</th>
                                    @foreach($criteria as $c)
                                        <th style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2);">{{ $c['code'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sawResults['rankings'] as $item)
                                    @php $wId = $item['ward_id']; @endphp
                                    <tr style="border-bottom: 1px solid rgba(128,128,128,0.15);">
                                        <td style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left; background: rgba(128,128,128,0.02);">{{ $item['ward_name'] }}</td>
                                        @foreach($criteria as $c)
                                            <td style="padding: 10px; font-size: 12px; font-family: monospace; border: 1px solid rgba(128,128,128,0.2); color: gray;">
                                                {{ number_format($sawResults['raw_matrix'][$wId][$c['id']], 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <!-- Max values row -->
                                <tr style="background: rgba(128,128,128,0.08); font-weight: bold; border-top: 2px solid rgba(128,128,128,0.3);">
                                    <td style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left;">Nilai Maksimum (Benefit)</td>
                                    @foreach($criteria as $c)
                                        <td style="padding: 10px; font-size: 12px; font-family: monospace; border: 1px solid rgba(128,128,128,0.2); color: var(--primary-600);">
                                            {{ number_format($sawResults['max_values'][$c['id']], 4) }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>

                <!-- Step 2: Normalized Matrix -->
                <x-filament::section>
                    <x-slot name="heading">
                        <span style="font-size: 16px; font-weight: 700;">Langkah 2: Matriks Ternormalisasi (R)</span>
                    </x-slot>
                    <x-slot name="description">
                        Ditransformasikan dengan membagi setiap sel nilai alternatif dengan nilai kolom maksimum: <code>r_ij = x_ij / max(x_j)</code> (karena semua kriteria adalah benefit).
                    </x-slot>

                    <div style="overflow-x: auto; margin-top: 12px;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 500px; text-align: center; border: 1px solid rgba(128,128,128,0.2);">
                            <thead>
                                <tr style="background: rgba(128,128,128,0.05); border-bottom: 2px solid rgba(128,128,128,0.2);">
                                    <th style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left;">Alternatif (Bangsal)</th>
                                    @foreach($criteria as $c)
                                        <th style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2);">{{ $c['code'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sawResults['rankings'] as $item)
                                    @php $wId = $item['ward_id']; @endphp
                                    <tr style="border-bottom: 1px solid rgba(128,128,128,0.15);">
                                        <td style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left; background: rgba(128,128,128,0.02);">{{ $item['ward_name'] }}</td>
                                        @foreach($criteria as $c)
                                            <td style="padding: 10px; font-size: 12px; font-family: monospace; border: 1px solid rgba(128,128,128,0.2); color: gray;">
                                                {{ number_format($sawResults['normalized_matrix'][$wId][$c['id']], 4) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>

                <!-- Step 3: Preference Aggregation Weights & Formula -->
                <x-filament::section>
                    <x-slot name="heading">
                        <span style="font-size: 16px; font-weight: 700;">Langkah 3: Perkalian Bobot & Penjumlahan Nilai Preferensi (V)</span>
                    </x-slot>
                    <x-slot name="description">
                        Nilai preferensi dihitung dengan mengalikan baris matriks ternormalisasi dengan bobot prioritas AHP kriteria yang berlaku: <code>V_i = Σ (w_j * r_ij)</code>.
                    </x-slot>

                    <div style="margin-top: 16px; padding: 16px; border-radius: 12px; border: 1px solid rgba(128,128,128,0.2); background: rgba(128,128,128,0.02); display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; font-size: 13px;">
                        <span style="font-weight: 700; color: gray; text-transform: uppercase;">Bobot Kriteria AHP yang Digunakan:</span>
                        @foreach($criteria as $c)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="padding: 2px 6px; font-size: 11px; font-weight: 700; font-family: monospace; background: rgba(128,128,128,0.15); border-radius: 4px;">{{ $c['code'] }}</span>
                                <strong style="font-family: monospace; color: var(--primary-600);">{{ number_format($sawResults['weights'][$c['id']], 4) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>

            </div>
        @endif

    </div>
</x-filament-panels::page>
