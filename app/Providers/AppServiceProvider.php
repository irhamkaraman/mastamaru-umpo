<?php

namespace App\Providers;

use App\Models\AttendanceSubmission;
use App\Observers\AttendanceSubmissionObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        AttendanceSubmission::observe(AttendanceSubmissionObserver::class);

        // Super admin bypass - otomatis izinkan SEMUA aksi dan permission tanpa pengecualian
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
