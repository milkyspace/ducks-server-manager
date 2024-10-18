<?php

namespace App\Http\Controllers\Business\Pages;

use App\Http\Controllers\Controller;
use App\Models\Vpnuser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VpnusersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $vpnusers = Vpnuser::latest()->paginate(10);

        return view('vpnusers.index', compact('vpnusers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('vpnusers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tg_id' => 'required|string|max:255',
        ]);

        Vpnuser::create([
            'name' => $request->name,
            'tg_id' => $request->ip,
        ]);

        return redirect()->route('vpnusers.index')->with('status', 'vpnuser Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vpnuser $vpnuser): View
    {
        return view('vpnusers.show', compact('vpnuser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vpnuser $vpnuser): View
    {
        return view('vpnusers.edit', compact('vpnuser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vpnuser $vpnuser): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tg_id' => 'required|string|max:255',
        ]);

        $vpnuser->name = $request->name;
        $vpnuser->tg_id = $request->tg_id;
        $vpnuser->save();

        return redirect()->route('vpnusersweb.index')->with('Status', 'vpnuser Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vpnuser $vpnuser): RedirectResponse
    {
        $vpnuser->delete();

        return redirect()->route('vpnusers.index')->with('status', 'vpnuser Delete Successfully');
    }
}
