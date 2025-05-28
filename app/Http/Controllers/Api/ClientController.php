<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
     public function index()
    {

        $clients = auth()->user()->clients()->latest()->get();
        if($clients->isEmpty()){
            return response()->json(['message' => 'No clients found'], 404);
        }
        return response()->json($clients);
    }


    public function store(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'contact_person' => 'nullable|string|max:255',
        ]);
        if($validated->fails()){
            return response()->json(['errors'=> $validated->errors()], 422);
        }
        if(auth()->user()->clients()->where('name', $request->name)->exists()){
            return response()->json(['message' => 'Client name already exists'], 409);
        }
        $client = new Client();
        $client->user_id = auth()->user()->id;
        $client->name = $request->name;
        $client->email = $request->email;
        $client->contact_person = $request->contact_person;
        $client->save();
        if(!$client){
            return response()->json(['message' => 'Failed to create client'], 500);
        }
        return response()->json(['message' => 'Client created successfully', 'client' => $client], 201);
    }


    public function show($id)
    {
        $client = Client::find($id);
       if(!$client){
        return response()->json(['message' => 'Client not found'], 404);
       }
       if($client->user_id != auth()->user()->id){
        return response()->json(['message' => 'Unauthorized'], 401);
       }

        return response()->json(['message' => 'Client found', 'client' => $client], 200);
    }

    public function update(Request $request)
    {
       $client = Client::find($request->client_id);
        $validated = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'contact_person' => 'nullable|string|max:255',
        ]);
        if($validated->fails()){
            return response()->json(['errors'=> $validated->errors()], 422);
        }
        if(auth()->user()->clients()->where('id', '!=', $client->id)->where('name', $request->name)->exists()){
            return response()->json(['message' => 'Client name already exists'], 409);
        }

        $client->user_id = auth()->user()->id;
        $client->name = $request->name;
        $client->email = $request->email;
        $client->contact_person = $request->contact_person;
        $client->save();
        if(!$client){
            return response()->json(['message' => 'Failed to update client'], 500);
        }
        return response()->json(['message' => 'Client updated successfully', 'client' => $client], 201);
    }


    public function destroy( $id)
    {
        $client = Client::find($id);
        if(!$client){
            return response()->json(['message' => 'Client not found'], 404);
        }
         if($client->user_id !== auth()->user()->id){
        return response()->json(['message' => 'Unauthorized'], 401);
       }

        $client->delete();

       return response()->json(['message' => 'Client deleted successfully'], 200);

    }
}
