<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'interviewer');
    }
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y h:i A');
    }
}
