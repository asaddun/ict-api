<x-layout>
    <div class="row justify-content-center">
        <div class="text-center mb-3">
            <h2>Reset Andon Manual</h2>
        </div>
        <div class="col-12 col-md-5">
            <div class="mb-3">
                <label for="action" class="form-label">Action</label>
                <select class="form-select" name="action">
                    <option value="AL">Leader</option>
                    <option value="AQ">Quality</option>
                    <option value="AM">Maintenance</option>
                    <option value="AT">Tools/Mold</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="id" class="form-label">Mesin</label>
                <button class="btn btn-primary btn-sm btn-update" type="button">
                    Update
                </button>
                <select class="form-select" name="id">
                    @foreach ($data['mesin'] as $mesin)
                        <option value="{{ $mesin['A_ASSET_ID'] }}">[{{ $mesin['LINENO'] }}] {{ $mesin['VALUE'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="button" class="form-label">Button</label>
                <select class="form-select" name="button">
                    <option value="0">OFF</option>
                    <option value="1">ON</option>
                </select>
            </div>
            <button class="btn btn-success btn-submit text-white">Send</button>
            <button class="btn btn-primary" type="button" onclick="window.location.reload()">Clear</button>
        </div>
    </div>

    <script>
        function connectWebsocket() {
            const ws = new WebSocket("ws://192.168.3.245:1880/resetandon");

            ws.onopen = () => {
                console.log('Connected to WebSocket! ✅');
            };

            ws.onerror = (error) => {
                console.error('WebSocket Error:', error);
            };

            ws.onclose = () => {
                console.log('WebSocket connection closed.');
            };

            return ws;
        }
        const resetWebsocket = connectWebsocket();

        document.querySelector('.btn-submit').addEventListener('click', () => {
            const action = document.querySelector('select[name="action"]').value;
            const id = document.querySelector('select[name="id"]').value;
            const button = document.querySelector('select[name="button"]').value;

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // POST ke API
            fetch(`api/andon/reset`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: action,
                        id: id,
                        button: button,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        if (resetWebsocket.readyState === WebSocket.OPEN) {
                            console.log(JSON.stringify(data.message));
                            resetWebsocket.send(JSON.stringify(data.message));
                            Swal.fire('Berhasil', 'Websocket terkirim', 'success');
                        } else {
                            console.log('Error WebSocket');
                            Swal.fire('Gagal', 'Websocket gagal', 'error');
                        }
                    } else {
                        Swal.fire('Gagal', `${data.message}`, 'error');
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan fetch.', 'error');
                    console.error(err);
                });
        });

        document.querySelector('.btn-update').addEventListener('click', () => {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // POST ke API
            fetch(`api/andon/update`)
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.status === 'success') {
                        Swal.fire('Berhasil', data.message, 'success');
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan fetch.', 'error');
                    console.error(err);
                });
        });
    </script>
</x-layout>
