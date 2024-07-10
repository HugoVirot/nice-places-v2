<?php

namespace App\Http\Controllers\API;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Region::all();
        return $this->sendResponse($regions, "Régions récupérées avec succès");
    }
}
