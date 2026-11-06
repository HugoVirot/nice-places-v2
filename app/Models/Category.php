<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $appends = ['image_url'];

    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function getImageUrlAttribute(): string
    {
        $path = public_path("images/category{$this->id}.jpg");
        $version = is_file($path) ? hash_file('sha256', $path) : 0;

        return "/images/category{$this->id}.jpg?v={$version}";
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'icon', 'color'
    ];
}
