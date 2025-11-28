<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArtigoSeeder extends Seeder
{
    /**
     * Insere dados de teste na tabela 'artigos'.
     *
     * @return void
     */
    public function run()
    {
        // Limpa a tabela antes de inserir novos dados para evitar duplicidade em testes.
        DB::table('artigos')->truncate();

        $artigos = [
            [
                'titulo' => 'Aplicações de Machine Learning no Diagnóstico Precoce de Doenças Cardiovasculares',
                'autores' => 'Dr. Marina Silva, Prof. Carlos Andrade',
                'categoria' => 'Saúde',
                'data_publicacao' => Carbon::parse('2025-03-15'),
                'tempo_leitura' => '12 min',
                'imagem' => 'saude.jpg',
                'conteudo' => 'O aprendizado de máquina (Machine Learning) está revolucionando a medicina com a capacidade de processar grandes volumes de dados de pacientes e identificar padrões que podem indicar a presença de doenças cardíacas muito antes dos métodos tradicionais. A pesquisa foca na criação de modelos preditivos mais precisos.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Sustentabilidade Urbana: Análise de Sistemas de Captação de Água Pluvial em Metrópoles',
                'autores' => 'Eng. Rafael Costa, Dra. Ana Oliveira',
                'categoria' => 'Engenharia',
                'data_publicacao' => Carbon::parse('2025-03-12'),
                'tempo_leitura' => '15 min',
                'imagem' => 'engenharia.jpg',
                'conteudo' => 'A crescente densidade populacional exige novas soluções de gestão hídrica nas grandes cidades. Este artigo detalha a eficácia de sistemas de captação e reuso de água da chuva (água pluvial) como uma ferramenta crítica para aumentar a resiliência hídrica urbana e diminuir a pressão sobre os recursos hídricos tradicionais.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Inteligência Artificial na Educação: Personalização do Aprendizado através de Sistemas Adaptativos',
                'autores' => 'Prof. Lucas Martins, Dra. Beatriz Santos',
                'categoria' => 'Educação',
                'data_publicacao' => Carbon::parse('2025-03-08'),
                'tempo_leitura' => '10 min',
                'imagem' => 'educacao.jpg',
                'conteudo' => 'A Inteligência Artificial promete transformar a sala de aula, adaptando o conteúdo e o ritmo de ensino às necessidades individuais de cada aluno. Discutimos a implementação e os desafios éticos de sistemas de aprendizado adaptativos que usam a IA para monitorar o progresso e oferecer intervenções personalizadas.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ⭐ NOVO ARTIGO ADICIONADO
            [
                'titulo' => 'Tecnologias Emergentes no Agronegócio: O Uso de Drones e Sensores Inteligentes',
                'autores' => 'Dr. Felipe Nunes, Eng. Larissa Prado',
                'categoria' => 'Agronegócio',
                'data_publicacao' => Carbon::parse('2025-03-20'),
                'tempo_leitura' => '8 min',
                'imagem' => 'agro.jpg',
                'conteudo' => 'O uso de drones e sensores inteligentes está revolucionando o agronegócio, permitindo o monitoramento avançado do solo, identificação precoce de pragas e otimização da irrigação. Este artigo apresenta estudos recentes e aplicações práticas dessas tecnologias no campo.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('artigos')->insert($artigos);
    }
}
