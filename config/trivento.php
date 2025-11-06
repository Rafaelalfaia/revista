<?php

return [
    'submission' => [
        'non_numbered' => [
            'agradecimentos','referências','referencias','apêndices','apendices','anexos'
        ],

        'blueprints' => [
            'artigo_original' => [
                ['title' => 'Introdução'],
                ['title' => 'Métodos'],
                ['title' => 'Resultados'],
                ['title' => 'Discussão'],
                ['title' => 'Conclusões'],
                ['title' => 'Agradecimentos', 'show_number' => false],
                ['title' => 'Referências',   'show_number' => false],
            ],
            'comunicacao_breve' => [
                ['title' => 'Introdução/Objetivo'],
                ['title' => 'Método resumido'],
                ['title' => 'Achado principal'],
                ['title' => 'Implicações'],
                ['title' => 'Referências', 'show_number' => false],
            ],
            'revisao_narrativa' => [
                ['title' => 'Introdução'],
                ['title' => 'Estado da Arte'],
                ['title' => 'Síntese Crítica'],
                ['title' => 'Lacunas e Agenda'],
                ['title' => 'Conclusões'],
                ['title' => 'Referências', 'show_number' => false],
            ],
            'revisao_sistematica' => [
                ['title' => 'Introdução'],
                ['title' => 'Métodos (protocolo/estratégia de busca)'],
                ['title' => 'Resultados'],
                ['title' => 'Discussão'],
                ['title' => 'Conclusões'],
                ['title' => 'Referências', 'show_number' => false],
            ],
            'relato_caso' => [
                ['title' => 'Introdução'],
                ['title' => 'Caso/Contexto'],
                ['title' => 'Procedimentos/Intervenção'],
                ['title' => 'Achados'],
                ['title' => 'Discussão'],
                ['title' => 'Considerações finais'],
                ['title' => 'Agradecimentos', 'show_number' => false],
                ['title' => 'Referências',    'show_number' => false],
            ],
            'relato_tecnico' => [
                ['title' => 'Introdução/Contexto'],
                ['title' => 'Objetivos'],
                ['title' => 'Procedimentos/Implementação'],
                ['title' => 'Resultados/Indicadores'],
                ['title' => 'Lições Aprendidas'],
                ['title' => 'Conclusões/Recomendações'],
                ['title' => 'Referências', 'show_number' => false],
            ],
        ],
    ],
];
