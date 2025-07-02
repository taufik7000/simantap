<?php

namespace App\Http\Controllers;

use App\Models\WebsiteContent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    /**
     * Menampilkan halaman beranda
     */
    public function home(): View
    {
        $content = WebsiteContent::getByPageKey('homepage');
        
        if (!$content) {
            abort(404, 'Halaman beranda tidak ditemukan');
        }

        return view('website.home', compact('content'));
    }

    /**
     * Menampilkan halaman berdasarkan slug
     */
    public function showPage(string $slug): View
    {
        $content = WebsiteContent::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Tentukan view berdasarkan page_key
        $viewName = match($content->page_key) {
            'homepage' => 'website.home',
            'about' => 'website.about',
            'contact' => 'website.contact',
            default => 'website.page'
        };

        return view($viewName, compact('content'));
    }

    /**
     * API endpoint untuk mendapatkan konten
     */
    public function getContent(string $pageKey)
    {
        $content = WebsiteContent::getByPageKey($pageKey);
        
        if (!$content) {
            return response()->json(['error' => 'Content not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $content->title,
                'content' => $content->content,
                'meta_title' => $content->meta_title,
                'meta_description' => $content->meta_description,
                'featured_image' => $content->featured_image,
                'updated_at' => $content->updated_at,
            ]
        ]);
    }

    /**
     * Menampilkan sitemap
     */
    public function sitemap()
    {
        $pages = WebsiteContent::published()->ordered()->get();

        return response()->view('website.sitemap', compact('pages'))
            ->header('Content-Type', 'text/xml');
    }
}