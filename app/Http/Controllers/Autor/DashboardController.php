<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $uid = Auth::id();

        $base = Submission::where('user_id', $uid);
        $stats = [
            'total'      => (clone $base)->count(),
            'rascunho'   => (clone $base)->where('status','rascunho')->count(),
            'submetido'  => (clone $base)->where('status','submetido')->count(),
            'revisao'    => (clone $base)->where('status','em_revisao')->count(),
            'corrigir'   => (clone $base)->where('status','revisao_solicitada')->count(),
            'aceito'     => (clone $base)->where('status','aceito')->count(),
            'rejeitado'  => (clone $base)->where('status','rejeitado')->count(),
            'publicado'  => (clone $base)->where('status','publicado')->count(),
        ];

        // cole aqui os mesmos datasets que a view usa, se quiser:
        $recentes = $base->latest()->limit(8)->get();
        $rascunhos = $base->where('status','rascunho')->latest()->limit(3)->get();
        $paraCorrigir = Submission::where('user_id',$uid)->where('status','revisao_solicitada')->latest()->limit(3)->get();

        return view('autor.dashboard', compact('stats','recentes','rascunhos','paraCorrigir'));
    }
}
