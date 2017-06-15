<?php

return [
    'hack_attempts' => [
        'menu_title' => 'Попытки взлома',
        'datagrid' => [
            'header' => 'Попытки взлома',
            'column' => [
                'id' => 'ID',
                'ip' => 'IP-адрес',
                'user_agent' => 'User agent',
                'created_at' => 'Создана',
            ],
            'filter' => [
                'cmf_hack_attempts' => [
                    'id' => 'ID',
                    'ip' => 'IP-адрес',
                    'user_agent' => 'User agent',
                    'created_at' => 'Создана',
                ]
            ]
        ],
        'form' => [
            'header_create' => 'Добавление попытки взлома',
            'header_edit' => 'Редактирование попытки взлома',
            'input' => [
                'id' => 'ID',
                'ip' => 'IP-адрес',
                'user_agent' => 'User agent',
                'created_at' => 'Создана',
            ],
        ],
        'item_details' => [
            'header' => 'Информация о попытке взлома',
            'field' => [
                'id' => 'ID',
                'ip' => 'IP-адрес',
                'user_agent' => 'User agent',
                'created_at' => 'Создана',
            ]
        ]
    ]
];