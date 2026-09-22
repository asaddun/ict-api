<x-layout>
    <style>
        .status-green {
            background-color: #28a745 !important;
            color: #fff;
        }

        .status-red {
            background-color: #dc3545 !important;
            color: #fff;
        }
    </style>
    <div class="container">
        <h4>
            Monitoring Machine
            (<span id="active">0</span>/{{ $total }})
        </h4>
        @foreach ($lines as $line => $machines)
            <div class="card">
                <div class="card-header">
                    Line {{ $line }}
                    <span>(<span id="active-{{ $line }}">0</span>/{{ count($machines) }})</span>
                </div>
                <div class="card-body row row-cols-3 row-cols-md-6 flex-row flex-wrap">
                    @foreach ($machines as $machine)
                        <div class="col px-1">
                            <div class="card text-center">
                                <div class="card-header py-1">
                                    <div
                                        onclick="window.open('http://192.168.3.245:1880/sensor?id={{ $machine['A_ASSET_ID'] }}','mywindow');">
                                        {{ $machine['LINENO'] . ' [' . $machine['VALUE'] . ']' }}
                                    </div>
                                    <div id="version-{{ $machine['A_ASSET_ID'] }}" class="text-muted p-0">-</div>
                                    <div id="ssid-{{ $machine['A_ASSET_ID'] }}" class="text-muted p-0">-</div>
                                </div>
                                <div class="card-body p-0 d-flex flex-row">
                                    <div class="col" id="status-{{ $machine['A_ASSET_ID'] }}"
                                        data-line="{{ $line }}">OFFLINE</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach


        <div class="card">
            <div class="card-header">
                Tes
            </div>
            <div class="card-body row row-cols-3 row-cols-md-6 flex-row flex-wrap">
                <div class="col px-1">
                    <div class="card text-center">
                        <div class="card-header py-1">
                            1000
                            <div id="version-1000" class="text-muted p-0">-</div>
                            <div id="ssid-1000" class="text-muted p-0">-</div>
                        </div>
                        <div class="card-body p-0 d-flex flex-row">
                            <div class="col" id="status-1000">
                                OFFLINE</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll("[id^='status-']").forEach(el => {
                    el.classList.add("status-red");
                });
            });

            function updateActiveCount() {
                const lines = {};
                let total = 0;

                document.querySelectorAll("[data-line]").forEach(el => {
                    const line = el.getAttribute("data-line");
                    const isActive = el.classList.contains("status-green");
                    if (isActive) total++;
                    if (!lines[line]) lines[line] = {
                        active: 0
                    };
                    if (isActive) lines[line].active++;
                });

                Object.keys(lines).forEach(line => {
                    document.getElementById("active-" + line).innerText = lines[line].active;
                });

                document.getElementById("active").innerHTML = total;
            }

            let activesMachine = {};
            let statusTimers = {};
            const client = mqtt.connect('wss://api-mqtt.adyawinsa.com/', {
                username: 'esp8266',
                password: 'esp8266-mqtt',
            });

            client.on('connect', () => {
                console.log("Connected!");
                client.subscribe("sensor/injection/+/request");
            });

            client.on("message", (topic, message) => {
                const data = JSON.parse(message.toString());
                const id = data.id;
                if (id === "1000") {
                    console.log(data);
                }
                const action = data.action;
                // const inject = data.inj;
                const status = document.getElementById("status-" + id);
                if (!status) return;

                // Saat pesan diterima → status hijau
                if (action == "status") {
                    const value = data.value;
                    if (value === "idle" || value === "running") {
                        status.classList.remove("status-red");
                        status.classList.add("status-green");
                    } else {
                        status.classList.remove("status-green");
                        status.classList.add("status-red");
                    }

                    status.innerHTML = value.toUpperCase();
                }
                const versionEl = document.getElementById("version-" + id);
                if (versionEl && data.version) versionEl.innerHTML = data.version;

                const ssidEl = document.getElementById("ssid-" + id);
                if (ssidEl && data.ssid) ssidEl.innerHTML = data.ssid;

                // Reset timer jika sebelumnya ada
                if (statusTimers[id]) clearTimeout(statusTimers[id]);

                // Set timer inactive
                statusTimers[id] = setTimeout(() => {
                    status.classList.remove("status-green");
                    status.classList.add("status-red");
                    status.innerHTML = "OFFLINE";
                    updateActiveCount();
                }, 60 * 1000);

                updateActiveCount();
            });
        </script>
    </div>
</x-layout>
