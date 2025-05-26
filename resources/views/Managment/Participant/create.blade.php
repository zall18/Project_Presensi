@extends('Template.template')

@section('container')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container mt-4">
    <h3 class="mb-4">Create Participant</h3>
    <form action="{{ route('participant.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="no_induk" class="form-label">No Induk</label>
            <input type="text" class="form-control" id="no_induk" name="no_induk" value="{{ old('no_induk') }}" required>
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}" required>
        </div>
        <div class="mb-3">
            <label for="id_kartu" class="form-label">ID Kartu</label>
            <input type="text" class="form-control" id="id_kartu" name="id_kartu" value="{{ old('id_kartu') }}" required>
        </div>
        <div class="mb-3">
            <label for="no_hp" class="form-label">No HP</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="2" required>{{ old('alamat') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('participant.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
