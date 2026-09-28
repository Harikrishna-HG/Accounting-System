<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->paginate(10);
        return view('accounting.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('accounting.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'pan_vat' => 'nullable|string|max:50',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Client::create($validated);

        return redirect()->route('accounting.clients.index')
            ->with('success', 'Client added successfully.');
    }

    public function edit(Client $client)
    {
        return view('accounting.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'pan_vat' => 'nullable|string|max:50',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $client->update($validated);

        return redirect()->route('accounting.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('accounting.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function show(Client $client)
    {
        $invoices = $client->invoices()->latest()->paginate(10);
        return view('accounting.clients.show', compact('client', 'invoices'));
    }
}
