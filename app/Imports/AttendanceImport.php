<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $attendance = [
                'department' => $row['Department'],
                'name' => $row['Name'],
                'number' => $row['No.'],
                'date_time' => $row['Date/Time'],
                'status' => $row['Status'],
                'location_id' => $row['Location ID'],
                'id_number' => $row['ID Number'],
                'verify_code' => $row['VerifyCode'],
                'card_no' => $row['CardNo'],
            ];
        }
    }
}
