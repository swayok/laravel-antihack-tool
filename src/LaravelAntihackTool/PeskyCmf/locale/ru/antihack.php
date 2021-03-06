<?php

return [
    'hack_attempts' => [
        'menu_title' => 'Попытки взлома',
        'reason' => [
            'Bad User Agent' => 'Плохой User Agent',
            'Invalid IP address' => 'Невалидный IP адрес',
            'Attempt to access PHP file directly' => 'Попытка прямого доступа к PHP файлу',
            'Bad URL address' => 'Плохой URL адрес',
            'Bad URL Query data' => 'Плохие данные в URL',
            'Bad POST data' => 'Плохие данные в POST',
        ],
        'datagrid' => [
            'header' => 'Попытки взлома',
            'toolbar' => [
                'show_blacklist' => 'Черный список'
            ],
            'column' => [
                'id' => 'ID',
                'ip' => 'IP адрес',
                'user_agent' => 'Браузер',
                'created_at' => 'Cовершена',
                'reason' => 'Причина',
                'extra' => 'Доп. Данные',
            ],
            'filter' => [
                'cmf_hack_attempts' => [
                    'id' => 'ID',
                    'ip' => 'IP адрес',
                    'user_agent' => 'Браузер',
                    'created_at' => 'Cовершена',
                    'reason' => 'Причина',
                    'extra' => 'Доп. Данные',
                ]
            ]
        ],
        'form' => [
            'header_create' => 'Добавление попытки взлома',
            'header_edit' => 'Редактирование попытки взлома',
            'input' => [
                'id' => 'ID',
                'ip' => 'IP адрес',
                'user_agent' => 'Браузер',
                'created_at' => 'Cовершена',
                'reason' => 'Причина',
                'extra' => 'Доп. Данные',
            ],
        ],
        'item_details' => [
            'header' => 'Информация о попытке взлома',
            'field' => [
                'id' => 'ID',
                'ip' => 'IP адрес',
                'user_agent' => 'Браузер',
                'created_at' => 'Cовершена',
                'reason' => 'Причина',
                'extra' => 'Доп. Данные',
            ]
        ],
        'blacklist_page' => [
            'page_title' => 'Черный список IP адресов и браузеров',
            'whitelisted_ips' => 'Белый список IP адресов',
            'blacklisted_ips_in_config' => 'Постоянно заблокированные IP адреса',
            'blacklisted_ips' => 'Текущий черный список IP адресов',
            'blacklisted_user_agents' => 'Черный список браузеров (RegExp-выражения)',
        ],
    ],
    'error_page' => [
        'back_to_home_page' => 'Вернуться на главную',
        '406' => [
            'page_title' => 'Обнаружена угроза безопасности сайта',
            'text' => 'Запрос не может быть обработан из-за вероятности вредоносного кода, переданного через него. Повторные попытки отослать аналогичные запросы могут привести к блокировке вашего IP адреса',
        ],
        '423' => [
            'page_title' => 'Доступ к сайту заблокирован',
            'text' => 'Ваш IP адрес был заблокирован за большое количество вредоносных запросов. Если вы считаете что эта блокировка ошибочна - обратитесь к администрации сайта',
        ],
    ],
];