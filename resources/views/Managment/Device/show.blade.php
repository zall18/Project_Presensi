@extends('Template.template')

@section('container')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-semibold mb-1">Detail Device</h3>
            <p class="text-muted mb-0">Informasi lengkap perangkat</p>
        </div>
        <a href="{{ route('device.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar Device
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Device</label>
                        <div class="form-control bg-light">{{ $device->nama }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Device ID</label>
                        <div class="form-control bg-light">{{ $device->device_id }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <div class="form-control bg-light">{{ $device->lokasi ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <div>
                            @if($device->status == 'active')
                                <span class="badge bg-success px-3 py-2">Active</span>
                            @else
                                <span class="badge bg-danger px-3 py-2">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('device.edit', $device->id) }}" class="btn btn-primary">
                    <i class="ti ti-edit me-1"></i> Edit
                </a>
                <a href="{{ route('device.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection