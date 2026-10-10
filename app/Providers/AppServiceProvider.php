<?php

namespace App\Providers;

use App\Models\Lembur;
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
        // Badge jumlah lembur menunggu di sidebar HRD, tersedia di semua
        // halaman HRD tanpa tiap controller harus mengirimnya.
        View::composer('partials.sidebar-hrd', function ($view) {
            $view->with('lemburMenunggu', Lembur::where('status', 'menunggu')->count());
        });
    }
}