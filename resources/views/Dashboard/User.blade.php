@extends('layouts.app')

@section('title', 'Dashboard User')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-3">👤 Dashboard User</h3>
            <p>Selamat datang, <strong>{{ Auth::user()->name }}</strong>!</p>
            <p>Anda login sebagai <span class="badge bg-primary">{{ Auth::user()->role }}</span>.</p>

            <hr>

            <h5>Menu Utama</h5>
            <ul>
                <li>Buat tiket masalah baru</li>
                <li>Lihat status tiket</li>
                <li>Profil saya</li>
            </ul>
        </div>
    </div>
@endsection
