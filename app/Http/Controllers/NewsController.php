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
        $locations = \App\Models\Location::all();
        return view('it.news.create', compact('locations'));
    }

    /**
     * CREATE: Menyimpan news baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:10',
            'location_id' => 'nullable|exists:locations,id'
        ]);

        News::create($request->only('message', 'location_id'));

        return redirect()->route('it.news.index')->with('success', 'News berhasil ditambahkan!');
    }

    /**
     * UPDATE: Mengupdate news yang sudah ada.
     * Laravel otomatis inject News model berdasarkan {news} di route
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'message' => 'required|string|min:10',
            'location_id' => 'nullable|exists:locations,id'
        ]);

        $news->update($request->only('message', 'location_id'));

        return redirect()->route('it.news.index')->with('success', 'News berhasil diupdate!');
    }

    /**
     * DELETE: Menghapus data news.
     * Laravel otomatis inject News model berdasarkan {news} di route
     */
    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('it.news.index')->with('success', 'News berhasil dihapus!');
    }
}