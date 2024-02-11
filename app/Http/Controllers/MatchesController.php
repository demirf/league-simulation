<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMatchesRequest;
use App\Http\Requests\UpdateMatchesRequest;
use App\Models\Matches;
use App\Services\Match\MatchServiceInterface;
use Inertia\Inertia;

class MatchesController extends Controller
{
    protected MatchServiceInterface $service;
    public function __construct(MatchServiceInterface $service)
    {
        $this->service = $service;
    }
    public function getAll(int $tournamentId) {
        $result = $this->service->getAll($tournamentId);

        return Inertia::render('AllMatches', [
            'allMatches' => $result
        ]);
    }
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
    public function store(StoreMatchesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Matches $matches)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matches $matches)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMatchesRequest $request, Matches $matches)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matches $matches)
    {
        //
    }
}
