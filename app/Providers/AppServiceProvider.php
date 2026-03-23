<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        if ($this->app->isLocal()) {
            $thresholdMs = (int) env('DB_SLOW_QUERY_THRESHOLD_MS', 250);

            DB::listen(function (QueryExecuted $query) use ($thresholdMs): void {
                if ($query->time < $thresholdMs) {
                    return;
                }

                Log::warning('Slow query detected', [
                    'time_ms' => $query->time,
                    'connection' => $query->connectionName,
                    'sql' => method_exists($query, 'toRawSql')
                        ? $query->toRawSql()
                        : $query->sql,
                ]);
            });
        }
    }
}
