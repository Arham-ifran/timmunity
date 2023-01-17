<?php

namespace App\Imports;

use App\Models\License;
use Maatwebsite\Excel\Concerns\ToModel;

class BulkLicenseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new License([
            'license_key' => $row['license_key']
        ]);
    }
}
