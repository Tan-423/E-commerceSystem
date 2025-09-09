<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle user locked.
     */
    public function updated(User $user)
    {
        // If user was unlocked -> reset failed attempts
        // But don't trigger another save() to avoid double database writes
        if ($user->isDirty('locked') && !$user->locked) {
            // Reset the attributes directly without triggering another save
            $user->failed_attempts = 0;
            $user->last_failed_attempt = null;
            
            // Update the database directly to avoid triggering the observer again
            $user->newQuery()
                ->where('id', $user->id)
                ->update([
                    'failed_attempts' => 0,
                    'last_failed_attempt' => null
                ]);
        }
    }
}
