<?php

namespace App\Http\Controllers\Revisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $uid = $r->user()->id;

        $base = Review::where('reviewer_id', $uid);

        $totais = [
            'total'               => (clone $base)->count(),
            'atribuida'           => (clone $base)->where('status','atribuida')->count(),
            'em_revisao'          => (clone $base)->where('status','em_revisao')->count(),
            'revisao_solicitada'  => (clone $base)->where('status','revisao_solicitada')->count(),
            'parecer_enviado'     => (clone $base)->where('status','parecer_enviado')->count(),
        ];

        $recentes = Review::with(['submission:id,title,slug,status,submitted_at'])
            ->where('reviewer_id', $uid)
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get(['id','submission_id','status','updated_at']);

        return view('revisor.dashboard', compact('totais','recentes'));
    }
}
