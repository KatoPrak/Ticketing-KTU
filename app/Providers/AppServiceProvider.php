<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\News; // 📰 tambahkan ini
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL; // 🔒 Wajib tambahkan ini agar URL facade terbaca
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔧 Inject variabel global ke semua view IT & Staff
        View::composer(['it.*', 'staff.*'], function ($view) {
            $view->with([
                'categories' => Category::all(),
                'news' => News::latest()->take(5)->get(), // 📰 ambil 5 berita terbaru
            ]);
        });
    }
}
