<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Branding & defaults
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk meta tag, Open Graph, Twitter Card, dan JSON-LD.
    | Pastikan APP_URL di production sesuai domain kanonis (https, tanpa trailing slash).
    |
    */
    'site_name' => env('SEO_SITE_NAME', env('APP_NAME', 'DForm')),

    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Platform manajemen acara dan formulir pendaftaran digital — kelola event, pendaftaran, QR absensi, dan pelaporan dalam satu tempat.'
    ),

    /** Locale BCP47 untuk og:locale, e.g. id_ID */
    'locale' => env('SEO_LOCALE', 'id_ID'),

    /** Kode bahasa untuk attribute <html lang=""> */
    'html_lang' => env('SEO_HTML_LANG', 'id'),

    /**
     * Path (relatif public/) untuk og:image dan twitter:image default.
     * Contoh: images/logo.webp
     */
    'default_og_image' => env('SEO_DEFAULT_OG_IMAGE', 'images/logo.webp'),

    /** @var string|null Twitter @handle tanpa @ */
    'twitter_site' => env('SEO_TWITTER_SITE'),

    'twitter_creator' => env('SEO_TWITTER_CREATOR'),

    /**
     * Pratinjau: warna theme untuk mobile browser chrome (meta theme-color).
     */
    'theme_color' => env('SEO_THEME_COLOR', '#0f172a'),

    /**
     * Verifikasi search console (opsional). Nilai diisi hanya meta content-nya.
     * Contoh env: SEO_GOOGLE_SITE_VERIFICATION=xxxx
     */
    'google_site_verification' => env('SEO_GOOGLE_SITE_VERIFICATION'),
    'bing_site_verification' => env('SEO_BING_SITE_VERIFICATION'),
    'yandex_verification' => env('SEO_YANDEX_VERIFICATION'),

    /**
     * Path prefix yang tidak boleh diindeks oleh mesin pencari (selain flag via middleware).
     * Digunakan di generator robots.txt — selaras dengan AddRobotsTagForPrivateAreas.
     */
    'disallow_paths' => [
        '/admin',
        '/user',
        '/dashboard',
        '/auth',
        '/info',
    ],

];
