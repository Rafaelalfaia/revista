<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class AuthorGuideController extends Controller
{
    public function index()
    {
        return view('site.pages.authors');
    }
}
