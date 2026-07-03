<?php

namespace App\Http\Services\AccountMethod;

use Illuminate\Http\{
Request,
JsonResponse
};
use App\Models\AccountMethod;

class IndexAccountMethodService
{
    public static function execute(Request $request): JsonResponse
    {
        $query = AccountMethod::query();

        $search = $request->input("search");

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("provider_name", "like", "%$search%")
                    ->orWhere("identifier", "like", "%$search%")
                    ->orWhere("holder_name", "like", "%$search%");
            });
        }

        $sort = $request->input("sort");
        $direction = $request->input("direction") == "desc" ? "desc" : "asc";

        if ($sort) {
            $query->orderBy($sort, $direction);
        }
        else {
            $query->orderBy('id', 'desc');
        }

        $methods = $query
            ->paginate(10)
            ->appends(request()->query());

        return response()->json([
            "rows" => $methods,
            "sort" => $request->query("sort"),
            "direction" => $request->query("direction"),
            "search" => $request->query("search")
        ]);
    }
}