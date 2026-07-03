<?php

namespace App\Http\Controllers;

use App\Http\Services\Service\IndexService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return IndexService::execute();
    }
}
