<?php

namespace App\Http\Controllers;

use App\Company;
use App\History;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    public function index()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(403);
    }

    public function show(History $history)
    {
        abort(404);
    }

    public function update(Request $request, Company $company, History $history)
    {
        // authorization
        $this->authorize('updateHistory', [$history->check, $company]);

        request()->validate([ 'date' => 'required|date' ]);

        $date = new Carbon(request('date'));

        $history->update(['date' => $date->format('Y-m-d')]);

        Log::info(request()->user()->name . ' updated history date.');

        return ['message' => 'History successfully updated.'];
    }

    public function destroy(History $history)
    {
        abort(403);
    }
}
