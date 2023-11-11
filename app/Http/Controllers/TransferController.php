<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'from_email' => 'required|email',
            'to_email' => 'required|email',
            'title' => 'required|min:3',
            'message' => 'nullable|min:5',
            'file' => 'required|file|max:2097152',
        ]);

        $request->file('file')->storeAs('transfers', $request->file('file')->getClientOriginalName());

        Transfer::create([
            'from_email' => $request->input('from_email'),
            'to_email' => $request->input('to_email'),
            'title' => $request->input('title'),
            'message' => $request->input('message'),
            'file' => $request->file('file')->getClientOriginalName(),
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
