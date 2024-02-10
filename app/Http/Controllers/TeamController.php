<?php

namespace App\Http\Controllers;
use App\Services\Team\TeamServiceInterface;
use Inertia\Inertia;

class TeamController extends Controller
{
    protected TeamServiceInterface $service;

    public function __construct(TeamServiceInterface $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->service->all();

        return Inertia::render('Home', [
            'teams'=>$result
        ]);
    }
}
