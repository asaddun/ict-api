<x-layout>

    <h4>Locker Access</h4>

    <div class="col-12 col-md-6 d-flex flex-row gap-2 mb-3">
        <form action="{{ route('locker.access') }}" class="d-flex flex-row gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Employee...">
            <button type="submit" class="btn btn-success btn-sm">Search</button>
        </form>
        <form action="{{ route('locker.access.sync') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Update</button>
        </form>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accesses as $access)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $access->employee->nama_karyawan }}</td>
                    <td>
                        <form action="{{ route('locker.access.update', $access->c_employee_id) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            <select name="lkr_location_id" class="form-select form-select-sm">
                                <option value="0" {{ $access->lkr_location_id == 0 ? 'selected' : '' }}>No Access</option>
                                @foreach($locations as $location)
                                <option value="{{ $location->lkr_location_id }}" {{ $access->lkr_location_id == $location->lkr_location_id ? 'selected' : '' }}>
                                    {{ $location->location }}
                                </option>
                                @endforeach
                            </select>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        No data.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $accesses->links() }}
    </div>
</x-layout>