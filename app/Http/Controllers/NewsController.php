<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * READ: Menampilkan semua data news.
     */
    public function index(Request $request)
    {
        $news = News::latest()->get();
        return view('it.news.index', compact('news'));
    }

    /**
     * Menampilkan form untuk membuat news baru.
     */
    public function create()
    {
        return view('it.news.create');
    }

    /**
     * CREATE: Menyimpan news baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:10'
        ]);

        News::create($request->only('message'));

        return redirect()->route('it.news.index')->with('success', 'News berhasil ditambahkan!');
    }
    /**
     * DELETE: Menghapus data news.
     */
public function destroy(News $news)
{
    // Laravel sudah otomatis menemukan data 'news' berdasarkan ID dari URL.
    // Kita tinggal panggil delete().
    $news->delete();

    return redirect()->route('it.news.index')->with('success', 'News berhasil dihapus!');
}
}
