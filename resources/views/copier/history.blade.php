<x-layout>
    <div class="container">
        <h5 class="fw-bold mb-2">Counter Fotokopi</h5>
        <span class="text-muted">Update: {{ $date }}</span>
        <div class="col-12 col-md-4 my-3">
            <form action="{{ route('copier.history') }}" method="GET" class="d-flex flex-row gap-2">
                <select name="range" id="range" class="form-select"
                    onchange="document.querySelector('[name=value]')?.remove(); this.form.requestSubmit();">>
                    <option value="day" {{ request('range') == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="week" {{ request('range') == 'week' ? 'selected' : '' }}>Week</option>
                    <option value="month" {{ request('range') == 'month' ? 'selected' : '' }}>Month</option>
                    <option value="mtd" {{ request('range') == 'mtd' ? 'selected' : '' }}>Month to Date</option>
                </select>

                @php
                    $range = request('range', 'day');
                    $selectedValue = request('value');
                @endphp

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
        <div class="col-12 col-md-4 mb-3">
            <input type="text" id="search" class="form-control" placeholder="Cari nama...">
        </div>
        <button class="btn btn-info" onclick="downloadCSV()">Download</button>
        <div class="table-responsive">
            <table class="table table-bordered" id="fotocopy-table">
                <thead>
                    <tr>
                        <td>Nama</td>
                        <td>B/W</td>
                        <td>Color</td>
                        <td>Total</td>
                        @if ($range === 'mtd')
                            <td>Limit</td>
                            <td>Penggunaan</td>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script>
        const allData = @json($data);
        const range = @json($range);

        const tbody = document.querySelector('#fotocopy-table tbody');
        const searchInput = document.getElementById("search");

        function renderFotocopy(data) {
            tbody.innerHTML = '';
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="6" class="text-center">Tidak ada data ditemukan.</td>`;
                tbody.appendChild(row);
                return;
            }
            data.forEach(item => {
                const row = document.createElement('tr');
                const used = (item.limit === '-' || item.limit === '0') ?
                    '-' :
                    ((+item.total / +item.limit) * 100).toFixed(0) + '%';
                row.innerHTML = `
                    <td>${item.name}</td>
                    <td>${item.bw}</td>
                    <td>${item.color}</td>
                    <td>${item.total}</td>`;
                if (range === 'mtd') {
                    row.innerHTML += `
                        <td>${item.limit}</td>
                        <td>${used}</td>
                    `;
                }
                tbody.appendChild(row);
            });
        }

        renderFotocopy(allData);

        // Event pencarian
        searchInput.addEventListener("input", () => {
            const keyword = searchInput.value.toLowerCase();
            const filtered = allData.filter(item =>
                item.name.toLowerCase().includes(keyword)
            );
            renderFotocopy(filtered);
        });

        function downloadCSV() {
            let table = document.getElementById("fotocopy-table");
            let rows = table.querySelectorAll("tr");
            let csv = [];

            rows.forEach(row => {
                let cols = row.querySelectorAll("td, th");
                let rowData = [];
                cols.forEach(col => rowData.push(col.innerText));
                csv.push(rowData.join(","));
            });

            let csvContent = csv.join("\n");

            // Buat file
            let blob = new Blob([csvContent], {
                type: "text/csv"
            });
            let url = URL.createObjectURL(blob);

            let a = document.createElement("a");
            a.href = url;
            filename = document.querySelector('[name=value]').value || 'data';
            a.download = `${filename}.csv`;
            a.click();
        }
    </script>
</x-layout>
