<?php

namespace App\Imports;

use App\Models\Participant;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;

class ParticipantImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new Participant([
            'no_induk' => $row['no_induk'],
            'nama' => $row['nama'],
            'id_kartu' => $row['id_kartu'],
            'no_hp' => $row['no_hp'],
            'alamat' => $row['alamat'],
        ]);
    }

    public function rules(): array
    {
        return [
            '*.no_induk' => ['required', 'unique:participants,no_induk'],
            '*.nama' => ['required'],
            '*.id_kartu' => ['required', 'unique:participants,id_kartu'],
            '*.no_hp' => ['nullable'],
            '*.alamat' => ['nullable'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'no_induk.required' => 'No Induk wajib diisi.',
            'no_induk.unique' => 'No Induk sudah terdaftar.',
            'id_kartu.required' => 'ID Kartu wajib diisi.',
            'id_kartu.unique' => 'ID Kartu sudah terdaftar.',
        ];
    }
}
