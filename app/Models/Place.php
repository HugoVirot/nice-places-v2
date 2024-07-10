<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    // chargement automatique des catégories / images / user associés lorsque l'on récupère le lieu
    // les avis ne sont pas inclus car tri par date de publication (le dernier en 1er) nécessaire 
    // => récupération et tri réalisés dans le contrôleur
    protected $with = ['categorie', 'user', 'image_mise_en_avant', 'departement'];

    public function category()
    {
        return $this->belongsTo(category::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'favoris');
    }

    // dans le cadre de la mise en favoris
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 'validé');
    }

    public function images()
    {
        return $this->hasMany(Image::class)->where('status', 'validée');
    }

    public function cover_image()
    {
        return $this->hasMany(Image::class)->where('cover', true);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'description', 'latitude', 'longitude', 'note', 'time', 'difficulty', 'category_id',
        'kilometres', 'department_id', 'adres', 'postcode', 'city', 'user_id', 'status', 'comment'
    ];
}
