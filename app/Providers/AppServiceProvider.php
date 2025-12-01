<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Blade;
use App\Helpers\PermissionHelper;

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
        // Register custom route bindings for models with encrypted IDs
        $this->registerEncryptedRouteBindings();
        
        // Register Blade directives for permissions
        $this->registerPermissionDirectives();
    }
    
    /**
     * Register Blade directives for permission checking
     */
    protected function registerPermissionDirectives(): void
    {
        // @can('permission-slug')
        Blade::if('can', function ($permission) {
            return PermissionHelper::can($permission);
        });
        
        // @canAny(['permission1', 'permission2'])
        Blade::if('canAny', function (array $permissions) {
            return PermissionHelper::canAny($permissions);
        });
        
        // @canAll(['permission1', 'permission2'])
        Blade::if('canAll', function (array $permissions) {
            return PermissionHelper::canAll($permissions);
        });
    }

    /**
     * Register route bindings for models that use encrypted route keys
     */
    protected function registerEncryptedRouteBindings(): void
    {
        $models = [
            'bar' => \App\Models\Bar::class,
            'claim' => \App\Models\Claim::class,
            'barReview' => \App\Models\BarReview::class,
            'barTag' => \App\Models\BarTag::class,
            'event' => \App\Models\Event::class,
            'barMenuCategory' => \App\Models\BarMenuCategory::class,
            'barMenuItem' => \App\Models\BarMenuItem::class,
            'barImage' => \App\Models\BarImage::class,
            'booking' => \App\Models\Booking::class,
            'country' => \App\Models\Country::class,
            'state' => \App\Models\State::class,
            'city' => \App\Models\City::class,
            'region' => \App\Models\Region::class,
            'section' => \App\Models\Section::class,
            'user' => \App\Models\User::class,
            'role' => \App\Models\Role::class,
            'permission' => \App\Models\Permission::class,
        ];

        foreach ($models as $key => $modelClass) {
            Route::bind($key, function ($value) use ($modelClass, $key) {
                try {
                    // Laravel automatically URL decodes route parameters, so $value should already be decoded
                    // But sometimes it might be double-encoded, so try both
                    $decryptedId = null;
                    $lastError = null;
                    
                    // First try: decrypt as-is (Laravel should have decoded it)
                    try {
                        $decryptedId = Crypt::decryptString($value);
                    } catch (\Exception $e) {
                        $lastError = $e;
                        // Second try: URL decode first, then decrypt
                        try {
                            $decodedValue = urldecode($value);
                            $decryptedId = Crypt::decryptString($decodedValue);
                        } catch (\Exception $e2) {
                            $lastError = $e2;
                            // Third try: rawurldecode (handles + differently)
                            try {
                                $decodedValue = rawurldecode($value);
                                $decryptedId = Crypt::decryptString($decodedValue);
                            } catch (\Exception $e3) {
                                $lastError = $e3;
                            }
                        }
                    }
                    
                    if ($decryptedId === null) {
                        \Log::error("Route binding failed for {$key}", [
                            'value' => $value,
                            'value_length' => strlen($value),
                            'error' => $lastError ? $lastError->getMessage() : 'Unknown error'
                        ]);
                        abort(404, "Resource not found");
                    }
                    
                    $model = $modelClass::where('id', $decryptedId)->first();
                    
                    if (!$model) {
                        \Log::warning("Model not found for {$key}", [
                            'decrypted_id' => $decryptedId,
                            'model_class' => $modelClass
                        ]);
                        abort(404, "Resource not found");
                    }
                    
                    return $model;
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    abort(404, "Resource not found");
                } catch (\Exception $e) {
                    \Log::error("Route binding exception for {$key}", [
                        'value' => $value,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    abort(404, "Resource not found");
                }
            });
        }
    }
}
