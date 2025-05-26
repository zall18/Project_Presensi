@extends('Template.template')

@section('container')
    <div class="container mt-4">
        <h3 class="mb-4">Create User</h3>
        <form action="{{ route('user.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Username</label>
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    required>
            </div>
            <div class="mb-3">
                <label for="level" class="form-label">Level</label>
                <select class="form-select" id="level" name="level" required>
                    <option value="admin" {{ old('level') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="operator" {{ old('level') == 'operator' ? 'selected' : '' }}>Operator</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
