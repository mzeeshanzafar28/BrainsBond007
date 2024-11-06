<?php

namespace App\Http\Controllers;

use App\Models\Prop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropController extends Controller
{
   
    public function add_prop(Request $request)
{
    $request->validate([
        'country' => 'required|string|max:255',
        'exe_url' => 'required|string|unique:props,exe_url',
        // 'is_premium' => 'boolean',
        'organization_location' => 'required|string|max:255',
    ]);

    $lastPort = Prop::max('port');
    $port = $lastPort ? $lastPort + 1 : 1000; 
    $siteUrl = config('app.url'); 
    $authUsername = Auth::user()->username; 
    $authId = Auth::id();
    $connectionUrl = "{$siteUrl}/{$authUsername}/{$authId}/{$port}";

    $prop = Prop::create([
        'user_id' => $authId,
        'country' => $request->country,
        'exe_url' => $request->exe_url,
        // 'is_premium' => $request->is_premium ?? false,
        'is_premium' => false,
        'organization_location' => $request->organization_location,
        'port' => $port,
        'connection_url' => $connectionUrl,
    ]);

    return response()->json($prop, 201);
}


   
    public function update_prop(Request $request)
    {
        $prop_id = $request->validate([
            'prop_id' => 'required|integer|exists:props,id'
        ]);

        $prop = Prop::where('id', $prop_id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'country' => 'sometimes|required|string|max:255',
            'exe_url' => 'sometimes|required|string|unique:props,exe_url,' . $prop->id,
            'is_premium' => 'boolean',
            'organization_location' => 'sometimes|required|string|max:255',
            'port' => 'sometimes|required|integer',
            'connection_url' => 'sometimes|required|string|max:255',
        ]);

        $prop->update($request->only([
            'country',
            'exe_url',
            'is_premium',
            'organization_location',
            'port',
            'connection_url',
        ]));

        return response()->json($prop, 200);
    }

  
    public function delete_prop(Request $request)
    {
        $prop_id = $request->validate([
            'prop_id' => 'required|integer|exists:props,id'
        ]);
        $prop = Prop::where('id', $prop_id)->where('user_id', Auth::id())->firstOrFail();

        $prop->delete();

        return response()->json(['message' => 'Prop deleted successfully'], 200);
    }

    public function get_props()
    {
        $user_id = Auth::id();
        $props = Prop::where('user_id', $user_id)->get();

        return response()->json($props, 200);
    }
}
