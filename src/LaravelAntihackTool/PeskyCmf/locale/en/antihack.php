<?php

return [
    'hack_attempts' => [
        'menu_title' => 'Hack attempts',
        'datagrid' => [
            'header' => 'Hack attempts',
            'column' => [
                'id' => 'ID',
                'ip' => 'IP address',
                'user_agent' => 'User agent',
                'created_at' => 'Created',
            ],
            'filter' => [
                'cmf_hack_attempts' => [
                    'id' => 'ID',
                    'ip' => 'IP address',
                    'user_agent' => 'User agent',
                    'created_at' => 'Created',
                ]
            ]
        ],
        'form' => [
            'header_create' => 'Adding hack attempt',
            'header_edit' => 'Editing hack attempt',
            'input' => [
                'id' => 'ID',
                'ip' => 'IP address',
                'user_agent' => 'User agent',
                'created_at' => 'Created',
            ],
        ],
        'item_details' => [
            'header' => 'Hack attempt details',
            'field' => [
                'id' => 'ID',
                'ip' => 'IP address',
                'user_agent' => 'User agent',
                'created_at' => 'Created',
            ]
        ]
    ]
];