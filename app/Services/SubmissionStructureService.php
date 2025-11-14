<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\SubmissionSection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SubmissionStructureService
{
    public function bootstrap(Submission $s): void
    {
        if ($s->sections()->count() > 0) {
            $this->renumber($s);
            return;
        }
        $this->applyBlueprint($s, $s->tipo_trabalho, mode: 'create_missing');
    }

    public function syncToType(Submission $s, string $newType, string $mode = 'create_missing'): void
    {
        $this->applyBlueprint($s, $newType, $mode);
    }

    protected function applyBlueprint(Submission $s, string $type, string $mode = 'create_missing'): void
    {
        $blue = config("trivento.submission.blueprints.$type", []);
        $nn   = array_map('mb_strtolower', config('trivento.submission.non_numbered', []));

        $existing = $s->sections()->roots()->orderBy('position')->get();

        $has = function ($sec): bool {
            $plain = trim(preg_replace('/\s+/', ' ', strip_tags((string)$sec->content)));
            return ($plain !== '') || $sec->assets()->exists();
        };

        $dups = $existing
            ->groupBy(fn($sec) => mb_strtolower(trim($sec->title)))
            ->filter(fn($g) => $g->count() > 1);

        foreach ($dups as $group) {
            $keep = $group->sortByDesc(function ($sec) use ($has) {
                return $has($sec) ? 1 : 0;
            })->first();

            foreach ($group as $sec) {
                if ($sec->id === $keep->id) continue;
                if (!$has($sec)) {
                    $sec->delete();
                }
            }
        }

        $existing = $s->sections()->roots()->orderBy('position')->get();

        $map = [];
        foreach ($existing as $sec) {
            $map[mb_strtolower(trim($sec->title))] = $sec;
        }

        $position = 1;
        $keepIds  = [];

        foreach ($blue as $row) {
            $title = trim((string)($row['title'] ?? 'Seção'));
            $key   = mb_strtolower($title);
            $show  = array_key_exists('show_number', $row) ? (bool)$row['show_number'] : !in_array($key, $nn, true);

            if (isset($map[$key])) {
                $sec = $map[$key];
                $sec->update([
                    'position'    => $position++,
                    'level'       => 1,
                    'show_number' => $show,
                ]);
            } else {
                $sec = SubmissionSection::create([
                    'submission_id' => $s->id,
                    'parent_id'     => null,
                    'position'      => $position++,
                    'title'         => $title,
                    'content'       => null,
                    'level'         => 1,
                    'show_number'   => $show,
                    'show_in_toc'   => true,
                ]);
            }
            $keepIds[] = $sec->id;

            foreach ((array)($row['children'] ?? []) as $i => $child) {
                $this->ensureChild($s, $sec, $child, $i + 1, $nn);
            }
        }

        if ($mode === 'hard_reset') {
            foreach ($existing as $sec) {
                if (!in_array($sec->id, $keepIds, true) && !$has($sec)) {
                    $sec->delete();
                }
            }
        }

        $this->renumber($s);
    }

    protected function ensureChild(Submission $s, SubmissionSection $parent, array $child, int $pos, array $nn): void
    {
        $title = trim((string) ($child['title'] ?? 'Seção'));
        $key   = mb_strtolower($title);
        $show  = array_key_exists('show_number', $child) ? (bool)$child['show_number'] : !in_array($key, $nn, true);

        $existing = $parent->children()->where('title', $title)->first();
        if ($existing) {
            $existing->update([
                'position'    => $pos,
                'level'       => $parent->level + 1,
                'show_number' => $show,
            ]);
            $node = $existing;
        } else {
            $node = SubmissionSection::create([
                'submission_id' => $s->id,
                'parent_id'     => $parent->id,
                'position'      => $pos,
                'title'         => $title,
                'content'       => null,
                'level'         => $parent->level + 1,
                'show_number'   => $show,
                'show_in_toc'   => true,
            ]);
        }

        foreach ((array) ($child['children'] ?? []) as $i => $grand) {
            $this->ensureChild($s, $node, $grand, $i + 1, $nn);
        }
    }

    protected function hasContent(\App\Models\SubmissionSection $sec): bool
    {
        $plain = trim(preg_replace('/\s+/',' ', strip_tags((string)$sec->content)));
        return $plain !== '' || $sec->assets()->exists();
    }

    public function countNonEmpty(\App\Models\Submission $s): int
    {
        $sections = \App\Models\SubmissionSection::where('submission_id', $s->id)->withCount('assets')->get();
        $c = 0;
        foreach ($sections as $sec) {
            $plain = trim(preg_replace('/\s+/',' ', strip_tags((string)$sec->content)));
            if ($plain !== '' || $sec->assets_count > 0) $c++;
        }
        return $c;
    }

    public function renumber(Submission $s): void
    {
        $all = SubmissionSection::where('submission_id', $s->id)->orderBy('position')->get()->groupBy('parent_id');

        $walk = function ($parentId, array $prefix = []) use (&$walk, $all) {
            $list = $all[$parentId] ?? collect();
            $i = 0;
            foreach ($list as $sec) {
                $i++;
                $num = array_merge($prefix, [$i]);
                $sec->numbering = implode('.', $num);
                $sec->save();
                $walk($sec->id, $num);
            }
        };

        $walk(null, []);
    }
}
