<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [
            ['loc' => url('/'), 'lastmod' => now()->toAtomString(), 'priority' => '1.0'],
            ['loc' => url('/pricing'), 'lastmod' => now()->toAtomString(), 'priority' => '0.8'],
            ['loc' => url('/business'), 'lastmod' => now()->toAtomString(), 'priority' => '0.8'],
            ['loc' => url('/login'), 'lastmod' => now()->toAtomString(), 'priority' => '0.5'],
            ['loc' => url('/register'), 'lastmod' => now()->toAtomString(), 'priority' => '0.5'],
        ];

        // Add dynamic templates if needed
        // $templates = Template::all();
        // foreach ($templates as $t) { ... }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . $url['loc'] . '</loc>';
            $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }
        $xml .= '</urlset>';

        return Response::make($xml, 200, ['Content-Type' => 'text/xml']);
    }
}
