<?php

namespace App\Http\Controllers\Revisor;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        // permite Revisor (e Admin, se quiser ver)
        $this->middleware(['auth','verified','role:Revisor|Admin']);
    }

    public function index()
    {
        return view('revisor.dashboard');
    }
}
