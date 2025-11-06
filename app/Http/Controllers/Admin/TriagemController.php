<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TriagemController extends Controller
{

    public function index(Request $request)
    {
        $q       = $request->get('q');
        $bucket  = $request->get('bucket', 'submetido');
        $from    = $request->get('from');
        $to      = $request->get('to');

        $map = [
            'submetido'       => [Submission::ST_SUBMITTED],
            'pendente_autor'  => [Submission::ST_REV_REQ],
            'em_triagem'      => [Submission::ST_SCREEN],
        ];
        $statuses = $map[$bucket] ?? [Submission::ST_SUBMITTED];

        $rows = Submission::with('author')
            ->whereIn('status', $statuses)
            ->when($from, fn($w)=>$w->whereDate('created_at','>=',$from))
            ->when($to,   fn($w)=>$w->whereDate('created_at','<=',$to))
            ->search($q)
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'submetido'      => Submission::where('status',Submission::ST_SUBMITTED)->count(),
            'pendente_autor' => Submission::where('status',Submission::ST_REV_REQ)->count(),
            'em_triagem'     => Submission::where('status',Submission::ST_SCREEN)->count(),
        ];

        return view('admin.triage.index', compact('rows','q','bucket','from','to','counts'));
    }


    public function show(Submission $submission)
    {
        $submission->load(['author','files']);
        return view('admin.triage.show', compact('submission'));
    }
}
