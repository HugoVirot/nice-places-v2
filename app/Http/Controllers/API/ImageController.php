<?php

namespace App\Http\Controllers\API;

use App\Models\Image;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageController extends BaseController
{

    public function __construct()
    {
        // middleware sanctum appliqué sur index / store / update / destroy
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of all the images for the administrator.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorizeAdmin(request());

        $images = Image::with('place')->get();
        return $this->sendResponse($images, 'Images récupérées avec succès');
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
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'place_id' => 'required|integer|exists:places,id',
        ]);

        $user = $request->user();
        $place = Place::findOrFail($validated['place_id']);
        $file = $request->file('image');
        $imageCount = Image::where('place_id', $place->id)->count();
        $imageName = substr(Str::slug($place->name), 0, 25)
            . '_image_' . Str::random(8) . '.' . $file->extension();
        $imageInfo = in_array(strtolower($file->extension()), ['jpg', 'jpeg', 'png'], true)
            ? getimagesize($file->getRealPath())
            : false;
        $fileSize = round($file->getSize() / 1000);
        $isAdmin = (int) $user->role?->id === 2;
        $file->move(public_path('images'), $imageName);

        $image = Image::create([
            'name' => $imageName,
            'user_id' => $user->id,
            'place_id' => $place->id,
            'width' => $imageInfo ? $imageInfo[0] : null,
            'height' => $imageInfo ? $imageInfo[1] : null,
            'size' => $fileSize,
            'cover' => $imageCount === 0,
            'status' => $isAdmin ? 'validée' : 'en attente',
        ]);

        $message = $isAdmin
            ? 'Image envoyée !'
            : "Image envoyée. Elle sera visible après validation par l'administrateur.";

        return $this->sendResponse($image->fresh()->load('place'), $message, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Avis  $avis
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        return $this->sendResponse($image, "Image récupérée avec succès !");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        $this->authorizeAdmin($request);
        $validated = $request->validate([
            'cover' => 'sometimes|required|boolean',
            'status' => 'sometimes|required|in:validée,en attente,refusée',
        ]);
        if (empty($validated)) {
            return $this->sendError('Aucune modification fournie.', [], 422);
        }

        $wasCover = $image->cover;
        if (!empty($validated['cover']) && $image->place_id) {
            Image::where('place_id', $image->place_id)->where('id', '!=', $image->id)->update(['cover' => false]);
        }
        $image->update($validated);

        if (
            $wasCover
            && $image->place_id
            && $image->status !== 'validée'
            && !Image::where('place_id', $image->place_id)
                ->where('cover', true)
                ->where('status', 'validée')
                ->exists()
        ) {
            $nextCover = Image::where('place_id', $image->place_id)
                ->where('status', 'validée')
                ->where('id', '!=', $image->id)
                ->first();
            $nextCover?->update(['cover' => true]);
        }

        // On retourne la réponse en JSON
        $message = "L'image a bien été modifiée";
        return $this->sendResponse($image->fresh()->load('place'), $message, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        $this->authorizeAdmin(request());

        // on supprime l'image du dossier public/images
        File::delete(public_path("images/" . $image->name));

        // on la supprime de la table images
        $image->delete();

        // on retourne la réponse en json
        $message = "L'image a bien été supprimée";
        return $this->sendResponse($image, $message);
    }
}
