@extends('Template.template')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4 fw-semibold">Participants</h3>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="bg-light text-dark border-bottom">
                <tr>
                    <th class="fw-normal">ID</th>
                    <th class="fw-normal">No Induk</th>
                    <th class="fw-normal">Nama</th>
                    <th class="fw-normal">ID Kartu</th>
                    <th class="fw-normal">No HP</th>
                    <th class="fw-normal">Alamat</th>
                    <th class="fw-normal">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($participants as $participant)
                <tr>
                    <td class="fw-semibold">{{ $participant->id }}</td>
                    <td class="fw-semibold">{{ $participant->no_induk }}</td>
                    <td class="fw-semibold">{{ $participant->nama }}</td>
                    <td class="fw-semibold">{{ $participant->id_kartu }}</td>
                    <td class="fw-semibold">{{ $participant->no_hp }}</td>
                    <td class="fw-semibold">{{ $participant->alamat }}</td>
                    <td>
                        <a href="{{ route('participant.edit', $participant->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('participant.destroy', $participant->id) }}" method="POST" class="d-inline">
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
