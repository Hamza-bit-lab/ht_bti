<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public function interviews()
    {
        return $this->hasMany(Interview::class, 'interviewer');
    }
    public function getJoiningDateAttribute($value)
    {
        return Carbon::parse($value)->format('d M, Y');
    }
}
