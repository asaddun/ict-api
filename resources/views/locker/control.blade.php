<x-layout>

    <h4>Locker Control</h4>

    <form action="{{ route('locker.control') }}" class="col-12 col-md-6 d-flex flex-row gap-2 mb-3">
        <input type="text" class="form-control form-control-sm" name="locker" placeholder="Locker...">
        <select name="location" class="form-select form-select-sm">
            <option value="0">
                ---
            </option>
            @foreach($locations as $location)
            <option value="{{ $location->lkr_location_id }}">
                {{ $location->location }}
            </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-success btn-sm">Search</button>
    </form>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Locker</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>IP</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lockers as $locker)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $locker->locker_name }}</td>
                    <td>{{ $locker->isavailable == 'Y' ? 'Free' : 'Booked'}}</td>
                    <td>{{ $locker->location->location }}</td>
                    <td>{{ $locker->ip_address }}</td>
                    <td>
                        <div class="d-flex flex-row gap-1">
                            <button
                                type="button"
                                class="btn btn-primary btn-sm view-data"
                                data-bs-toggle="modal"
                                data-bs-target="#lockerModal"
                                data-id="{{ $locker->lkr_locker_id }}"
                                data-title="{{ $locker->locker_name }}"
                                data-isactive="{{ $locker->isactive }}"
                                data-location="{{ $locker->lkr_location_id }}"
                                data-ip="{{ $locker->ip_address }}"
                                {{ $locker->isavailable == 'Y' ? '' : 'disabled'}}>
                                Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="openLocker('{{ $locker->ip_address }}', '{{ $locker->io}}')">Open</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No data.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $lockers->links() }}
    </div>

    <!-- Modal -->
    <div class="modal fade" id="lockerModal" tabindex="-1" aria-labelledby="lockerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="lockerModalLabel">Locker</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="form-locker">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal-isactive" class="form-label">Active</label>
                            <select name="isactive" class="form-select" id="modal-isactive">
                                <option value="Y">Active</option>
                                <option value="N">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal-location" class="form-label">Location</label>
                            <select name="lkr_location_id" class="form-select" id="modal-location">
                                <option value="0">
                                    No Location
                                </option>
                                @foreach($locations as $location)
                                <option value="{{ $location->lkr_location_id }}">
                                    {{ $location->location }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ip" class="form-label">IP</label>
                            <span class="form-control" id="modal-ip"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openLocker(ip, io) {

            Swal.fire({
                title: "Force Open?",
                text: "You are about to force open this locker.",
                icon: "warning",
                confirmButtonText: "Yes, open it!",
                showCancelButton: true,
                cancelButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Swal.fire({
                    //     title: "Success!",
                    //     text: "The locker has been unlocked.",
                    //     icon: "success"
                    // });


                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // POST ke API
                    fetch(`{{ route('locker.open') }}`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}", // penting untuk Laravel
                            },
                            body: JSON.stringify({
                                ip: ip,
                                io: io,
                            }),
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.status === 'success') {
                                Swal.fire('Success', data.message, 'success');
                            } else {
                                Swal.fire('Failed', data.message, 'error');
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            Swal.fire('Error', 'Something wrong happened.', 'error');
                            console.error(err);
                        });
                }
            });
        }

        // Tangkap event saat tombol dengan kelas 'view-data' diklik
        const viewButtons = document.querySelectorAll('.view-data');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                // 1. Ambil data dari atribut tombol
                const id = this.dataset.id;
                const baseUrl = "{{ route('locker.control.update', ['id' => '__ID__']) }}";
                const url = baseUrl.replace('__ID__', id);
                console.log(url);

                const title = this.dataset.title;
                const isactive = this.dataset.isactive;
                const location = this.dataset.location;
                const ip = this.dataset.ip;

                // 2. Masukkan data ke dalam elemen modal
                // document.getElementById('form-locker').action = `${url}/${id}`;
                document.getElementById('form-locker').action = url;
                document.getElementById('lockerModalLabel').innerHTML = `Locker ${title}`;
                document.getElementById('modal-isactive').value = isactive;
                document.getElementById('modal-location').value = location;
                document.getElementById('modal-ip').innerHTML = ip;

                // Catatan: Jika Anda menggunakan Bootstrap 5 dan jQuery:
                // const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                // detailModal.show();
                // Namun, jika menggunakan data-bs-toggle="modal", modal akan terbuka otomatis.
            })
        });
        // });
    </script>
</x-layout>