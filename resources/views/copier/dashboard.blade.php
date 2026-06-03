<x-layout>
    <div class="container">
        <div class="col-12 col-md-4 mb-3">
            <form action="{{ route('copier.dashboard') }}" method="GET" class="d-flex flex-row gap-2">
                @php
                    $range = request('range', 'month');
                    $selectedValue = request('value');
                @endphp

                <select name="range" id="range" class="form-select"
                    onchange="document.querySelector('[name=value]')?.remove(); this.form.requestSubmit();">>
                    <option value="day" {{ $range == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="week" {{ $range == 'week' ? 'selected' : '' }}>Week</option>
                    <option value="month" {{ $range == 'month' ? 'selected' : '' }}>Month</option>
                </select>

                @if ($range === 'day')
                    <input type="date" name="value" class="form-control"
                        value="{{ $selectedValue ?? now()->subDay()->toDateString() }}"
                        onchange="this.form.requestSubmit()">
                @elseif ($range === 'week')
                    <input type="week" name="value" class="form-control"
                        value="{{ $selectedValue ?? now()->format('o-\WW') }}" onchange="this.form.requestSubmit()">
                @elseif ($range === 'month')
                    <input type="month" name="value" class="form-control"
                        value="{{ $selectedValue ?? now()->format('Y-m') }}" onchange="this.form.requestSubmit()">
                @endif
            </form>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Top 10 User Copier
                </div>
                <div class="card-body">
                    <canvas id="topUserChart" height="75"></canvas>
                </div>
            </div>
        </div>
        <div class="row flex-row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        Paper Usage
                    </div>
                    <div class="card-body d-flex flex-row flex-wrap">
                        <div class="col px-1">
                            <div class="info-box bg-black">
                                <span class="info-box-icon bg-light"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Black & White</span>
                                    <span class="info-box-number">{{ $paper['bw'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col px-1">
                            <div class="info-box bg-success">
                                <span class="info-box-icon bg-light"><i class="fas fa-file-image"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Colors</span>
                                    <span class="info-box-number">{{ $paper['color'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col px-1">
                            <div class="info-box bg-info">
                                <span class="info-box-icon bg-light"><i class="fas fa-copy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total</span>
                                    <span class="info-box-number">{{ $paper['total'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        Cost Printing
                    </div>
                    <div class="card-body d-flex flex-column flex-wrap">
                        <div class="col px-1">
                            <div class="info-box bg-white">
                                <span class="info-box-icon bg-info"><i class="fas fa-print"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Cost Rent</span>
                                    <span class="info-box-number">Rp 2.000.000,00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col px-1">
                            <div class="info-box bg-white">
                                <span class="info-box-icon bg-info"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Cost Paper</span>
                                    <span class="info-box-number">
                                        {{ 'Rp ' . number_format($cost['paper'], 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const topUsers = @json($topten);

            const ctx = document.getElementById('topUserChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: topUsers.map(u => u.name),
                    datasets: [{
                        label: 'Total Halaman',
                        data: topUsers.map(u => u.total),
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
</x-layout>
