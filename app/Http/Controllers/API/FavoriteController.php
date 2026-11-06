<?php

namespace App\Http\Controllers\API;

use App\Models\Place;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FavoriteController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(User $user)
    {
        if (request()->user()->isNot($user)) {
            throw ValidationException::withMessages([
                'user' => ['Vous ne pouvez consulter que vos propres favoris.'],
            ]);
        }

        $user->load('favorites');

        return $this->sendResponse($user->favorites, 'Favoris récupérés avec succès');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'place_id' => ['required', 'integer', 'exists:places,id'],
        ]);

        $request->user()->favorites()->syncWithoutDetaching([$validated['place_id']]);

        return $this->sendResponse($validated['place_id'], 'Lieu ajouté aux favoris', 201);
    }

    public function destroy(Request $request, User $user, Place $lieu)
    {
        if ($request->user()->isNot($user)) {
            throw ValidationException::withMessages([
                'user' => ['Vous ne pouvez modifier que vos propres favoris.'],
            ]);
        }

        $user->favorites()->detach($lieu->id);

        return $this->sendResponse($lieu->id, 'Lieu retiré des favoris');
    }
}
