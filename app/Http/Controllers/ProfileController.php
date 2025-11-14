<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{

    public function edit(Request $request)
    {
        $user = $request->user();

        return view('profile.edit', compact('user'));
    }

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        Validator::make($request->all(), [
            'name'  => ['required','string','max:120'],
            'email' => ['required','email','max:190','unique:users,email,'.$user->id],

            'cpf'   => ['nullable','string','max:20','unique:users,cpf,'.$user->id],
        ], [
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'cpf.unique'   => 'Este CPF já está sendo utilizado.',
        ])->validate();

        $user->fill($request->only('name','email','cpf'))->save();

        $rawCpf = $request->input('cpf');
        $cpfDigits = preg_replace('/\D+/', '', (string) $rawCpf) ?: null; // null se vazio
        $user->fill([
            'name'  => $request->input('name'),
            'email' => $request->input('email'),
            'cpf'   => $cpfDigits,
        ])->save();


        return back()->with('ok', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        Validator::make($request->all(), [
            'current_password' => ['required'],
            'password'         => ['required','confirmed','min:8'],
        ])->after(function ($validator) use ($user, $request) {
            if (! Hash::check($request->input('current_password'), $user->password)) {
                $validator->errors()->add('current_password', 'A senha atual não confere.');
            }
        })->validate();

        $user->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        return back()->with('ok', 'Senha alterada com sucesso.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ], [
            'avatar.image' => 'Envie uma imagem válida.',
            'avatar.mimes' => 'Formatos permitidos: JPG, PNG, WEBP.',
            'avatar.max'   => 'Tamanho máximo: 2MB.',
        ]);

        $user = $request->user();


        $dir = public_path('images/avatars');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }


        $tmp  = $request->file('avatar')->getRealPath();
        $mime = mime_content_type($tmp);

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $img = imagecreatefromjpeg($tmp);
        } elseif ($mime === 'image/png') {
            $img = imagecreatefrompng($tmp);
        } elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $img = @imagecreatefromwebp($tmp);
        } else {
            return back()->withErrors(['avatar' => 'Formato de imagem não suportado neste servidor.']);
        }


        $w = imagesx($img); $h = imagesy($img);
        $canvas = imagecreatetruecolor($w, $h);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopy($canvas, $img, 0, 0, 0, 0, $w, $h);

        $dest = $dir.DIRECTORY_SEPARATOR.$user->id.'.png';
        imagepng($canvas, $dest, 6);

        imagedestroy($img);
        imagedestroy($canvas);

        return back()->with('ok', 'Foto de perfil atualizada.');
    }
}
