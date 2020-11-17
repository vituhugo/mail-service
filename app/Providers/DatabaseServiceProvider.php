<?php


namespace App\Providers;


use App\Adapters\DatabaseStorageAdapter;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class DatabaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Storage::extend('dropbox', function ($app, $config) {
            return new Filesystem(new DatabaseStorageAdapter());
        });
    }
}
