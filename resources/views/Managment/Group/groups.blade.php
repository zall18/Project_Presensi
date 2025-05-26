@extends('Template.template')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4 fw-semibold">Groups</h3>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="bg-light text-dark border-bottom">
                <tr>
                    <th class="fw-normal">ID</th>
                    <th class="fw-normal">Nama</th>
                    <th class="fw-normal">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $group)
                <tr>
                    <td class="fw-semibold">{{ $group->id }}</td>
                    <td class="fw-semibold">{{ $group->nama }}</td>
                    <td>
                        <a href="{{ route('group.edit', $group->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('group.destroy', $group->id) }}" method="POST" class="d-inline">
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
    <!-- Floating Create Group Button -->
    <a href="{{ route('group.create') }}"
       class="btn btn-success rounded-circle shadow d-flex align-items-center justify-content-center"
       style="position: fixed; bottom: 30px; right: 30px; width: 56px; height: 56px; font-size: 2rem; z-index: 1050;"
       title="Create Group"
    >
        <i class="ti ti-plus"></i>
    </a>
</div>
@endsection
