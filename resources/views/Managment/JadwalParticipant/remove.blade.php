@extends('Template.template')

@section('container')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-semibold mb-1">Remove Participant from Jadwal Participant</h3>
            <p class="text-muted mb-0">Pilih Participant yang akan hapus dari jadwal.</p>
        </div>
        <a href="{{ route('jadwalParticipant.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Jadwal Participant
        </a>
    </div>
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-transparent border-0 pt-3 pb-2">
            <div class="row g-3">
                <div class="col-md-6">
                    <form action="{{ route('jadwalParticipant.create', $shift->id) }}" method="GET">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-transparent">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0"
                                placeholder="Search participants..." value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('jadwalParticipant.create', [
                                'id_shift' => $shift->id,
                                'filter_grup' => request('filter_grup'),
                                'sort' => request('sort'),
                                'direction' => request('direction'),
                                'search' => null,
                            ]) }}"
                            class="input-group-text bg-transparent text-danger" title="Clear search">
                                <i class="ti ti-x"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- <div class="col-md-3 text-md-end">
                    <div class="text-muted small">
                        @if($participants->total() > 0)
                        Showing {{ $participants->firstItem() }}-{{ $participants->lastItem() }} of {{ $participants->total() }}
                        @else
                        No records found
                        @endif
                    </div>
                </div> --}}
            </div>
        </div>

        <form action="{{ route('jadwalParticipant.destroyItem', $shift->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="id_shift" value="{{ $shift->id }}">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-nowrap">
                            <tr>
                                <th class="ps-4">
                                    <a href="{{ route('participant.index', [
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
                                    <a href="{{ route('participant.index', [
                                        'sort' => 'nama',
                                        'direction' => request('sort') == 'nama' && request('direction') == 'asc' ? 'desc' : 'asc',
                                        'search' => request('search'),
                                    ]) }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                        <span>Nama</span>
                                        @if(request('sort') == 'nama')
                                        <i class="ti ti-arrows-sort fs-4 text-primary"></i>
                                        <i class="ti ti-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }} fs-4 text-primary"></i>
                                        @else
                                        <i class="ti ti-arrows-sort fs-4 text-muted opacity-50"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>No Induk</th>
                                <th>ID Kartu</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th>Status Jadwal</th>
                                <th class="text-end pe-4">
                                    <input type="checkbox" name="all_checked" id="all_checked" class="form-check-input"
                                        onclick="toggleAllCheckboxes(this)">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse ($participants as $key => $participant)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $participant->nama }}</span>
                                            <small class="text-muted">ID: {{ $participant->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $participant->no_induk }}</td>
                                <td>{{ $participant->id_kartu }}</td>
                                <td>{{ $participant->no_hp }}</td>
                                <td>{{ $participant->alamat }}</td>
                                <td>
                                    @if($participant->jadwalParticipant)
                                    <span class="badge bg-success">Jadwal {{ $participant->jadwalParticipant->shift->nama }}</span>
                                    @else
                                    <span class="badge bg-secondary">Belum Dijadwalkan</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <input type="checkbox" value="{{ $participant->id }}"
                                        class="form-check-input"
                                        @if(in_array($participant->id, $jadwalParticipantIds ?? []))
                                            checked disabled
                                            {{-- Don't include name so it won't be submitted --}}
                                        @else
                                            name="participants[]"
                                        @endif
                                        value="{{ $participant->id }}">
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <i class="ti ti-users-off fs-5 text-muted mb-2"></i>
                                        <span class="text-muted">No participants found</span>
                                        @if(request('search'))
                                        <a href="{{ route('participant.index') }}" class="btn btn-sm btn-outline-primary mt-3">
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
            <div class="mt-4 pt-2 border-top">
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="ti ti-users-plus me-1"></i> Remove Group Participants
                    </button>
                    <a href="{{ route('jadwalParticipant.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="ti ti-reload me-1"></i> Cancel
                    </a>
                </div>
        </form>
        
    </div>
</div>

<script>
    function toggleAllCheckboxes(source) {
        if (!source.checked) {
            document.getElementById('all_checked').checked = false;
            
        }
        const checkboxes = document.querySelectorAll('input[name="participants[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }
</script>

@endsection