<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\User;

class EditorialBoardController extends Controller
{
    public function index()
    {
        $reviewers = User::role('Revisor')
            ->with(['categories' => function ($q) {
                $q->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('site.pages.editorial-board', compact('reviewers'));
    }
}
