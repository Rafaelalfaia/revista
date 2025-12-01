<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class JournalTeamController extends Controller
{
    public function index()
    {
        return view('site.pages.journal-team');
    }
}
