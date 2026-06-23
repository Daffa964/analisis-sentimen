<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px; font-family: inherit;">
        
        <!-- Header Info Section -->
        <x-filament::section>
            <x-slot name="heading">
                <span style="font-size: 18px; font-weight: 700;">Panduan Pembobotan Kriteria AHP</span>
            </x-slot>
            <x-slot name="description">
                Metode <strong>Analytic Hierarchy Process (AHP)</strong> digunakan untuk menentukan bobot prioritas dari 6 kriteria kepuasan pelayanan rawat inap. Silakan bandingkan tingkat kepentingan antar kriteria berdasarkan Skala Saaty (1-9).
            </x-slot>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-top: 16px;">
                <div style="padding: 12px; border-radius: 8px; border: 1px solid rgba(128,128,128,0.2); background: rgba(128,128,128,0.02);">
                    <strong style="color: var(--primary-600); display: block; font-size: 13px;">Skor 1</strong>
                    <span style="font-size: 12px; color: gray;">Sama pentingnya</span>
                </div>
                <div style="padding: 12px; border-radius: 8px; border: 1px solid rgba(128,128,128,0.2); background: rgba(128,128,128,0.02);">
                    <strong style="color: var(--primary-600); display: block; font-size: 13px;">Skor 3</strong>
                    <span style="font-size: 12px; color: gray;">Sedikit lebih penting</span>
                </div>
                <div style="padding: 12px; border-radius: 8px; border: 1px solid rgba(128,128,128,0.2); background: rgba(128,128,128,0.02);">
                    <strong style="color: var(--primary-600); display: block; font-size: 13px;">Skor 5</strong>
                    <span style="font-size: 12px; color: gray;">Lebih penting</span>
                </div>
                <div style="padding: 12px; border-radius: 8px; border: 1px solid rgba(128,128,128,0.2); background: rgba(128,128,128,0.02);">
                    <strong style="color: var(--primary-600); display: block; font-size: 13px;">Skor 7</strong>
                    <span style="font-size: 12px; color: gray;">Sangat lebih penting</span>
                </div>
                <div style="padding: 12px; border-radius: 8px; border: 1px solid rgba(128,128,128,0.2); background: rgba(128,128,128,0.02);">
                    <strong style="color: var(--primary-600); display: block; font-size: 13px;">Skor 9</strong>
                    <span style="font-size: 12px; color: gray;">Mutlak lebih penting</span>
                </div>
            </div>
        </x-filament::section>

        <!-- Main Form Section -->
        <form wire:submit.prevent="calculate" style="display: flex; flex-direction: column; gap: 24px;">
            <x-filament::section>
                <x-slot name="heading">
                    <span style="font-size: 16px; font-weight: 700;">Bandingkan Pasangan Kriteria</span>
                </x-slot>

                <div style="display: flex; flex-direction: column; gap: 16px; max-height: 500px; overflow-y: auto; padding-right: 8px;">
                    @php $index = 1; @endphp
                    @foreach($criteria as $i => $c1)
                        @foreach($criteria as $j => $c2)
                            @if($c1['id'] < $c2['id'])
                                @php $key = "{$c1['id']}_{$c2['id']}"; @endphp
                                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 24px; padding: 20px 0; border-bottom: 1px solid rgba(128,128,128,0.15);">
                                    
                                    <!-- Comparison Label -->
                                    <div style="flex: 1; min-width: 250px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; background: rgba(128,128,128,0.1); border-radius: 50%; color: gray;">
                                                {{ $index++ }}
                                            </span>
                                            <strong style="font-size: 14px;">{{ $c1['code'] }} vs {{ $c2['code'] }}</strong>
                                        </div>
                                        <div style="font-size: 12px; color: gray; margin-top: 4px; padding-left: 32px;">
                                            {{ $c1['name'] }} dibanding {{ $c2['name'] }}
                                        </div>
                                    </div>

                                    <!-- 9-Button Grid Interface -->
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 340px;">
                                        <div style="display: flex; justify-content: space-between; width: 100%; font-size: 11px; font-weight: 600; padding: 0 4px; color: gray;">
                                            <span>← Lebih Penting ({{ $c1['code'] }})</span>
                                            <span style="color: gray;">Sama</span>
                                            <span>Lebih Penting ({{ $c2['code'] }}) →</span>
                                        </div>
                                        <div style="display: flex; gap: 6px; align-items: center;">
                                            <!-- Left Dominant Buttons (C1) -->
                                            @foreach([9, 7, 5, 3] as $s)
                                                @php 
                                                    $isActive = ($matrixInput[$key]['dominant'] == $c1['id'] && $matrixInput[$key]['scale'] == $s);
                                                @endphp
                                                <button type="button" 
                                                        wire:click="setComparisonValue('{{ $key }}', {{ $c1['id'] }}, {{ $s }})"
                                                        style="width: 32px; height: 32px; font-size: 11px; font-weight: bold; border-radius: 6px; cursor: pointer; transition: all 0.2s;
                                                               border: 1px solid {{ $isActive ? '#10b981' : 'rgba(128,128,128,0.2)' }};
                                                               background: {{ $isActive ? '#10b981' : 'transparent' }};
                                                               color: {{ $isActive ? '#fff' : 'inherit' }};"
                                                        title="Kriteria {{ $c1['code'] }} lebih penting dengan skala {{ $s }}">
                                                    {{ $s }}
                                                </button>
                                            @endforeach

                                            <!-- Middle Button (Equal) -->
                                            @php 
                                                $isEqualActive = ($matrixInput[$key]['scale'] == 1);
                                            @endphp
                                            <button type="button" 
                                                    wire:click="setComparisonValue('{{ $key }}', {{ $c1['id'] }}, 1)"
                                                    style="width: 32px; height: 32px; font-size: 11px; font-weight: bold; border-radius: 6px; cursor: pointer; transition: all 0.2s;
                                                           border: 1px solid {{ $isEqualActive ? '#6b7280' : 'rgba(128,128,128,0.2)' }};
                                                           background: {{ $isEqualActive ? '#6b7280' : 'transparent' }};
                                                           color: {{ $isEqualActive ? '#fff' : 'inherit' }};"
                                                    title="Sama penting">
                                                1
                                            </button>

                                            <!-- Right Dominant Buttons (C2) -->
                                            @foreach([3, 5, 7, 9] as $s)
                                                @php 
                                                    $isActive = ($matrixInput[$key]['dominant'] == $c2['id'] && $matrixInput[$key]['scale'] == $s);
                                                @endphp
                                                <button type="button" 
                                                        wire:click="setComparisonValue('{{ $key }}', {{ $c2['id'] }}, {{ $s }})"
                                                        style="width: 32px; height: 32px; font-size: 11px; font-weight: bold; border-radius: 6px; cursor: pointer; transition: all 0.2s;
                                                               border: 1px solid {{ $isActive ? '#06b6d4' : 'rgba(128,128,128,0.2)' }};
                                                               background: {{ $isActive ? '#06b6d4' : 'transparent' }};
                                                               color: {{ $isActive ? '#fff' : 'inherit' }};"
                                                        title="Kriteria {{ $c2['code'] }} lebih penting dengan skala {{ $s }}">
                                                    {{ $s }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>

                <x-slot name="footer">
                    <div style="display: flex; justify-content: flex-end;">
                        <x-filament::button type="submit" size="lg">
                            Hitung Konsistensi & Bobot
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::section>
        </form>

        <!-- Calculation Results -->
        @if($isCalculated && $results)
            <div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <!-- Consistency Check Card -->
                    <x-filament::section>
                        <x-slot name="heading">
                            <span style="font-size: 16px; font-weight: 700;">Uji Konsistensi (AHP)</span>
                        </x-slot>

                        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px; margin-top: 8px;">
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(128,128,128,0.1); padding-bottom: 8px;">
                                <span style="color: gray;">Total Kriteria (n)</span>
                                <strong>6</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(128,128,128,0.1); padding-bottom: 8px;">
                                <span style="color: gray;">λ maks (Lambda Max)</span>
                                <strong style="font-family: monospace;">{{ number_format($results['lambda_max'], 4) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(128,128,128,0.1); padding-bottom: 8px;">
                                <span style="color: gray;">CI (Consistency Index)</span>
                                <strong style="font-family: monospace;">{{ number_format($results['ci'], 4) }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(128,128,128,0.1); padding-bottom: 8px;">
                                <span style="color: gray;">RI (Random Index)</span>
                                <strong style="font-family: monospace;">1.24</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 4px;">
                                <span style="font-weight: 700;">CR (Consistency Ratio)</span>
                                <strong style="font-family: monospace; font-size: 15px; color: {{ $results['is_consistent'] ? '#10b981' : '#ef4444' }};">
                                    {{ number_format($results['cr'], 4) }}
                                </strong>
                            </div>

                            <!-- Consistent status badge -->
                            <div style="margin-top: 16px; padding: 16px; border-radius: 12px; border: 1px solid {{ $results['is_consistent'] ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }}; background: {{ $results['is_consistent'] ? 'rgba(16,185,129,0.05)' : 'rgba(239,68,68,0.05)' }}; text-align: center;">
                                <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: gray; display: block; margin-bottom: 4px;">Status Matriks</span>
                                <strong style="font-size: 14px; color: {{ $results['is_consistent'] ? '#10b981' : '#ef4444' }};">
                                    {{ $results['is_consistent'] ? 'KONSISTEN (CR < 0.1)' : 'TIDAK KONSISTEN (CR >= 0.1)' }}
                                </strong>
                                @if(!$results['is_consistent'])
                                    <p style="font-size: 11px; color: gray; margin-top: 8px; line-height: 1.4;">
                                        Matriks perbandingan tidak memenuhi standar konsistensi Saaty. Harap sesuaikan kembali nilai kepentingan di atas agar CR di bawah 0.10.
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($results['is_consistent'])
                            <div style="margin-top: 20px; border-top: 1px solid rgba(128,128,128,0.1); padding-top: 16px;">
                                <x-filament::button wire:click="saveWeights" color="success" icon="heroicon-o-arrow-down-tray" style="width: 100%;">
                                    Simpan Bobot ke Database
                                </x-filament::button>
                            </div>
                        @endif
                    </x-filament::section>

                    <!-- Priority Weights Card -->
                    <x-filament::section>
                        <x-slot name="heading">
                            <span style="font-size: 16px; font-weight: 700;">Hasil Pembobotan Kriteria (Priority Vector)</span>
                        </x-slot>

                        <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 12px;">
                            @foreach($results['weighted_criteria'] as $id => $wc)
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                                        <span style="font-weight: 600;">{{ $wc['code'] }} - {{ $wc['name'] }}</span>
                                        <strong style="color: var(--primary-600); font-family: monospace;">
                                            {{ number_format($wc['weight'] * 100, 2) }}% ({{ number_format($wc['weight'], 4) }})
                                        </strong>
                                    </div>
                                    <div style="width: 100%; height: 8px; background: rgba(128,128,128,0.15); border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ $wc['weight'] * 100 }}%; background: var(--primary-600); border-radius: 4px;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>
                </div>

                <!-- Matrix A Table Card -->
                <x-filament::section>
                    <x-slot name="heading">
                        <span style="font-size: 16px; font-weight: 700;">Matriks Perbandingan Berpasangan (Matrix A)</span>
                    </x-slot>

                    <div style="overflow-x: auto; margin-top: 12px;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 500px; text-align: center; border: 1px solid rgba(128,128,128,0.2);">
                            <thead>
                                <tr style="background: rgba(128,128,128,0.05); border-bottom: 2px solid rgba(128,128,128,0.2);">
                                    <th style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left;">Kriteria</th>
                                    @foreach($criteria as $c)
                                        <th style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2);">{{ $c['code'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $c1)
                                    <tr style="border-bottom: 1px solid rgba(128,128,128,0.15);">
                                        <td style="padding: 10px; font-size: 12px; font-weight: 700; border: 1px solid rgba(128,128,128,0.2); text-align: left; background: rgba(128,128,128,0.02);">{{ $c1['code'] }}</td>
                                        @foreach($criteria as $c2)
                                            @php $val = $results['matrix'][$c1['id']][$c2['id']]; @endphp
                                            <td style="padding: 10px; font-size: 12px; font-family: monospace; border: 1px solid rgba(128,128,128,0.2); color: gray;">
                                                @if($c1['id'] == $c2['id'])
                                                    <span style="font-weight: 700; color: inherit;">1</span>
                                                @else
                                                    {{ $val >= 1.0 ? round($val, 2) : number_format($val, 3) }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>

            </div>
        @endif

    </div>
</x-filament-panels::page>
