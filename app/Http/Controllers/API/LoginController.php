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

            // forcer la régénération du token csrf : à checker car déclenche l'erreur CSRF token mismatch
            // ou l'erreur "Session store not set on request" si pas de header Origin : 127.0.0.1:8000
            //https://laracasts.com/discuss/channels/laravel/is-it-really-necessary-to-call-session-regenerate-after-login
            // if ($request->session()) {
            //     $request->session()->regenerate();
            // }

            // si la connexion fonctionne, on récupère le user connecté et on charge son département
            $authUser = User::find(Auth::user()->id);
            $authUser->load('department');

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
        //dd($request);
        // déconnecte de la session en cours et invalide le token du cookie de session
        Auth::guard('web')->logout();

        return $this->sendResponse(null, 'Déconnexion réussie');
    }
}
