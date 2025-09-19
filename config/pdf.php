<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PDF Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for PDF generation using Spatie Laravel PDF.
    |
    */

    'chrome_path' => env('PDF_CHROME_PATH', '/usr/bin/google-chrome'),
    
    'chrome_options' => [
        '--headless',
        '--no-sandbox',
        '--disable-gpu',
        '--disable-dev-shm-usage',
        '--disable-web-security',
        '--disable-features=VizDisplayCompositor',
    ],

    'default_options' => [
        'format' => 'a4',
        'margin_top' => '10mm',
        'margin_right' => '10mm',
        'margin_bottom' => '10mm',
        'margin_left' => '10mm',
        'print_background' => true,
        'display_header_footer' => false,
    ],

    'timeout' => 60,
]; 