<?php

return array(
    'locale' => array(
        'language' => 'en',
    ),
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
        'files_dir' => 'uploads',
        'gcs_bucket_name' => 'premium-modem-518.appspot.com',
        'suffix' => '.txt',
        'upload' => array(            
            'images' => array(
                'allowed_extensions' => array(
                    'jpg',
                    'jpeg',
                    'gif',
                    'png',
                ),
                'allowed_types' => array(
                    '',
                ),
            ),
            'files' => array(
                'allowed_extensions' => array(
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
    ),
);