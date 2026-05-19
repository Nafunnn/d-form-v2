<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsTxtController extends Controller
{
    public function __invoke(): Response
    {
        $base = rtrim((string) config('app.url'), '/');
        $sitemap = $base.'/sitemap.xml';

        $disallowed = config('seo.disallow_paths', []);

        $lines = [
            '# https://www.robotstxt.org/',
            'User-agent: *',
        ];

        foreach ($disallowed as $path) {
            $path = (string) $path;
            if ($path === '') {
                continue;
            }
            $lines[] = 'Disallow: '.rtrim($path, '/');
        }

        $lines[] = '';
        $lines[] = 'User-agent: Googlebot';
        $lines[] = 'Allow: /';
        foreach ($disallowed as $path) {
            $path = (string) $path;
            if ($path === '') {
                continue;
            }
            $lines[] = 'Disallow: '.rtrim($path, '/');
        }

        $lines[] = '';
        $lines[] = 'User-agent: Googlebot-Image';
        $lines[] = 'Allow: /';
        foreach ($disallowed as $path) {
            $path = (string) $path;
            if ($path === '') {
                continue;
            }
            $lines[] = 'Disallow: '.rtrim($path, '/');
        }

        $lines[] = '';
        $lines[] = 'User-agent: Bingbot';
        $lines[] = 'Allow: /';
        foreach ($disallowed as $path) {
            $path = (string) $path;
            if ($path === '') {
                continue;
            }
            $lines[] = 'Disallow: '.rtrim($path, '/');
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.$sitemap;
        $lines[] = '';

        $body = implode("\n", $lines);

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
