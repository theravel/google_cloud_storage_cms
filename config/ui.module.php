<?php

return array(
    'layout' => array(
        'title' => 'Test CMS',
        'name'  => 'Project name',
        'footer_text'  => '&copy; Company 2014',
    ),
    'engine_storage' => array(
        'type' => 'auto',
        'models' => array(
            'namespace' => '\App\Models',
        ),
        'data_dir' => 'data',
        'files_dir' => 'files',
        'gcs_bucket_name' => 'premium-modem-518.appspot.com',
        'suffix' => '.txt',
    ),
);