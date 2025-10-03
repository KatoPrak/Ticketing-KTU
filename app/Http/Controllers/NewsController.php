<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('category')->latest()->paginate(10);
        return view('it.news.index', compact('news'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('it.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'message' => 'required|string'
        ]);

        News::create($request->all());

        return redirect()->route('news.index')
            ->with('success', 'News berhasil ditambahkan!');
    }

    // public function show(News $news)
    // {
    //     return view('it.news.show', compact('news'));
    // }

    public function edit(News $news)
    {
        $categories = Category::all();
        return view('it.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'message' => 'required|string'
        ]);

        $news->update($request->all());

        return redirect()->route('news.index')
            ->with('success', 'News berhasil diupdate!');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('news.index')
            ->with('success', 'News berhasil dihapus!');
    }
}
