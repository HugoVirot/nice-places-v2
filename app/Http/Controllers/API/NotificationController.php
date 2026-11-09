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
    public function getNotificationsByUser(Request $request, User $user)
    {
        abort_unless((int) $request->user()->id === (int) $user->id, 403, 'Vous ne pouvez consulter que vos notifications.');

        // on récupère les notifications de l'utilisateur en paramètre
        // triées dans l'ordre suivant : la plus récente en premier
        $user->load(['notifications' => function ($query) {
            $query->latest();
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
    public function update(Request $request, Notification $notification)
    {
        abort_unless((int) $request->user()->id === (int) $notification->user_id, 403, 'Vous ne pouvez modifier que vos notifications.');

        // on marque la notification comme lue
        $notification->read = true;
        $notification->save();
        return $this->sendResponse($notification, "Notification marquée comme lue");
    }
}
