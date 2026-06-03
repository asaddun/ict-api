<x-layout>
    <h4>Locker History</h4>
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
                @foreach($logs as $log)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $log->employee->nama_karyawan }}</td>
                    <td>{{ $log->locker->locker_name }}</td>
                    <td>{{ $log->start }}</td>
                    <td>{{ $log->duration_formatted }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $logs->links() }}
    </div>
</x-layout>