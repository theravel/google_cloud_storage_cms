<?php

return array(
    'layout' => array(
        'title' => 'Test CMS',
        'name'  => 'Project name',
    ),
    'engine_storage' => array(
        'type' => 'auto',
        'models' => array(
            'namespace' => '\App\Models',
        ),
        'data_dir' => 'data',
        'gcs_bucket_name' => 'premium-modem-518.appspot.com',
        'suffix' => '.txt',
    ),
);