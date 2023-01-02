<?php

namespace App\Imports;

use App\Models\Blastlist;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BlastlistImport implements ToModel,WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Blastlist([
            'email'     => $row['email'],
            'fname'     => $row['fname'],
            'lname'     => $row['lname'],
        ]);
    }
}
