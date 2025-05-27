@extends('Template.template')

@section('container')
<div class="container mt-4">
    <h3 class="mb-4">Create Detail Shift</h3>
    <form action="{{ route('detail-shifts.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="hari" class="form-label">Hari</label>
             <input type="hidden" name="shift_id" value="{{ $shift->id }}">
            <select class="form-select" id="hari" name="hari" required>
                <option value="" disabled selected>Pilih Hari</option>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                    @if ($shift->detailShifts->contains('hari', $day))
                        <option value="{{ $day }}" disabled>{{ $day }} (Already exists)</option>
                    @else
                        <option value="{{ $day }}" {{ old('hari') == $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endif
                @endforeach
            </select>
            @error('hari')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('shift.show', $shift->id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
