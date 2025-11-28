<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artigo extends Model
{
    use HasFactory;

    /**
     * Define a lista de atributos que podem ser preenchidos em massa (mass assignable).
     * É CRUCIAL que todos os campos que você usa no seu formulário de submissão
     * e que são preenchidos por código estejam nesta lista.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'titulo',
    'autores',
    'categoria',
    'data_publicacao',
    'tempo_leitura',
    'imagem',
    'arquivo_pdf',
    'conteudo',
];


    /**
     * Define a conversão de tipos para atributos específicos.
     * Garante que 'data_publicacao' seja sempre um objeto Carbon/DateTime.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_publicacao' => 'datetime',
    ];

    // Se no futuro você tiver um relacionamento (ex: um artigo pertence a um Usuário),
    // ele seria definido aqui. Exemplo:
    /*
    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    */
}