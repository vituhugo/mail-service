<?php


namespace App\Providers;


use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $blade = new Blade();
        var_dump($blade);
    }
}
