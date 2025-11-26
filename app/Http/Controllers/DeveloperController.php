<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function index()
    {
        $developers = collect([
            (object)[
                'name' => 'Murilo Moschen',
                'role' => 'Contribuidor',
                'avatar' => '/images/avatars/murilo.png',
                'linkedin' => 'https://www.linkedin.com/in/murilo-moschen-0b59a8252/',
                'github' => 'https://github.com/22moschen',
            ],

            (object)[
                'name' => 'Rafael Alfaia',
                'role' => 'Full Stack Developer',
                'avatar' => '/images/avatars/9.png',
                'linkedin' => 'https://www.linkedin.com/',
                'github' => 'https://github.com/Rafaelalfaia',
            ],
            // Adicione mais desenvolvedores conforme necessário
        ]);

        return view('desenvolvedores', compact('developers'));
    }
}
