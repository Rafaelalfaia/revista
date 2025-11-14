<?php

namespace App\Http\Controllers\Coordenador;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class RevisorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reviewers.manage');
    }

    public function index(Request $r)
    {
        $q = trim($r->get('q',''));

        $revisores = User::role('Revisor')
            ->where('created_by_id', $r->user()->id)
            ->when($q, fn($w) =>
                $w->where(function($x) use ($q){
                    $x->where('name','ilike',"%{$q}%")
                      ->orWhere('email','ilike',"%{$q}%")
                      ->orWhere('cpf','ilike',"%{$q}%");
                })
            )
            ->with('categories:id,name')
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('coordenador.revisores.index', compact('revisores','q'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        return view('coordenador.revisores.create', compact('categories'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'     => ['required','string','max:120'],
            'email'    => ['required_without:cpf','nullable','email','max:160','unique:users,email'],
            'cpf'      => ['required_without:email','nullable','digits:11','unique:users,cpf'],
            'password' => ['required','string','min:8','confirmed'],
            'categories'   => ['nullable','array'],
            'categories.*' => ['integer', Rule::exists('categories','id')],
        ], [
            'email.required_without' => 'Informe e-mail ou CPF.',
            'cpf.required_without'   => 'Informe CPF ou e-mail.',
            'cpf.digits'             => 'CPF deve ter 11 dígitos.',
        ]);

        $user = new User();
        $user->name          = $data['name'];
        $user->email         = $data['email'] ?? null;
        $user->cpf           = $data['cpf']   ?? null;
        $user->password      = Hash::make($data['password']);
        $user->created_by_id = $r->user()->id;
        $user->save();

        $user->syncRoles(['Revisor']);
        $user->categories()->sync($data['categories'] ?? []);

        return redirect()->route('coordenador.revisores.index')->with('ok','Revisor criado com sucesso.');
    }

    public function edit(Request $r, User $user)
    {

        if (!$user->hasRole('Revisor') || $user->created_by_id !== $r->user()->id) {
            return redirect()->route('coordenador.revisores.index')->with('err','Revisor não pertence a você.');
        }

        $categories = Category::orderBy('name')->get(['id','name']);
        $selected   = $user->categories()->pluck('categories.id')->all();

        return view('coordenador.revisores.edit', compact('user','categories','selected'));
    }

    public function update(Request $r, User $user)
    {
        if (!$user->hasRole('Revisor') || $user->created_by_id !== $r->user()->id) {
            return redirect()->route('coordenador.revisores.index')->with('err','Revisor não pertence a você.');
        }

        $data = $r->validate([
            'name'     => ['required','string','max:120'],
            'email'    => ['required_without:cpf','nullable','email','max:160', Rule::unique('users','email')->ignore($user->id)],
            'cpf'      => ['required_without:email','nullable','digits:11', Rule::unique('users','cpf')->ignore($user->id)],
            'password' => ['nullable','string','min:8','confirmed'],
            'categories'   => ['nullable','array'],
            'categories.*' => ['integer', Rule::exists('categories','id')],
        ], [
            'email.required_without' => 'Informe e-mail ou CPF.',
            'cpf.required_without'   => 'Informe CPF ou e-mail.',
            'cpf.digits'             => 'CPF deve ter 11 dígitos.',
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'] ?? null;
        $user->cpf   = $data['cpf']   ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->syncRoles(['Revisor']);
        $user->categories()->sync($data['categories'] ?? []);

        return redirect()->route('coordenador.revisores.index')->with('ok','Revisor atualizado.');
    }

    public function destroy(Request $r, User $user)
    {
        if (!$user->hasRole('Revisor') || $user->created_by_id !== $r->user()->id) {
            return back()->with('err','Revisor não pertence a você.');
        }
        if ($user->id === $r->user()->id) {
            return back()->with('err','Você não pode excluir sua própria conta.');
        }
        $user->delete();
        return back()->with('ok','Revisor excluído.');
    }
}
