<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        News::create([
            'title'      => $validated['title'],
            'content'    => $validated['content'],
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('it.dashboard')->with('success', 'News berhasil dibuat!');
    }

    public function index(Request $request)
{
    // Ambil query search dari URL, default kosong jika tidak ada
    $search = $request->query('search', '');

    // Ambil data berita, filter jika ada search
    $news = News::when($search, function($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // supaya pagination tetap membawa query search

    // Kirim data ke view
    return view('it.news.index-news', compact('news', 'search'));
}

    public function create()
    {
        return view('it.news.create');
    }

// Halaman detail berita
    public function show($id)
    {
        $newsItem = News::findOrFail($id);
        return view('it.news.show', compact('newsItem'));
    }

    // Halaman edit berita
public function edit($id)
{
    $newsItem = News::findOrFail($id); // ambil berita berdasarkan id
    return view('it.news.edit', compact('newsItem')); // kirim $newsItem ke blade
}
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $news = News::findOrFail($id);
        $news->update($request->only('title', 'content'));

        return redirect()->route('news.index')->with('success', 'News berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return redirect()->route('news.index')->with('success', 'News berhasil dihapus.');
    }
}