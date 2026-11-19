<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /**
     * Tenter la connexion utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Laravel tente de connecter le user si l'email existe ET si le mdp en clair correspond à celui hashé 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // si la connexion fonctionne, on récupère le user connecté et on charge son département
            $authUser = User::find(Auth::user()->id);
            $authUser->load(['department.region', 'role']);

            // on renvoie la réponse 
            return $this->sendResponse($authUser, 'Vous êtes connecté');
        } else {
            // si échec de la connexion, on renvoie un message d'erreur
            return $this->sendError(
                'Echec de la connexion.',
                ['error' => 'L\'utilisateur n\'existe pas ou le mot de passe est incorrect'],
                401
            );
        }
    }

    /**
     * Déconnexion utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->sendResponse(null, 'Déconnexion réussie');
    }
}
