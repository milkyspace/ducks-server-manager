<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $servers = Server::latest()->paginate(10);

        return view('servers.index', compact('servers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('servers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|string|max:255',
            'api_path' => 'max:255',
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        Server::create([
            'name' => $request->name,
            'ip' => $request->ip,
            'api_path' => $request->api_path,
            'login' => $request->login,
            'password' => $request->password,
            'active' => true,
        ]);

        return redirect()->route('servers.index')->with('status', 'Server Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Server $server): View
    {
        return view('servers.show', compact('server'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Server $server): View
    {
        return view('servers.edit', compact('server'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Server $server): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|string|max:255',
            'api_path' => 'max:255',
            'login' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $server->name = $request->name;
        $server->ip = $request->ip;
        $server->api_path = $request->api_path;
        $server->login = $request->login;
        $server->password = $request->password;
        $server->active = $request->active ? true : false;
        $server->save();

        return redirect()->route('servers.index')->with('status', 'Server Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Server $server): RedirectResponse
    {
        $server->delete();

        return redirect()->route('servers.index')->with('status', 'Server Delete Successfully');
    }


}
