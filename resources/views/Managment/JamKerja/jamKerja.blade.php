@extends('Template.template')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4 fw-semibold">Jam Kerja</h3>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="bg-light text-dark border-bottom">
                <tr>
                    <th class="fw-normal">ID</th>
                    <th class="fw-normal">Nama</th>
                    <th class="fw-normal">Jam Masuk</th>
                    <th class="fw-normal">Jam Pulang</th>
                    <th class="fw-normal">Toleransi Terlambat</th>
                    <th class="fw-normal">Toleransi Pulang Cepat</th>
                    <th class="fw-normal">Mulai Scan Masuk</th>
                    <th class="fw-normal">Mulai Scan Keluar</th>
                    <th class="fw-normal">Actions</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($jamKerjas as $jam)
                <tr>
                    <td class="fw-semibold">{{ $jam->id }}</td>
                    <td class="fw-semibold">{{ $jam->nama }}</td>
                    <td class="fw-semibold">{{ $jam->jam_masuk }}</td>
                    <td class="fw-semibold">{{ $jam->jam_pulang }}</td>
                    <td class="fw-semibold">{{ $jam->toleransi_terlambat }}</td>
                    <td class="fw-semibold">{{ $jam->toleransi_pulang_cepat }}</td>
                    <td class="fw-semibold">{{ $jam->jam_mulai_scan_masuk }}</td>
                    <td class="fw-semibold">{{ $jam->jam_mulai_scan_keluar }}</td>
                    <td>
                        <a href="{{ route('jamKerja.edit', $jam->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('jamKerja.destroy', $jam->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Floating Create Participant Button -->
    <a href="{{ route('participant.create') }}"
       class="btn btn-success rounded-circle shadow d-flex align-items-center justify-content-center"
       style="position: fixed; bottom: 30px; right: 30px; width: 56px; height: 56px; font-size: 2rem; z-index: 1050;"
       title="Create Participant"
    >
        <i class="ti ti-plus"></i>
    </a>
</div>
@endsection
