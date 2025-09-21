<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar semua tiket.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Di sini Anda akan mengambil data tiket dari database
        // dan mengirimkannya ke tampilan.
        $tickets = []; // Ganti dengan logika untuk mengambil data tiket
        
        return view('admin.tickets', compact('tickets'));
    }
}
