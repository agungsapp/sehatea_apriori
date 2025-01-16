<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        if (Session::get('selected_cabang') == null) {
            Session::put('selected_cabang', 1);
        }

        Str::macro('rupiah', function ($value) {
            if (is_null($value) || !is_numeric($value)) {
                return; // Kembalikan string kosong jika nilainya null atau bukan angka
            }
            return 'Rp. ' . number_format((float) $value, 0, '.', '.');
        });
    }
}
