<?php

namespace App\Http\Controllers;

use App\Models\PCRenouv;
use App\Models\Stock;
use App\Models\Client;
use App\Models\LocPret;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PCRenouvController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('gestrenouv.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gestrenouv.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PCRenouv $pcRenouv)
    {
        return view('gestrenouv.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PCRenouv $pcRenouv)
    {
        return view('gestrenouv.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PCRenouv $pcRenouv)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PCRenouv $pcRenouv)
    {
        //
    }

}