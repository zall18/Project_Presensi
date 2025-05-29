@extends('Template.template')

@section('container')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-semibold mb-1">Detail User</h3>
            <p class="text-muted mb-0">Informasi lengkap user</p>
        </div>
        <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar User
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama User</label>
                        <div class="form-control bg-light">{{ $user->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <div class="form-control bg-light">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <div>
                            @if($user->level == 'admin')
                                <span class="badge bg-primary px-3 py-2">Admin</span>
                            @elseif($user->level == 'operator')
                                <span class="badge bg-info px-3 py-2 text-dark">Operator</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">{{ ucfirst($user->level) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dibuat Pada</label>
                        <div class="form-control bg-light">{{ $user->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i> Edit
                </a>
                <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
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
                                        <a href="{{ route('participant.show', $presensi->participant->id) }}" 
                                        class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                            <i class="ti ti-search"></i> Detail Participant
                                        </a>
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
        </div>
    </div>
</div>
@endsection