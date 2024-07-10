<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $with = ['region'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function place()
    {
        return $this->hasMany(Place::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
