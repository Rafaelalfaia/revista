<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total'      => Submission::count(),
            'rascunho'   => Submission::where('status', Submission::ST_DRAFT)->count(),
            'submetido'  => Submission::where('status', Submission::ST_SUBMITTED)->count(),
            'triagem'    => Submission::where('status', Submission::ST_SCREEN)->count(),
            'revisao'    => Submission::where('status', Submission::ST_REVIEW)->count(),
            'rev_solic'  => Submission::where('status', Submission::ST_REV_REQ)->count(),
            'aceito'     => Submission::where('status', Submission::ST_ACCEPTED)->count(),
            'publicado'  => Submission::where('status', Submission::ST_PUBLISHED)->count(),
        ];

        $revisoresCount = Role::where('name','Revisor')->first()?->users()->count() ?? 0;

        return view('coordenador.dashboard', compact('stats','revisoresCount'));
    }
}
