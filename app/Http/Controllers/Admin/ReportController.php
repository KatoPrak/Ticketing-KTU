<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Menampilkan daftar semua laporan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Di sini Anda akan mengambil data laporan dari database
        // dan mengirimkannya ke tampilan.
        $reports = []; // Ganti dengan logika untuk mengambil data laporan
        
        return view('admin.reports', compact('reports'));
    }
}
