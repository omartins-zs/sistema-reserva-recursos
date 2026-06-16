<?php

return [
    'single' => [
        'label' => 'Excluir',
        'modal' => [
            'heading' => 'Excluir :label',
            'actions' => [
                'delete' => [
                    'label' => 'Excluir',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'Excluido',
            ],
        ],
    ],
    'multiple' => [
        'label' => 'Excluir selecionados',
        'modal' => [
            'heading' => 'Excluir :label selecionados',
            'actions' => [
                'delete' => [
                    'label' => 'Excluir',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'Exclusao concluida',
            ],
            'deleted_partial' => [
                'title' => 'Excluidos :count de :total',
                'missing_authorization_failure_message' => 'Sem permissao para excluir :count.',
                'missing_processing_failure_message' => ':count registros nao puderam ser excluidos.',
            ],
            'deleted_none' => [
                'title' => 'Falha ao excluir',
                'missing_authorization_failure_message' => 'Sem permissao para excluir :count.',
                'missing_processing_failure_message' => ':count registros nao puderam ser excluidos.',
            ],
        ],
    ],
];
