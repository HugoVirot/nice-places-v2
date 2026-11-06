<?php

namespace App\Http\Controllers\API;

use App\Models\Place;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlaceController extends BaseController
{

    public function __construct()
    {
        // middleware sanctum appliqué sur store / update / destroy
        $this->middleware('auth:sanctum')->except(['index', 'show', 'getAllPlaces']);
    }


    /**
     * Récupérer les lieux validés uniquement (pour l'affichage utilisateur)
     *
     * @return \Illuminate\Http\Response 
     */
    public function index()
    {
        $places = Place::where('status', 'validé')->get();
        return $this->sendResponse($places, 'Lieux validés récupérés avec succès');
    }


    /**
     * Récupérer tous les lieux (pour le back-office admin)
     *
     * @return \Illuminate\Http\Response 
     */
    public function getAllPlaces(Request $request)
    {
        $this->authorizeAdmin($request);

        $places = Place::all();
        return $this->sendResponse($places, 'Lieux récupérés avec succès');
    }

    public function geocode(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:3|max:150',
        ]);

        $client = Http::acceptJson()
            ->connectTimeout(3)
            ->timeout(8);

        if (defined('CURLSSLOPT_NATIVE_CA')) {
            $client = $client->withOptions([
                'curl' => [CURLOPT_SSL_OPTIONS => CURLSSLOPT_NATIVE_CA],
            ]);
        }

        try {
            $response = $client
                ->get('https://data.geopf.fr/geocodage/search', [
                    'q' => $validated['q'],
                    'index' => 'address,poi',
                    'limit' => 5,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('IGN geocoding service connection failed.', ['exception' => $exception->getMessage()]);

            return $this->sendError('La recherche de lieu est temporairement indisponible.', [], 503);
        }

        $features = $response->json('features');
        if (!$response->successful() || !is_array($features)) {
            Log::warning('IGN geocoding service returned an invalid response.', [
                'status' => $response->status(),
            ]);

            return $this->sendError('La recherche de lieu est temporairement indisponible.', [], 503);
        }

        return $this->sendResponse($features, 'Résultats de recherche récupérés avec succès.');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // on précise les critères attendus pour les champs
        // longitude et latitude : intervalles possibles définis avec between
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'description' => 'nullable|string|max:3000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'category_id' => 'required|integer|exists:categories,id',
            'note' => 'nullable|numeric|min:1|max:10',
            'time' => 'nullable|integer|min:1|max:24',
            'difficulty' => 'nullable|in:famille,amateur,sportif',
            'kilometres' => 'nullable|integer|min:1|max:100',
            'department_id' => 'required|integer|exists:departments,id',
            'adress' => 'required|string|min:5|max:75',
            'postcode' => 'required|string|size:5',
            'city' => 'required|string|min:3|max:50',
        ]);

        $validated['description'] = $validated['description'] ?? 'Aucune description renseignée.';
        $validated['note'] = $validated['note'] ?? 5;
        $validated['time'] = $validated['time'] ?? 1;
        $validated['difficulty'] = $validated['difficulty'] ?? 'famille';

        $isAdmin = (int) $request->user()->role?->id === 2;
        $place = Place::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => $isAdmin ? 'validé' : 'en attente',
        ]);
        $message = $isAdmin
            ? 'Le lieu a bien été créé.'
            : "Le lieu a bien été proposé (en attente de validation par l'administrateur).";

        return $this->sendResponse($place->fresh(), $message, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Place  $lieu
     * @return \Illuminate\Http\Response
     */
    public function show(Place $lieu)
    {
        $lieu->load(['reviews.user', 'images']);
        return $this->sendResponse($lieu, 'Lieu récupéré avec succès');
    }


    // récupérer les places postés par l'utilisateur

    public function getPlacesByUser(Request $request)
    {
        $userPlaces = $request->user()->places()->latest()->get();
        return $this->sendResponse($userPlaces, 'Nombre de lieux postés récupéré avec succès');
    }


    // récupérer le nombre d'images par lieu

    public function getImagesNumberByPlace(Place $lieu)
    {
        $imagesNumber = count($lieu->images);
        return $this->sendResponse($imagesNumber, 'Nombre d\'images pour le lieu récupéré avec succès');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Place  $lieu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Place $lieu)
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|min:10|max:100',
            'description' => 'sometimes|required|string|min:25|max:3000',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'note' => 'sometimes|required|numeric|min:1|max:10',
            'time' => 'sometimes|required|integer|min:1|max:24',
            'difficulty' => 'sometimes|required|in:famille,amateur,sportif',
            'kilometres' => 'sometimes|nullable|integer|min:1|max:100',
            'department_id' => 'sometimes|required|integer|exists:departments,id',
            'adress' => 'sometimes|required|string|min:5|max:75',
            'postcode' => 'sometimes|required|string|size:5',
            'city' => 'sometimes|required|string|min:3|max:50',
            'status' => 'sometimes|required|in:validé,en attente,à modifier,refusé',
            'comment' => 'sometimes|required_if:status,à modifier,refusé|nullable|string|min:5|max:1000',
        ]);

        if (empty($validated)) {
            return $this->sendError('Aucune modification fournie.', [], 422);
        }

        $previousStatus = $lieu->status;
        $lieu->update($validated);

        if (
            isset($validated['status'])
            && $validated['status'] !== $previousStatus
            && $lieu->user_id
            && in_array($validated['status'], ['validé', 'à modifier', 'refusé'], true)
        ) {
            $statusMessages = [
                'validé' => 'a été validé',
                'à modifier' => 'nécessite des modifications',
                'refusé' => 'a été refusé',
            ];
            \App\Models\Notification::create([
                'title' => "Votre lieu « {$lieu->name} » {$statusMessages[$validated['status']]}",
                'message' => $validated['status'] === 'à modifier' || $validated['status'] === 'refusé'
                    ? ($lieu->comment ?: 'Aucun commentaire transmis.')
                    : "Votre lieu « {$lieu->name} » est maintenant visible sur Nice Places.",
                'user_id' => $lieu->user_id,
                'place_id' => $lieu->id,
            ]);
        }

        // On retourne la réponse en JSON
        $message = "Lieu modifié avec succès";
        return $this->sendResponse($lieu->fresh(), $message);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Place  $lieu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Place $lieu)
    {
        $this->authorizeAdmin(request());

        // on supprime le lieu
        $lieu->delete();

        // on retourne la réponse JSON
        $message = "Lieu supprimé avec succès";
        return $this->sendResponse($lieu, $message);
    }
}
