<?php

namespace App\Http\Controllers\API;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;

class NotificationController extends BaseController
{

    public function __construct()
    {
        // middleware sanctum appliqué sur tout le contrôleur
        $this->middleware('auth:sanctum')->except('store');
    }


    /**
     * Get the notifications of a particular user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNotificationsByUser(User $user)
    {
        // on récupère les notifications de l'utilisateur en paramètre
        // triées dans l'ordre suivant : la plus récente en premier
        $user->load(['notifications' => function ($query) {
            $query->latest()->get();
        }]);
        return response()->json($user->notifications);
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
            'message' => 'required|string|min:20|max:3000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors());
        }

        $notification = Notification::create($request->all());
        
        $this->sendResponse($notification, 'Notification envoyée');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Notification $notification)
    {
        // on marque la notification comme lue
        $notification->lue = true;
        $notification->save();
        $this->sendResponse($notification, "Notification marquée comme lue", 201);
    }
}
