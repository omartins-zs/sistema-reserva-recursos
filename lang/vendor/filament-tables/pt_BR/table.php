<?php

return [
    'column_manager' => [
        'heading' => 'Colunas',
        'actions' => [
            'apply' => [
                'label' => 'Aplicar colunas',
            ],
            'reset' => [
                'label' => 'Redefinir',
            ],
        ],
    ],
    'columns' => [
        'actions' => [
            'label' => 'Acao|Acoes',
        ],
        'select' => [
            'loading_message' => 'Carregando...',
            'no_options_message' => 'Nenhuma opcao disponivel.',
            'no_search_results_message' => 'Nenhuma opcao encontrada.',
            'placeholder' => 'Selecione uma opcao',
            'searching_message' => 'Buscando...',
            'search_prompt' => 'Digite para buscar...',
        ],
        'text' => [
            'actions' => [
                'collapse_list' => 'Mostrar :count a menos',
                'expand_list' => 'Mostrar :count a mais',
            ],
            'more_list_items' => 'e mais :count',
        ],
    ],
    'fields' => [
        'search' => [
            'label' => 'Buscar',
            'placeholder' => 'Buscar',
            'indicator' => 'Busca',
        ],
    ],
    'actions' => [
        'filter' => [
            'label' => 'Filtrar',
        ],
        'group' => [
            'label' => 'Agrupar',
        ],
        'open_bulk_actions' => [
            'label' => 'Acoes em lote',
        ],
        'column_manager' => [
            'label' => 'Gerenciar colunas',
        ],
    ],
    'empty' => [
        'heading' => 'Nenhum :model',
        'description' => 'Crie um :model para comecar.',
    ],
    'filters' => [
        'actions' => [
            'apply' => [
                'label' => 'Aplicar filtros',
            ],
            'remove' => [
                'label' => 'Remover filtro',
            ],
            'remove_all' => [
                'label' => 'Limpar filtros',
                'tooltip' => 'Limpar filtros',
            ],
            'reset' => [
                'label' => 'Redefinir',
            ],
        ],
        'heading' => 'Filtros',
        'indicator' => 'Filtros ativos',
        'multi_select' => [
            'placeholder' => 'Todos',
        ],
        'select' => [
            'placeholder' => 'Todos',
            'relationship' => [
                'empty_option_label' => 'Nenhum',
            ],
        ],
    ],
    'selection_indicator' => [
        'selected_count' => '1 registro selecionado|:count registros selecionados',
        'actions' => [
            'select_all' => [
                'label' => 'Selecionar todos os :count',
            ],
            'deselect_all' => [
                'label' => 'Limpar selecao',
            ],
        ],
    ],
    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'Ordenar por',
            ],
            'direction' => [
                'label' => 'Direcao',
                'options' => [
                    'asc' => 'Crescente',
                    'desc' => 'Decrescente',
                ],
            ],
        ],
    ],
    'default_model_label' => 'registro',
];
