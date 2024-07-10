<?php

namespace App\Http\Controllers\API;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Place;
use App\Models\User;

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
        $validator = Validator::make($request->all(), [
            'note' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|min:5|max:2000',
            'place_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        // on crée un nouvel avis 
        $reviews = Review::create([
            'note' => $request->note,
            'comment' => $request->comment,
            'place_id' => $request->place_id,
            'user_id' => $request->user_id,
            // selon le statut de l'utilisateur (admin ou utilisateur standard), on valide ou on met en attente l'avis
            'status' => User::find($request->user_id)->role->name == "admin" ? "validé" : "en attente",
        ]);

        // actualiser la note moyenne du lieu en fonction de la note de l'avis

        // 1 ) on récupère le lieu avec son nombre d'avis
        $lieu = Place::find($reviews->lieu_id);
        $lieu->loadCount('reviews');

        // 2) on calcule la nouvelle note moyenne = (note actuelle * nb notes + nouvelle note) / (nb notes + 1)

        $reviewRating = intval($request->note); // intval transforme la note en integer
        $currentAverageRating = $lieu->note;    // note moyenne actuelle de l'article
        $notesNumber = $lieu->avis_count + 1;   // comptage du nombre de notes
        $newAverageRating = ($currentAverageRating * $notesNumber + $reviewRating) / ($notesNumber + 1); // calcul final

        // 3) on la sauvegarde
        $lieu->note = $newAverageRating;
        $lieu->save();

        // On retourne l'avis créé avec un message de confirmation
        $message = "Votre avis a bien été enregistré. Il sera contrôlé par l'administrateur.";
        return $this->sendResponse($reviews, $message, 201);
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
        $validator = Validator::make($request->all(), [
            'note' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|min:5|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        $review->update($request->except('_token'));

        $message = "L'avis a bien été modifié";
        return $this->sendResponse($review, $message, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $reviews
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        $review->delete();
        $message = "Avis supprimé avec succès";
        return $this->sendResponse($review, $message);
    }
}
