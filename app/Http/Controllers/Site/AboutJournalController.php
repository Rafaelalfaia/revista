<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class AboutJournalController extends Controller
{
    public function index()
    {
        return view('site.pages.about-journal');
    }
}
