<?php

namespace App\Http\Controllers\API;

use App\Models\Department;

class DepartmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::all();
        return $this->sendResponse($departments, 'Départements récupérés avec succès');
    }
}
