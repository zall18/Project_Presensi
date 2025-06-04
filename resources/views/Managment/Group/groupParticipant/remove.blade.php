@extends('Template.template')

@section('container')
<div class="container mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-semibold mb-1">Remove Participants to Group</h3>
            <p class="text-muted mb-0">Select participants to remove to this group</p>
        </div>
        <a href="{{ route('group.show', Crypt::encrypt($group->id)) }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Group List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('groupParticipant.destroy') }}" method="POST">
                @csrf
                @method('DELETE');
                <input type="hidden" name="group_id" value="{{ $group->id }}">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>No Induk</th>
                                <th>Nama</th>
                                <th>ID Kartu</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th class="text-end pe-4">
                                    <input type="checkbox" name="all_checked" id="all_checked" class="form-check-input"
                                        onclick="toggleAllCheckboxes(this)">
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participants as $participant)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $participant->no_induk }}</td>
                                    <td>{{ $participant->nama }}</td>
                                    <td>{{ $participant->id_kartu }}</td>
                                    <td>{{ $participant->no_hp }}</td>
                                    <td>{{ $participant->alamat }}</td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                               value="{{ $participant->id }}"
                                               name="participants[]"
                                               >
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No participants found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-2 border-top">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-users-plus me-1"></i> Remove Group Participants
                    </button>
                    <a href="{{ route('groupParticipant.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="ti ti-reload me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
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
