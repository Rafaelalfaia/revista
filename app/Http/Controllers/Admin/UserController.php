<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        // Gate por permissão (do Spatie). Definidas no PermissionsSeeder.
        $this->middleware('permission:users.view')->only(['index']);
        $this->middleware('permission:users.manage')->only(['create','store','edit','update','destroy']);
    }

    public function index(Request $r)
    {
        $q = trim($r->get('q',''));

        $users = User::query()
            ->when($q, fn($w) =>
                $w->where(function($x) use ($q){
                    // Postgres-friendly: ILIKE
                    $x->where('name', 'ilike', "%{$q}%")
                      ->orWhere('email','ilike', "%{$q}%");
                })
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users','q'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Admin','Coordenador','Revisor','Autor'])
                     ->orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserStoreRequest $r)
    {
        $data = $r->validated();

        $user = new User();
        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->cpf      = $data['cpf']   ?? null;
        $user->password = Hash::make($data['password']);
        $user->save();

        // Papel único (se quiser múltiplos, trocar para array/checkbox)
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')
            ->with('ok','Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $roles = Role::whereIn('name', ['Admin','Coordenador','Revisor','Autor'])
                     ->orderBy('name')->get();
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(UserUpdateRequest $r, User $user)
    {
        $data = $r->validated();

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->cpf   = $data['cpf']   ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')
            ->with('ok','Usuário atualizado.');
    }

    public function destroy(User $user)
    {
        // Evita excluir a si mesmo (opcional, mas recomendado)
        if (auth()->id() === $user->id) {
            return back()->with('err','Você não pode excluir sua própria conta.');
        }

        $user->delete();
        return back()->with('ok','Usuário excluído.');
    }
}
