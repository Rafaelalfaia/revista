<?php

namespace App\Http\Controllers\Autor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $base = Submission::query()
            ->select('id','title','slug','status','submitted_at','updated_at','published_at')
            ->where('user_id', $user->id);

        $with = ['categories:id,name'];

        $stats = [
            'total'               => (clone $base)->count(),
            'rascunho'            => (clone $base)->where('status', Submission::ST_DRAFT)->count(),
            'submetido'           => (clone $base)->where('status', Submission::ST_SUBMITTED)->count(),
            'em_triagem'          => (clone $base)->where('status', Submission::ST_SCREEN)->count(),
            'em_revisao'          => (clone $base)->where('status', Submission::ST_REVIEW)->count(),
            'revisao_solicitada'  => (clone $base)->where('status', Submission::ST_REV_REQ)->count(),
            'aceito'              => (clone $base)->where('status', Submission::ST_ACCEPTED)->count(),
            'rejeitado'           => (clone $base)->where('status', Submission::ST_REJECTED)->count(),
            'publicado'           => (clone $base)->where('status', Submission::ST_PUBLISHED)->count(),
        ];

        $rascunhos = (clone $base)
            ->with($with)
            ->where('status', Submission::ST_DRAFT)
            ->latest('updated_at')
            ->take(8)
            ->get();

        $paraCorrigir = (clone $base)
            ->with($with)
            ->where('status', Submission::ST_REV_REQ)
            ->latest('updated_at')
            ->take(8)
            ->get();

        $recentes = (clone $base)
            ->with($with)
            ->orderByRaw("CASE status
                WHEN 'rascunho' THEN 0
                WHEN 'submetido' THEN 1
                WHEN 'em_triagem' THEN 2
                WHEN 'em_revisao' THEN 3
                WHEN 'revisao_solicitada' THEN 4
                WHEN 'aceito' THEN 5
                WHEN 'publicado' THEN 6
                ELSE 9 END")
            ->orderByDesc('updated_at')
            ->take(12)
            ->get();

        return view('autor.dashboard', compact('stats','rascunhos','paraCorrigir','recentes'));
    }
}
