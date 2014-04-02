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
        'files_dir' => 'files',
        'gcs_bucket_name' => 'premium-modem-518.appspot.com',
        'suffix' => '.txt',
        'upload' => array(
            'allowed_extensions' => array(
                'jpg',
                'jpeg',
                'gif',
                'png',
                'doc',
                'docx',
                'xls',
                'xlsx',
                'pdf',
                'txt',
            ),
            'allowed_types' => array(
                '',
            ),
        ),
    ),
);