<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

/**
 * XML sitemap for search engines.
 *
 * Without this, crawlers only discover the site through internal links and
 * the blog post URLs are effectively invisible to indexing.
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $static = [
            route('landing'),
            route('blog'),
            route('gallery'),
            route('team'),
            route('sponsor'),
            route('supplier'),
            route('rider'),
            route('csr'),
            route('franchise'),
        ];

        // Route names for the informational pages are prefixed "pages.".
        foreach (['privacy', 'terms', 'cookies', 'compliance', 'help', 'faq', 'community', 'affiliate'] as $page) {
            $static[] = route('pages.'.$page);
        }

        foreach ($static as $url) {
            $urls[] = ['loc' => $url, 'changefreq' => 'monthly', 'priority' => '0.6'];
        }

        // The landing page is the primary target and changes most often.
        $urls[0]['changefreq'] = 'weekly';
        $urls[0]['priority'] = '1.0';
        $urls[1]['changefreq'] = 'daily';
        $urls[1]['priority'] = '0.9';

        if (Post::query()->published()->exists()) {
            $posts = Post::query()
                ->published()
                ->orderBy('published_at', 'desc')
                ->get(['slug', 'updated_at']);

            foreach ($posts as $post) {
                $urls[] = [
                    'loc' => route('blog_details', $post->slug),
                    'lastmod' => optional($post->updated_at)->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            }
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
