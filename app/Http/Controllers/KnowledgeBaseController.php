<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseCategory;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
public function index(Request $request)
{
    $categories = KnowledgeBaseCategory::withCount(['knowledgeBases' => function ($query) {
        $query->where('status', 'published');
    }])->get();
    
    $selectedCategory = null;

    // Mulai query dasar
    $query = KnowledgeBase::where('status', 'published');

    // Filter berdasarkan kategori jika ada
    if ($request->has('kategori') && !empty($request->kategori)) {
        $selectedCategory = KnowledgeBaseCategory::where('slug', $request->kategori)->firstOrFail();
        $query->where('knowledge_base_category_id', $selectedCategory->id);
    }

    // --- AWAL FITUR PENCARIAN ---
    // Filter berdasarkan kata kunci pencarian jika ada
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('content', 'like', '%' . $search . '%');
        });
    }
    // --- AKHIR FITUR PENCARIAN ---

    $articles = $query->with('category')->latest()->paginate(10);
        
    return view('kb.index', compact('articles', 'categories', 'selectedCategory'));
}

    public function show($slug)
    {
        $article = KnowledgeBase::where('slug', $slug)
            ->where('status', 'published')
            ->with('category', 'user')
            ->firstOrFail();

        return view('kb.show', compact('article'));
    }
}