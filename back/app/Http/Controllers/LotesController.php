<?php

namespace App\Http\Controllers;

use App\Http\Services\Lotes\IndexLoteService;
use App\Http\Services\Lotes\CreateLoteService;
use App\Http\Services\Lotes\DestroyLoteService;
use App\Http\Services\Lotes\GetLotesCheckedService;
use Illuminate\Http\Request;

class LotesController extends Controller
{

    public function index(Request $request)
    {
        return IndexLoteService::index($request);
    }

    public function store(Request $request)
    {
        return CreateLoteService::index($request);
    }

    public function destroy(Request $request, int $id)
    {
        return DestroyLoteService::index($request, $id);
    }

    public function get_check_lotes(Request $request)
    {
        return GetLotesCheckedService::index($request);
    }
}