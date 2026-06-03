<x-layout>
    <h4>Locker Active</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Locker</th>
                    <th>Start</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lockers as $locker)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td onclick="forceUnlock('{{ $locker->lkr_locker_id }}')">
                        {{ $locker->employee->nama_karyawan }}
                    </td>
                    <td>{{ $locker->locker_name }}</td>
                    <td>{{ $locker->booked_at }}</th>
                    <td>{{ $locker->duration }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        function forceUnlock(id) {
            // const url = "{{ url('locker.force') }}";
            const baseUrl = "{{ route('locker.force', ['id' => '__ID__']) }}";
            const url = baseUrl.replace('__ID__', id);
            console.log(url);
            Swal.fire({
                title: "Force Unlock?",
                text: "You are about to force unlock this locker.",
                icon: "warning",
                confirmButtonText: "Yes, unlock it!",
                showCancelButton: true,
                cancelButtonColor: "#d33",
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // POST ke API
                    fetch(`${url}/${id}`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}", // penting untuk Laravel
                            },
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
    </script>
</x-layout>