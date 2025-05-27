@extends('Template.template')

@section('container')

<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-semibold mb-1">Presensi Management</h3>
            <p class="text-muted mb-0">Manage all presensi records in the system</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-transparent border-0 pt-3 pb-2">
            <div class="row g-3">
                <div class="col-md-6">
                    <form action="{{ route('presensi.index') }}" method="GET">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-transparent">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                   placeholder="Search presensi..." value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('presensi.index', request()->except('search')) }}"
                               class="input-group-text bg-transparent text-danger" title="Clear search">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="text-muted small">
                        {{-- @if($presensis->total() > 0)
                        Showing {{ $presensis->firstItem() }}-{{ $presensis->lastItem() }} of {{ $presensis->total() }}
                        @else --}}
                        No records found
                        {{-- @endif --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-4">
                                <a href="{{ route('presensi.index', [
                                    'sort' => 'id',
                                    'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    'search' => request('search'),
                                ]) }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                    <span>ID</span>
                                    @if(request('sort') == 'id')
                                    <i class="ti ti-arrows-sort fs-4 text-primary"></i>
                                    <i class="ti ti-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} fs-4 text-primary"></i>
                                    @else
                                    <i class="ti ti-arrows-sort fs-4 text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('presensi.index', [
                                    'sort' => 'participant_nama',
                                    'direction' => request('sort') == 'participant_nama' && request('direction') == 'asc' ? 'desc' : 'asc',
                                    'search' => request('search'),
                                ]) }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                    <span>Participant</span>
                                    @if(request('sort') == 'participant_nama')
                                    <i class="ti ti-arrows-sort fs-4 text-primary"></i>
                                    <i class="ti ti-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} fs-4 text-primary"></i>
                                    @else
                                    <i class="ti ti-arrows-sort fs-4 text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Device</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($presensis as $presensi)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $presensi->id }}</td>
                            <td>{{ $presensi->participant->nama ?? "-"}}</td>
                            <td>{{ $presensi->waktu_masuk }}</td>
                            <td>{{ $presensi->waktu_keluar }}</td>
                            <td>{{ $presensi->device->nama }}</td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('presensi.show', $presensi->id) }}" class="btn btn-sm btn-icon btn-outline-info rounded-3" data-bs-toggle="tooltip" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('presensi.edit', $presensi->id) }}" class="btn btn-sm btn-icon btn-outline-primary rounded-3" data-bs-toggle="tooltip" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form action="{{ route('presensi.destroy', $presensi->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger rounded-3"
                                                data-bs-toggle="tooltip" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this presensi?')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="ti ti-users-off fs-5 text-muted mb-2"></i>
                                    <span class="text-muted">No presensi found</span>
                                    @if(request('search'))
                                    <a href="{{ route('presensi.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                                        Clear filters
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Pagination --}}
        {{-- <div class="d-flex justify-content-end mt-3">
            {{ $presensis->links() }}
        </div> --}}
    </div>
</div>
@endsection