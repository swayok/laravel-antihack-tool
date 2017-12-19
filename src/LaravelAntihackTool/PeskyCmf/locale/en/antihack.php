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
                'reason' => 'Reason',
                'extra' => 'Extra data',
            ],
            'filter' => [
                'cmf_hack_attempts' => [
                    'id' => 'ID',
                    'ip' => 'IP address',
                    'user_agent' => 'User agent',
                    'created_at' => 'Created',
                    'reason' => 'Reason',
                    'extra' => 'Extra data',
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
                'reason' => 'Reason',
                'extra' => 'Extra data',
            ],
        ],
        'item_details' => [
            'header' => 'Hack attempt details',
            'field' => [
                'id' => 'ID',
                'ip' => 'IP address',
                'user_agent' => 'User agent',
                'created_at' => 'Created',
                'reason' => 'Reason',
                'extra' => 'Extra data',
            ]
        ]
    ],
    'error_page' => [
        'back_to_home_page' => 'Back to home page',
        '406' => [
            'page_title' => 'Server security violation detected',
            'text' => 'Request cannot be accepted due to probability of harmful code in it. Continuous attemts may result in IP ban.',
        ],
        '423' => [
            'page_title' => 'Access to web site was blocked',
            'text' => 'Your IP address was blocked due to excessive amount of malicious requests. If you think you was banned by mistake - contact site administration',
        ],
    ],
];