<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();

        // Auto-reset jatah cuti tahunan pegawai jika terdeteksi pergantian tahun baru (fallback jika cron scheduler tidak aktif)
        $resetFilePath = storage_path('app/last_reset_year.txt');
        $currentYear = date('Y');
        
        if (!file_exists($resetFilePath) || trim(@file_get_contents($resetFilePath)) !== $currentYear) {
            try {
                \Illuminate\Support\Facades\Artisan::call('app:reset-annual-leave');
                @file_put_contents($resetFilePath, $currentYear);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Auto-reset jatah cuti tahunan gagal: ' . $e->getMessage());
            }
        }

        // Registrasi Driver Google Drive Storage
        try {
            \Illuminate\Support\Facades\Storage::extend('google', function ($app, $config) {
                $client = new \Google\Client();
                
                if (!empty($config['serviceAccountJson'])) {
                    $jsonPath = $config['serviceAccountJson'];
                    if (!file_exists($jsonPath) && file_exists(base_path($jsonPath))) {
                        $jsonPath = base_path($jsonPath);
                    }
                    $client->setAuthConfig($jsonPath);
                } else {
                    $client->setClientId($config['clientId'] ?? null);
                    $client->setClientSecret($config['clientSecret'] ?? null);
                    $client->refreshToken($config['refreshToken'] ?? null);
                }
                
                $client->addScope(\Google\Service\Drive::DRIVE);
                
                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/');
                $driver = new \League\Flysystem\Filesystem($adapter);
                
                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter, $config);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menginisialisasi driver Google Drive: ' . $e->getMessage());
        }
    }
}
