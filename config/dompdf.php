<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,   // Throw an Exception on warnings from dompdf
    'orientation' => 'portrait',
    'defines' => [
        'font_dir' => storage_path('fonts/'), // Change this if you want to override the default font folder
        'font_cache' => storage_path('fonts/'), // Change this if you want to override the default font cache folder
        'temp_dir' => storage_path('temp/'), // Change this if you want to override the default temporary folder
        'chroot' => base_path(), // Change this to the base path you want to allow
        'enable_font_subsetting' => false,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_font' => 'serif',
        'dpi' => 96,
        'enable_php' => true,
        'enable_javascript' => true,
        'enable_remote' => true, // Enable remote assets
        'log_output_file' => null,
        'font_height_ratio' => 1.1,
        'is_html5_parser_enabled' => true,
        'is_font_subsetting_enabled' => false,
        'debug_png' => false,
        'debug_keep_temp' => false,
        'debug_css' => false,
        'debug_layout' => false,
        'debug_layout_links' => false,
        'debug_layout_blocks' => false,
        'debug_layout_inline' => false,
        'debug_layout_padding_box' => false,
    ],
];
