<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{


    public function index(Request $request)
    {
        $user  = Auth::user();
        $roles = $user?->getRoleNames() ?? collect();

        $hasUsers       = Schema::hasTable('users');
        $hasSubmissions = Schema::hasTable('submissions');
        $hasReviews     = Schema::hasTable('reviews');
        $hasArticles    = Schema::hasTable('articles');

        $kpis = [
            'users'       => $hasUsers       ? DB::table('users')->count()       : 0,
            'submissions' => $hasSubmissions ? DB::table('submissions')->count() : 0,
            'reviews'     => $hasReviews     ? DB::table('reviews')->count()     : 0,
            'articles'    => $hasArticles    ? DB::table('articles')->count()    : 0,
        ];

        $recentUsers = $hasUsers
            ? DB::table('users')->select('id','name','email','created_at')->orderByDesc('created_at')->limit(5)->get()
            : collect();

        return view('admin.dashboard', compact(
            'user','roles','kpis','recentUsers',
            'hasUsers','hasSubmissions','hasReviews','hasArticles'
        ));
    }
}
