<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = auth()->user()->clients()->latest()->paginate(15);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:14',
        ]);

        auth()->user()->clients()->create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $this->ensureOwnership($client);
        $client->load('documents');
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $this->ensureOwnership($client);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->ensureOwnership($client);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:14',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
         $this->ensureOwnership($client);
         $client->delete();
         return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }


    private function ensureOwnership(Client $client): void
{
    abort_unless($client->user_id == auth()->id(), 404);
}
}
