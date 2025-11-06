<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionSection extends Model
{
    protected $fillable = [
        'submission_id','parent_id','position','title','content',
        'level','numbering','show_in_toc','show_number',
    ];

    protected $casts = [
        'show_in_toc' => 'boolean',
        'show_number' => 'boolean',
    ];

    // 🚀 expõe "is_filled" para usar direto no Blade
    protected $appends = ['is_filled'];

    protected $touches = ['submission'];

    public function submission(){ return $this->belongsTo(Submission::class); }
    public function parent(){ return $this->belongsTo(self::class, 'parent_id'); }
    public function children(){ return $this->hasMany(self::class, 'parent_id')->orderBy('position'); }
    public function assets(){ return $this->hasMany(SubmissionAsset::class, 'section_id')->orderBy('order'); }

    public function scopeRoots($q){ return $q->whereNull('parent_id')->orderBy('position'); }

    /** --- Helpers de preenchimento --- */

    // mínimo de caracteres “reais” (sem HTML, sem espaços extras) para considerar preenchida
    public const MIN_TEXT_LEN = 20;

    protected function plainTextLen(?string $html): int
    {
        if (!$html) return 0;
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        return mb_strlen($plain);
    }

    protected function hasOwnContentOrAssets(): bool
    {
        if ($this->plainTextLen($this->content) >= self::MIN_TEXT_LEN) return true;
        return $this->relationLoaded('assets')
            ? $this->assets->isNotEmpty()
            : $this->assets()->exists();
    }

    protected function childFilled(): bool
    {
        if ($this->relationLoaded('children')) {
            foreach ($this->children as $c) {
                if ($c->is_filled) return true; // usa o accessor recursivo
            }
            return false;
        }
        // fallback sem eager load (consulta leve)
        return $this->children()->where(function($q){
            $q->whereNotNull('content');
        })->orWhereHas('assets')->exists();
    }

    public function getIsFilledAttribute(): bool
    {
        return $this->hasOwnContentOrAssets() || $this->childFilled();
    }
}
