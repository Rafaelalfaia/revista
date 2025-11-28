<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Este método cria a tabela 'artigos' com todas as colunas necessárias,
     * alinhadas com o Artigo.php e o ArtigoSeeder.php.
     */
    public function up(): void
    {
        Schema::create('artigos', function (Blueprint $table) {
            $table->id();
            
            // Colunas alinhadas com o Artigo Model e Seeder
            $table->string('titulo', 255);
            $table->string('autores', 500);
            $table->string('categoria', 100);

            // 💡 CORRIGIDO: Nome da coluna de 'data' para 'data_publicacao'
            $table->timestamp('data_publicacao')->nullable(); 

            // 💡 CORRIGIDO: Nome da coluna de 'tempo' para 'tempo_leitura'
            $table->string('tempo_leitura', 20); // Ex: '12 min'

            $table->string('imagem', 255)->nullable(); // Nome do arquivo de imagem
            
            // 💡 CORRIGIDO: Adicionada a coluna 'conteudo'
            $table->longText('conteudo'); 
            
            // ⚠️ REMOVIDA A COLUNA 'link' pois não é usada no Modelo/Controller.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artigos');
    }
};