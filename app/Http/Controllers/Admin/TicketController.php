<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar semua tiket.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua data tiket dari database, urutkan terbaru
        $tickets = Ticket::with('user')->latest()->get();

        // Kirim ke view
        return view('admin.tickets', compact('tickets'));
    }
    
}
