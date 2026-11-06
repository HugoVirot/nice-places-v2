<?php

namespace App\Http\Controllers\API;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends BaseController
{

    public function __construct()
    {
        // middleware sanctum appliqué sur tout sauf show
        $this->middleware('auth:sanctum')->except(['show']);

        //middleware admin à ajouter pour index (en supplément de sanctum)
    }

    /**
     * Display a listing of the resource for the administrator.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorizeAdmin(request());

        $reviews = Review::all();
        return $this->sendResponse($reviews, 'Avis récupérés avec succès');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Place  $lieu
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
        return $this->sendResponse($review, 'Avis récupéré avec succès');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'note' => ['required', 'integer', 'min:1', 'max:10'],
            'comment' => ['nullable', 'string', 'min:5', 'max:2000'],
            'place_id' => ['required', 'integer', 'exists:places,id'],
        ]);

        $review = DB::transaction(function () use ($request, $validated) {
            $user = $request->user();
            $place = Place::findOrFail($validated['place_id']);
            $isAdmin = $user->role()->where('name', 'admin')->exists();

            $review = Review::create([
                'note' => $validated['note'],
                'comment' => $validated['comment'] ?? null,
                'place_id' => $place->id,
                'user_id' => $user->id,
                'status' => $isAdmin ? 'validé' : 'en attente',
            ]);

            if ($isAdmin) {
                $place->note = $place->reviews()->avg('note');
                $place->save();
            }

            return $review;
        });

        $message = $review->status === 'validé'
            ? 'Votre avis a bien été enregistré.'
            : "Votre avis a bien été enregistré. Il sera contrôlé par l'administrateur.";

        return $this->sendResponse($review, $message, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $reviews
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'note' => 'sometimes|required|integer|min:1|max:10',
            'comment' => 'sometimes|nullable|string|min:5|max:2000',
            'status' => 'sometimes|required|in:validé,en attente,refusé',
        ]);
        if (empty($validated)) {
            return $this->sendError('Aucune modification fournie.', [], 422);
        }

        $review->update($validated);
        $place = $review->place;
        if ($place) {
            $place->note = $place->reviews()->avg('note') ?? 0;
            $place->save();
        }

        $message = "L'avis a bien été modifié";
        return $this->sendResponse($review->fresh(), $message, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $reviews
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        $this->authorizeAdmin(request());
        $place = $review->place;
        $review->delete();
        if ($place) {
            $place->note = $place->reviews()->avg('note') ?? 0;
            $place->save();
        }
        $message = "Avis supprimé avec succès";
        return $this->sendResponse($review, $message);
    }
}
