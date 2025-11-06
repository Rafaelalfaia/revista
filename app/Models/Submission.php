<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Submission extends Model
{

    public const ST_DRAFT     = 'rascunho';
    public const ST_SUBMITTED = 'submetido';
    public const ST_SCREEN    = 'em_triagem';
    public const ST_REVIEW    = 'em_revisao';
    public const ST_REV_REQ   = 'revisao_solicitada';
    public const ST_ACCEPTED  = 'aceito';
    public const ST_REJECTED  = 'rejeitado';
    public const ST_PUBLISHED = 'publicado';

    public const STATUS_ORDER = [
        self::ST_DRAFT,
        self::ST_SUBMITTED,
        self::ST_SCREEN,
        self::ST_REVIEW,
        self::ST_REV_REQ,
        self::ST_ACCEPTED,
        self::ST_PUBLISHED,
        self::ST_REJECTED,
    ];

    public const TP_ORIGINAL   = 'artigo_original';
    public const TP_BRIEF      = 'comunicacao_breve';
    public const TP_REV_NARR   = 'revisao_narrativa';
    public const TP_REV_SIST   = 'revisao_sistematica';
    public const TP_CASE       = 'relato_caso';
    public const TP_TECH       = 'relato_tecnico';

    public const TYPE_LABELS = [
        self::TP_ORIGINAL => 'Artigo original',
        self::TP_BRIEF    => 'Comunicação breve',
        self::TP_REV_NARR => 'Revisão narrativa',
        self::TP_REV_SIST => 'Revisão sistemática',
        self::TP_CASE     => 'Estudo/Relato de caso',
        self::TP_TECH     => 'Relato técnico/experiência',
    ];

    public const STATUS_LABELS = [
        self::ST_DRAFT     => 'Rascunho',
        self::ST_SUBMITTED => 'Submetido',
        self::ST_SCREEN    => 'Em triagem',
        self::ST_REVIEW    => 'Em revisão',
        self::ST_REV_REQ   => 'Revisão solicitada',
        self::ST_ACCEPTED  => 'Aceito',
        self::ST_REJECTED  => 'Rejeitado',
        self::ST_PUBLISHED => 'Publicado',
    ];


    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'abstract',
        'language',
        'keywords',
        'meta',
        'tipo_trabalho',
        'status',
        'doi',
        'numbering_config',
        'pagination_config',
        'submitted_at',
        'triaged_at',
        'accepted_at',
        'published_at',
    ];

    protected $casts = [
        'keywords'          => 'array',
        'meta'              => 'array',
        'numbering_config'  => 'array',
        'pagination_config' => 'array',
        'submitted_at'      => 'datetime',
        'triaged_at'        => 'datetime',
        'accepted_at'       => 'datetime',
        'published_at'      => 'datetime',
    ];

    protected $appends = [
        'type_label',
        'status_label',
        'doi_url',
    ];


    protected static function booted(): void
    {
        static::creating(function (Submission $m) {
            // slug
            if (blank($m->slug)) {
                $m->slug = static::generateUniqueSlug($m->title);
            }

            // defaults de numeração/paginação se não enviados
            if (blank($m->numbering_config)) {
                $m->numbering_config = static::defaultNumberingConfig();
            }
            if (blank($m->pagination_config)) {
                $m->pagination_config = static::defaultPaginationConfig();
            }
        });

        static::updating(function (Submission $m) {
            // Atualiza slug se título mudou e slug não foi fixado manualmente
            if ($m->isDirty('title') && $m->getOriginal('slug') === $m->slug) {
                $m->slug = static::generateUniqueSlug($m->title, $m->id);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        // pivot padrão: category_submission (ordem alfabética)
        return $this->belongsToMany(Category::class, 'category_submission')
                    ->withTimestamps()
                    ->withPivot('is_primary');
    }

    /** Helper para sincronizar seleção e “principal”. */
    public function syncCategories(array $ids, ?int $primaryId = null): void
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $data = [];
        foreach ($ids as $id) {
            $data[$id] = ['is_primary' => ($primaryId && $primaryId === $id)];
        }
        $this->categories()->sync($data);
    }



    protected static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug(Str::limit($title, 120, ''));
        $slug = $base ?: Str::random(8);

        $i = 1;
        while (
            static::query()
                ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $i++;
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sections()
    {
        return $this->hasMany(SubmissionSection::class);
    }

    public function rootSections()
    {
        return $this->hasMany(SubmissionSection::class)->whereNull('parent_id')->orderBy('position');
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function assets()
    {
        return $this->hasMany(SubmissionAsset::class);
    }

    public function references()
    {
        return $this->hasMany(SubmissionReference::class)->orderBy('order');
    }

    public function metadata()
    {
        return $this->hasOne(SubmissionMetadata::class);
    }

   public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function comments(){ return $this->hasMany(\App\Models\SubmissionComment::class); }
    public function openBlockingComments(){ return $this->comments()->where('level','must_fix')->where('status','open'); }
    public function recomputeStatus(): void
    {
        $hasBlocking = $this->openBlockingComments()->exists();
        if ($hasBlocking && $this->status !== self::ST_REV_REQ) $this->update(['status'=>self::ST_REV_REQ]);
        if (!$hasBlocking && $this->status === self::ST_REV_REQ) $this->update(['status'=>self::ST_REVIEW]);
    }


    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }
    public function assignments() {
    return $this->hasMany(\App\Models\ReviewAssignment::class);
    }


    public function typeLabel(): Attribute
    {
        return Attribute::get(fn () => self::TYPE_LABELS[$this->tipo_trabalho] ?? $this->tipo_trabalho);
    }

    public function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUS_LABELS[$this->status] ?? $this->status);
    }

    public function doiUrl(): Attribute
    {
        return Attribute::get(function () {
            $doi = trim((string) $this->doi);
            if ($doi === '') {
                return null;
            }
            // aceita "10.xxxx/..." ou url completa
            if (Str::startsWith($doi, ['http://', 'https://'])) {
                return $doi;
            }
            return 'https://doi.org/' . $doi;
        });
    }


    public function scopeMine(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', self::ST_PUBLISHED);
    }

    public function scopeStatus(Builder $q, string|array $status): Builder
    {
        $arr = (array) $status;
        return $q->whereIn('status', $arr);
    }

    public function scopeOfType(Builder $q, string|array $types): Builder
    {
        return $q->whereIn('tipo_trabalho', (array) $types);
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') return $q;

        return $q->where(function (Builder $qq) use ($term) {
            $qq->where('title', 'ILIKE', "%{$term}%")
               ->orWhere('abstract', 'ILIKE', "%{$term}%")
               ->orWhereRaw("EXISTS (
                    SELECT 1 FROM jsonb_array_elements_text(keywords) AS kw
                    WHERE kw ILIKE ?
               )", ["%{$term}%"]);
        });
    }

    public function scopeSmartOrder(Builder $q): Builder
    {
        $cases = collect(self::STATUS_ORDER)
            ->map(fn ($st, $i) => "WHEN '{$st}' THEN {$i}")
            ->implode(' ');

        return $q->orderByRaw("CASE status {$cases} ELSE 999 END")
                 ->orderByDesc('updated_at');
    }


    public static function defaultNumberingConfig(): array
    {
        return [
            'style'            => 'decimal',
            'max_depth'        => 3,
            'prefix_root'      => '',
            'include'          => ['Introdução','Métodos','Resultados','Discussão','Conclusões'],
            'exclude'          => ['Agradecimentos','Referências'],
            'restart_per_level'=> false,
            'custom_map'       => [],
        ];
    }

    public static function defaultPaginationConfig(): array
    {
        return [
            'frontmatter' => ['style' => 'roman_lower', 'start' => 1],
            'main'        => ['style' => 'decimal',     'start' => 1],
            'appendix'    => ['style' => 'alpha_upper', 'start' => 1],
            'show_on'     => ['pdf','html'],
            'position'    => 'footer_center',
        ];
    }


    /**
     *
     *
     *
     * @param  iterable<\App\Models\SubmissionSection>  $sections
     * @return array<int, array> cada item: ['id'=>, 'title'=>, 'level'=>, 'computed_numbering'=>, ...]
     */
    public function withComputedNumbering(iterable $sections): array
    {
        $cfg = array_merge(self::defaultNumberingConfig(), $this->numbering_config ?? []);

        $byParent = [];
        foreach ($sections as $s) {
            $byParent[$s->parent_id ?? 0][] = $s;
        }
        foreach ($byParent as &$list) {
            usort($list, fn ($a, $b) => $a->position <=> $b->position);
        }

        $out = [];
        $stack = [];
        $prefixRoot = (string) ($cfg['prefix_root'] ?? '');
        $maxDepth = (int) ($cfg['max_depth'] ?? 3);
        $restartPerLevel = (bool) ($cfg['restart_per_level'] ?? false);
        $exclude = (array) ($cfg['exclude'] ?? []);
        $customMap = (array) ($cfg['custom_map'] ?? []);
        $style = (string) ($cfg['style'] ?? 'decimal');

        $walker = function ($parentId, $level) use (&$walker, &$byParent, &$out, &$stack, $maxDepth, $restartPerLevel, $exclude, $customMap, $style, $prefixRoot) {
            $children = $byParent[$parentId] ?? [];
            if (empty($children)) return;

            if (!isset($stack[$level])) $stack[$level] = 0;
            if ($restartPerLevel) {
                $stack = array_slice($stack, 0, $level + 1, true);
            }

            foreach ($children as $child) {

                $title = trim($child->title);
                $shouldNumber = $child->show_number && !in_array($title, $exclude, true) && $level <= $maxDepth;

                if ($shouldNumber) {
                    $stack[$level] = ($stack[$level] ?? 0) + 1;
                }

                $computed = null;

                if (!$child->show_number) {
                    $computed = null;
                } elseif (Arr::exists($customMap, $title)) {
                    $computed = (string) $customMap[$title];
                } elseif (!blank($child->numbering)) {
                    $computed = (string) $child->numbering;
                } elseif ($shouldNumber) {

                    $parts = [];
                    for ($i = 1; $i <= $level; $i++) {
                        $n = $stack[$i] ?? null;
                        if ($n === null) break;
                        $parts[] = Submission::formatCounter($n, $i === 1 ? $style : 'decimal');
                    }
                    $computed = ($prefixRoot && $level === 1 ? $prefixRoot : '');
                    $computed .= implode('.', $parts);
                }

                $out[] = [
                    'id'                 => $child->id,
                    'parent_id'          => $child->parent_id,
                    'level'              => $child->level,
                    'position'           => $child->position,
                    'title'              => $child->title,
                    'content'            => $child->content,
                    'show_number'        => (bool) $child->show_number,
                    'show_in_toc'        => (bool) $child->show_in_toc,
                    'computed_numbering' => $computed,
                ];

                $walker($child->id, $level + 1);
            }
        };

        $walker(0, 1);

        return $out;
    }

    public static function formatCounter(int $n, string $style = 'decimal'): string
    {
        return match ($style) {
            'roman_lower' => strtolower(static::toRoman($n)),
            'roman_upper' => strtoupper(static::toRoman($n)),
            'alpha_lower' => strtolower(static::toAlpha($n)),
            'alpha_upper' => strtoupper(static::toAlpha($n)),
            default       => (string) $n, // decimal
        };
    }

    protected static function toAlpha(int $n): string
    {
        $result = '';
        while ($n > 0) {
            $n--; // 1-based
            $result = chr(($n % 26) + 65) . $result;
            $n = intdiv($n, 26);
        }
        return $result ?: 'A';
    }

    protected static function toRoman(int $n): string
    {
        $map = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1,
        ];
        $res = '';
        foreach ($map as $roman => $value) {
            while ($n >= $value) {
                $res .= $roman;
                $n -= $value;
            }
        }
        return $res ?: 'I';
    }


    public function isDraft(): bool      { return $this->status === self::ST_DRAFT; }
    public function isSubmitted(): bool  { return $this->status === self::ST_SUBMITTED; }
    public function inScreen(): bool     { return $this->status === self::ST_SCREEN; }
    public function inReview(): bool     { return $this->status === self::ST_REVIEW; }
    public function isRevRequested(): bool { return $this->status === self::ST_REV_REQ; }
    public function isAccepted(): bool   { return $this->status === self::ST_ACCEPTED; }
    public function isRejected(): bool   { return $this->status === self::ST_REJECTED; }
    public function isPublished(): bool  { return $this->status === self::ST_PUBLISHED; }
}
