<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px; font-family: inherit;">
        
        <!-- Selectors Card -->
        <x-filament::section>
            <x-slot name="heading">
                <span style="font-size: 16px; font-weight: 700;">Filter Simulasi Sensitivitas</span>
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
                    Belum ada data responden yang valid untuk periode ini. Silakan impor data kuesioner terlebih dahulu.
                </p>
            </x-filament::section>
        @else
            <!-- Weight Simulation Sliders Panel -->
            <x-filament::section>
                <x-slot name="heading">
                    <span style="font-size: 16px; font-weight: 700;">Simulasi Perubahan Bobot Kriteria</span>
                </x-slot>
                <x-slot name="description">
                    Geser slider kriteria di bawah ini untuk mensimulasikan perubahan bobot kepentingan pelayanan. Sistem akan otomatis menormalisasi bobot agar jumlah totalnya tetap 1.0 (100%).
                </x-slot>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 16px;">
                    @foreach($criteria as $c)
                        @php $cId = $c['id']; @endphp
                        <div style="padding: 16px; border: 1px solid rgba(128,128,128,0.2); border-radius: 12px; background: rgba(128,128,128,0.02); display: flex; flex-direction: column; gap: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <span style="font-size: 11px; font-weight: 800; font-family: monospace; background: rgba(128,128,128,0.15); padding: 2px 6px; border-radius: 4px; color: gray; margin-right: 4px;">{{ $c['code'] }}</span>
                                    <span style="font-size: 13px; font-weight: 700;">{{ $c['name'] }}</span>
                                </div>
                            </div>

                            <!-- Weight Stats comparison -->
                            <div style="display: flex; justify-content: space-between; font-size: 11px; color: gray; margin-top: 4px;">
                                <span>Bobot Asli: <strong>{{ number_format($originalWeights[$cId] * 100, 1) }}%</strong></span>
                                <span>Bobot Simulasi: <strong style="color: var(--primary-600); font-size: 12px;">{{ number_format(($normalizedWeights[$cId] ?? 0.0) * 100, 1) }}%</strong></span>
                            </div>

                            <!-- Slider Input -->
                            <div style="display: flex; align-items: center; gap: 12px; margin-top: 4px;">
                                <input type="range" 
                                       min="0" 
                                       max="100" 
                                       wire:model.live="sliderWeights.{{ $cId }}" 
                                       style="flex: 1; accent-color: var(--primary-600); cursor: pointer; height: 6px; border-radius: 3px;">
                                <span style="font-family: monospace; font-size: 12px; font-weight: 700; width: 30px; text-align: right;">{{ $sliderWeights[$cId] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
                    <x-filament::button wire:click="resetSliders" color="gray" size="sm">
                        Kembalikan ke Bobot AHP Asli
                    </x-filament::button>
                </div>
            </x-filament::section>

            <!-- Rankings Side-by-Side Comparison -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px;">
                
                <!-- Stored / Original AHP Ranking -->
                <x-filament::section>
                    <x-slot name="heading">
                        <span style="font-size: 15px; font-weight: 700; color: gray;">Ranking Asli (Bobot AHP Database)</span>
                    </x-slot>
                    <x-slot name="description">
                        Peringkat bangsal menggunakan hasil pembobotan AHP asli yang tersimpan di database.
                    </x-slot>

                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 12px;">
                        @php $originalRankMap = []; @endphp
                        @foreach($originalResults['rankings'] as $item)
                            @php $originalRankMap[$item['ward_id']] = $item['rank']; @endphp
                            <div style="padding: 12px; border: 1px solid rgba(128,128,128,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: space-between; background: rgba(128,128,128,0.01);">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border-radius: 50%; background: rgba(128,128,128,0.1); color: gray;">
                                        #{{ $item['rank'] }}
                                    </span>
                                    <strong style="font-size: 13px;">{{ $item['ward_name'] }}</strong>
                                </div>
                                <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: gray;">
                                    V = {{ number_format($item['preference_value'], 4) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>

                <!-- Simulated / Sensitivity Ranking -->
                <x-filament::section>
                    <x-slot name="heading">
                        <span style="font-size: 15px; font-weight: 700; color: var(--primary-600);">Ranking Simulasi (Analisis Sensitivitas)</span>
                    </x-slot>
                    <x-slot name="description">
                        Peringkat bangsal yang terhitung otomatis secara real-time berdasarkan bobot simulasi di atas.
                    </x-slot>

                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 12px;">
                        @foreach($simulatedResults['rankings'] as $item)
                            @php 
                                $origRank = $originalRankMap[$item['ward_id']] ?? $item['rank'];
                                $rankDiff = $origRank - $item['rank']; // Positive if rank improved (1 is better than 2)
                            @endphp
                            <div style="padding: 12px; border: 1px solid {{ $rankDiff > 0 ? '#10b981' : ($rankDiff < 0 ? '#ef4444' : 'rgba(128,128,128,0.15)') }}; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; background: {{ $rankDiff > 0 ? 'rgba(16,185,129,0.03)' : ($rankDiff < 0 ? 'rgba(239,68,68,0.03)' : 'rgba(128,128,128,0.01)') }}; transition: all 0.2s;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <!-- Rank circle badge -->
                                    <span style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; border-radius: 50%; 
                                                 background: {{ $item['rank'] === 1 ? 'rgba(245,158,11,0.2)' : 'rgba(128,128,128,0.1)' }}; 
                                                 color: {{ $item['rank'] === 1 ? '#f59e0b' : 'inherit' }};">
                                        #{{ $item['rank'] }}
                                    </span>
                                    
                                    <div>
                                        <strong style="font-size: 13px; display: block;">{{ $item['ward_name'] }}</strong>
                                        
                                        <!-- Rank difference badge -->
                                        @if($rankDiff > 0)
                                            <span style="font-size: 10px; font-weight: bold; color: #10b981; display: inline-flex; align-items: center; gap: 2px;">
                                                ▲ Naik {{ $rankDiff }} tingkat (sebelumnya #{{ $origRank }})
                                            </span>
                                        @elseif($rankDiff < 0)
                                            <span style="font-size: 10px; font-weight: bold; color: #ef4444; display: inline-flex; align-items: center; gap: 2px;">
                                                ▼ Turun {{ abs($rankDiff) }} tingkat (sebelumnya #{{ $origRank }})
                                            </span>
                                        @else
                                            <span style="font-size: 10px; font-weight: 600; color: gray;">
                                                = Tetap
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <span style="font-family: monospace; font-size: 13px; font-weight: 700; color: var(--primary-600);">
                                    V = {{ number_format($item['preference_value'], 4) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>

            </div>

            <!-- Recommendation Engine Panel (Berdasarkan Hasil Asli) -->
            <x-filament::section>
                <x-slot name="heading">
                    <span style="font-size: 16px; font-weight: 700;">Rekomendasi Keputusan Mutu Pelayanan (Automated Recommendation Engine)</span>
                </x-slot>
                <x-slot name="description">
                    Sistem mendeteksi secara otomatis kriteria pelayanan yang paling membutuhkan perbaikan taktis untuk masing-masing bangsal (skor di bawah 3.8/5.0).
                </x-slot>

                <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 12px;">
                    @foreach($originalResults['rankings'] as $item)
                        <div style="padding: 16px; border: 1px solid rgba(128,128,128,0.2); border-radius: 12px; background: rgba(128,128,128,0.02);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <h4 style="font-size: 14px; font-weight: 700;">
                                    Peringkat #{{ $item['rank'] }} — {{ $item['ward_name'] }}
                                </h4>
                                <span style="font-size: 11px; font-weight: 700; color: gray;">
                                    Nilai Kepuasan Akhir: <strong style="color: var(--primary-600);">{{ number_format($item['preference_value'] * 100, 1) }}%</strong>
                                </span>
                            </div>
                            
                            <div style="font-size: 12.5px; color: inherit; line-height: 1.6; white-space: pre-line;">
                                {!! Str::markdown($item['recommendation']) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

    </div>
</x-filament-panels::page>
