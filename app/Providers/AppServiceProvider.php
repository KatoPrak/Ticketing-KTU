<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\News; // 📰 tambahkan ini
use Illuminate\Support\Facades\View;
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
        // Jika aplikasi tidak berada dalam environment 'local' (seperti di server produksi)
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // 🔧 Inject variabel global ke semua view IT & Staff
        View::composer(['it.*', 'staff.*'], function ($view) {
            $view->with([
                'categories' => Category::all(),
                'news' => News::latest()->take(5)->get(), // 📰 ambil 5 berita terbaru
            ]);
        });
    }
}
