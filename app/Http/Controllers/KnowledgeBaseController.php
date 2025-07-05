<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    // Menampilkan daftar semua artikel yang sudah di-publish
    public function index()
    {
        $articles = KnowledgeBase::where('status', 'published')
            ->latest()
            ->paginate(10);
            
        return view('kb.index', compact('articles'));
    }

    // Menampilkan satu artikel berdasarkan slug-nya
    public function show($slug)
    {
        $article = KnowledgeBase::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail(); 
            
        return view('kb.show', compact('article'));
    }
}