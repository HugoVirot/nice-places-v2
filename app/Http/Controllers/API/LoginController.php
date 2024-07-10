<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\API\BaseController;
use App\Models\Departement;

class LoginController extends BaseControllerus
{
    /**
     * Tenter la connexion utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Laravel tente de connecter le user si l'email existe ET si le mdp en clair correspond à celui hashé 
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

             // si la connexion fonctionne
             // on récupère l'utilisateur et on charge son rôle
            $authUser = User::find(Auth::user()->id);
            $authUser->load('role'); 

            // on lui crée un token de session via la fonction createToken
            // le token est hashé en Sha-256 avant d'être enregistré dans la table personnal_access_tokens
            // on le stocke dans $success ainsi que ses autres infos renvoyées en json
            $success['token'] =  $authUser->createToken('LoginUser' . $authUser->id)->plainTextToken;
            $success['pseudo'] =  $authUser->pseudo;
            $success["email"] = $authUser->email;
            $success['id'] = $authUser->id;
            $success['role'] = $authUser->role->role;
            $success['departement'] = Departement::find($authUser->departement_id);

            // on renvoie la réponse 
            return $this->sendResponse($success, 'Vous êtes connecté');
            
        } else {
            // si échec de la connexion, on renvoie un message d'erreur
            return $this->sendError('Echec de la connexion.', 
            ['error' => 'L\'utilisateur n\'existe pas ou le mot de passe est incorrect']);
        }
    }
}
