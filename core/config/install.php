<?php

return [

    /* Server Requirements */
    'php_version' => '8.2',

    'extensions' => [
        'php' => [
            'cURL',
            'Mbstring',
            'OpenSSL',
            'PDO',
            'pdo_mysql',
            'Fileinfo',
            'JSON',
            'Tokenizer',
            'Ctype',
            'XML',
            'BCMath',
            'GD',
            'zip',
            'iconv'
        ],
        'apache' => [
            'mod_rewrite',
        ],
    ],

    /* File permissions */
    'permissions' => [
        'Files' => [
            'core/.env',
        ],
        'Folders' =>
        [
            'storage/',
            'core/bootstrap/cache',
            'core/lang',
            'core/plugins',
            'core/storage',
            'core/storage/framework',
            'core/storage/framework/cache',
            'core/storage/framework/cache/data',
            'core/storage/framework/sessions',
            'core/storage/framework/views',
            'core/storage/logs',
        ],
    ]
];
