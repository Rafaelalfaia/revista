<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EditionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:Admin']);
    }

    public function index(Request $r)
    {
        $q = trim((string) $r->query('q',''));
        $editions = Edition::query()
            ->when($q !== '', fn($qq) =>
                $qq->where('title','ilike',"%{$q}%")->orWhere('slug','ilike',"%{$q}%"))
            ->latest('release_date')->latest('created_at')
            ->paginate(12);
        return view('admin.editions.index', compact('editions','q'));
    }

    public function create()
    {
        return view('admin.editions.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title' => ['required','string','max:160'],
            'slug'  => ['nullable','string','max:180','unique:editions,slug'],
            'description' => ['nullable','string'],
            'release_date' => ['nullable','date'],
            'published_at' => ['nullable','date'],
            'profile_photo' => ['nullable','image','max:5120'],
            'cover_photo'   => ['nullable','image','max:8192'],
        ]);

        $ed = new Edition();
        $ed->title = $data['title'];
        $ed->slug = $data['slug'] ?? Str::slug($data['title']).'-'.Str::random(6);
        $ed->description = $data['description'] ?? null;
        $ed->release_date = $data['release_date'] ?? null;
        $ed->published_at = $data['published_at'] ?? null;

        if ($r->hasFile('profile_photo')) {
            $path = $r->file('profile_photo')->storePublicly('editions/profile','public');
            $ed->profile_photo_path = $path;
            $ed->profile_photo_disk = 'public';
        }
        if ($r->hasFile('cover_photo')) {
            $path = $r->file('cover_photo')->storePublicly('editions/cover','public');
            $ed->cover_photo_path = $path;
            $ed->cover_photo_disk = 'public';
        }

        $ed->save();

        return redirect()->route('admin.editions.edit', $ed)->with('ok','Edição criada.');
    }

    public function edit(Edition $edition)
    {
        return view('admin.editions.edit', compact('edition'));
    }

    public function update(Request $r, Edition $edition)
    {
        $data = $r->validate([
            'title' => ['required','string','max:160'],
            'slug'  => ['nullable','string','max:180','unique:editions,slug,'.$edition->id],
            'description' => ['nullable','string'],
            'release_date' => ['nullable','date'],
            'published_at' => ['nullable','date'],
            'profile_photo' => ['nullable','image','max:5120'],
            'cover_photo'   => ['nullable','image','max:8192'],
            'remove_profile_photo' => ['nullable','boolean'],
            'remove_cover_photo'   => ['nullable','boolean'],
        ]);

        $edition->title = $data['title'];
        $edition->slug = $data['slug'] ?? $edition->slug;
        $edition->description = $data['description'] ?? null;
        $edition->release_date = $data['release_date'] ?? null;
        $edition->published_at = $data['published_at'] ?? null;

        if ($r->boolean('remove_profile_photo') && $edition->profile_photo_path) {
            Storage::disk($edition->profile_photo_disk ?: 'public')->delete($edition->profile_photo_path);
            $edition->profile_photo_path = null;
        }
        if ($r->boolean('remove_cover_photo') && $edition->cover_photo_path) {
            Storage::disk($edition->cover_photo_disk ?: 'public')->delete($edition->cover_photo_path);
            $edition->cover_photo_path = null;
        }

        if ($r->hasFile('profile_photo')) {
            $path = $r->file('profile_photo')->storePublicly('editions/profile','public');
            $edition->profile_photo_path = $path;
            $edition->profile_photo_disk = 'public';
        }
        if ($r->hasFile('cover_photo')) {
            $path = $r->file('cover_photo')->storePublicly('editions/cover','public');
            $edition->cover_photo_path = $path;
            $edition->cover_photo_disk = 'public';
        }

        $edition->save();

        return back()->with('ok','Edição atualizada.');
    }

    public function destroy(Edition $edition)
    {
        $edition->delete();
        return redirect()->route('admin.editions.index')->with('ok','Edição removida.');
    }
}
