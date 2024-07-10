<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{

    public function __construct()
    {
        // middleware sanctum appliqué sur store / update / destroy
        $this->middleware('auth:sanctum')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return $this->sendResponse($categories, 'Catégories récupérés avec succès');
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
            'name' => 'required|string|min:5|max:50',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:15'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        // on crée un nouveau lieu
        $categorie = Category::create($request->all());
        // $categorie = Category::create([
        //     'nom' => $request->nom,
        //     'icone' => $request->icone,
        //     'couleur' => $request->couleur
        // ]);

        // On retourne les informations en JSON
        $message = "La catégorie a bien été créée";
        return $this->sendResponse($categorie, $message, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $categorie
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category->load("places");
        $message = "La catégorie a bien été récupérée";
        return $this->sendResponse($category, $message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $categorie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'icon' => 'required|max:50',
            'color' => 'required|max:15'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        // on modifie les infos du lieu
        $category->update($request->except('_token'));

        // On retourne la réponse en JSON
        $message = "La catégorie a bien été modifiée";
        return $this->sendResponse($category, $message, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $categorie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        $message = "Catégorie supprimée avec succès";
        return $this->sendResponse($category, $message);
    }
}
