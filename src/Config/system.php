<?php

return [
    [
        'key' => 'sales.carriers.vandersonramos_fmtransportes',
        'name' => 'FM Transportes',
        'sort' => 1,
        'fields' => [
            [
                'name' => 'active',
                'title' => 'admin::app.admin.system.status',
                'type'  => 'boolean',
                'options' => [
                    [
                        'title' => 'Sim',
                        'value' => true
                    ], [
                        'title' => 'Não',
                        'value' => false
                    ]
                ],
                'validation' => 'required'
            ],
            [
                'name' => 'environment',
                'title' => 'Ambiente',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Ambiente de testes / Homologação',
                        'value' => 'sandbox'
                    ], [
                        'title' => 'Ambiente de Produção',
                        'value' => 'live'
                    ]
                ],
            ],
            [
                'name' => 'dimension_type',
                'title' => 'Tipo de dimensão',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Centímetros',
                        'value' => 'cm'
                    ], [
                        'title' => 'Metros',
                        'value' => 'm'
                    ]
                ],
            ],
            [
                'name' => 'weight_type',
                'title' => 'Formato de peso',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Kilos',
                        'value' => 'kg'
                    ], [
                        'title' => 'Gramas',
                        'value' => 'gr'
                    ]
                ],
            ],
            [
                'name' => 'title',
                'title' => 'Nome do método de entrega',
                'type' => 'text',
                'validation' => 'required',
            ],
            [
                'name' => 'login',
                'title' => 'Login',
                'type' => 'text',
            ],
            [
                'name' => 'password',
                'title' => 'Senha',
                'type' => 'text',
            ],
            [
                'name' => 'active_standard',
                'title' => 'Ativar Serviço Standard',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Sim',
                        'value' => true
                    ], [
                        'title' => 'Não',
                        'value' => false
                    ]
                ],
            ],
            [
                'name' => 'standard_client_code',
                'title' => 'Código do Cliente para o serviço Standard',
                'type' => 'text',
            ],
            [
                'name' => 'active_express',
                'title' => 'Ativar Serviço Express',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Sim',
                        'value' => true
                    ], [
                        'title' => 'Não',
                        'value' => false
                    ]
                ],
            ],
            [
                'name' => 'express_client_code',
                'title' => 'Código do Cliente para o serviço Express',
                'type' => 'text',
            ],
            [
                'name' => 'active_rodo',
                'title' => 'Ativar Serviço Rodo',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Sim',
                        'value' => true
                    ], [
                        'title' => 'Não',
                        'value' => false
                    ]
                ],
            ],
            [
                'name' => 'rodo_client_code',
                'title' => 'Código do Cliente para o serviço Rodo',
                'type' => 'text',
            ],
        ]
    ]
];