<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LocationsLog;
use Illuminate\Http\Request;

class LocationsLogController extends Controller
{
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required',
            'long' => 'required',
        ]);

        $location = auth()->user()->location()->first();

        if (!$location) {
            $location = new LocationsLog();
            $location->user_id = auth()->id();
        }

        $location->latitude = $request->lat;
        $location->longitude = $request->long;
        $location->save();


        return response()->json(['message' => 'Location updated successfully']);
    }

    public function getLocations()
    {
        $locations = LocationsLog::where('user_id', '!=', auth()->id())->with('user')->get();
        return response()->json($locations);
    }
}
