<?php

namespace App\Http\Controllers\Seo;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        $urls[] = [
            'loc' => URL::to('/'),
            'changefreq' => 'weekly',
            'priority' => '1.0',
            'lastmod' => null,
        ];

        foreach (['/events', '/features', '/docs'] as $path) {
            $urls[] = [
                'loc' => URL::to($path),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'lastmod' => null,
            ];
        }

        $events = Event::query()
            ->where('status', EventStatus::Published)
            ->whereNull('deleted_at')
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at']);

        foreach ($events as $event) {
            $slug = $event->slug;
            if ($slug === null || $slug === '') {
                continue;
            }
            $urls[] = [
                'loc' => URL::to('/events/'.$slug),
                'changefreq' => 'daily',
                'priority' => '0.9',
                'lastmod' => $event->updated_at?->toAtomString(),
            ];
        }

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
