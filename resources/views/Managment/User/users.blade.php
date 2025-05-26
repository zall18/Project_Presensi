@extends('Template.template')

@section('container')
<div class="container mt-5">
    <h3 class="mb-4 fw-semibold">Users</h3>

    <div class="table-responsive rounded-3 shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-dark border-bottom">
                <tr>
                    <th class="fw-normal">ID</th>
                    <th class="fw-normal">Username</th>
                    <th class="fw-normal">Email</th>
                    <th class="fw-normal">Level</th>
                    <th class="fw-normal text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->id }}</td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td class="fw-semibold">{{ $user->email }}</td>
                    <td class="fw-semibold text-capitalize">{{ $user->level }}</td>
                    <td class="text-center">
                        <a href="{{ route('user.show', $user->id) }}"
                           class="btn btn-sm btn-outline-info rounded-pill me-1"
                           title="View User">
                            <i class="ti ti-eye"></i>
                        </a>
                        <a href="{{ route('user.edit', $user->id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill me-1"
                           title="Edit User">
                            <i class="ti ti-pencil"></i>
                        </a>
                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger rounded-pill"
                                    onclick="return confirm('Are you sure?')"
                                    title="Delete User">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Floating Create User Button -->
    <a href="{{ route('user.create') }}"
       class="btn btn-success rounded-circle shadow d-flex align-items-center justify-content-center"
       style="position: fixed; bottom: 30px; right: 30px; width: 56px; height: 56px; font-size: 1.5rem; z-index: 1050;"
       title="Create User"
    >
        <i class="bi bi-plus-lg"></i>
    </a>
</div>
@endsection
