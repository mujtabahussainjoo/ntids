<?php
return [
    'backend' => [
        'frontName' => 'ni_admin'
    ],
    'crypt' => [
        'key' => 'cb94b829fc5dd9b5eae10db479b31968'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'devlopment-cluster.cluster-cruxv62u21s2.ap-southeast-2.rds.amazonaws.com',
                'dbname' => 'kenny',
                'username' => 'neatideas',
                'password' => 'l3t5g0l1v4321',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'translate' => 1,
        'config_webservice' => 1,
        'compiled_config' => 1,
        'vertex' => 1,
        'gigyaim_fieldmapping_cache' => 1
    ],
    'install' => [
        'date' => 'Mon, 25 Jun 2018 10:03:37 +0000'
    ]
];
