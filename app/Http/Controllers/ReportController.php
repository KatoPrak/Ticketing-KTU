<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function submit(Request $request)
    {
        // Validasi data
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lampiran' => 'nullable|file|max:2048',
        ]);

        // Simpan data ke database atau proses lainnya
        // Report::create([...]);

        return back()->with('success', 'Laporan berhasil dikirim!');
    }
}
