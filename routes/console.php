<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schedule as ScheduleFacade; // Optional alias if needed

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// -----------------------------------------------------------------
// Add your scheduled tasks below (inside the schedule closure)
// -----------------------------------------------------------------

app()->booted(function () {
    /** @var Schedule $schedule */
    $schedule = app(Schedule::class);

    // Check and upgrade user tiers daily
    $schedule->call(function () {
        app(\App\Services\TierProgressionService::class)->checkAllUsersForUpgrade();
    })->daily();

    // Send membership expiry reminders (30 days before)
    $schedule->call(function () {
        $users = \App\Models\User::membershipExpiringSoon(30)->get();

        foreach ($users as $user) {
            $daysRemaining = $user->membership_expires_at->diffInDays(now());
            app(\App\Services\NotificationService::class)
                ->sendMembershipExpiryReminder($user, $daysRemaining);
        }
    })->daily();

    // Send membership expiry reminders (7 days before)
    $schedule->call(function () {
        $users = \App\Models\User::membershipExpiringSoon(7)->get();

        foreach ($users as $user) {
            $daysRemaining = $user->membership_expires_at->diffInDays(now());
            app(\App\Services\NotificationService::class)
                ->sendMembershipExpiryReminder($user, $daysRemaining);
        }
    })->daily();

    // Mark expired memberships as inactive
    $schedule->call(function () {
        \App\Models\User::membershipExpired()
            ->update(['is_active' => false]);
    })->daily();

    // Process quarterly benefits (runs on 1st of Jan, Apr, Jul, Oct)
    $schedule->command('membership:process-quarterly-benefits')
        ->quarterly()
        ->withoutOverlapping()
        ->runInBackground();

    // Check overdue loan repayments daily
    $schedule->command('loans:check-overdue')
        ->daily()
        ->withoutOverlapping()
        ->runInBackground();
});