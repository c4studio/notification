<?php

if (!function_exists('notify')) {

    /**
     * Helper function for calling add() method on underlying Notification service
     *
     * @param string $message
     * @param \App\Models\User|int $owner
     * @return LogMessage|bool
     */
    function notify($message, $owner = null)
    {
        return \C4studio\Notification\Facades\Notification::notify($message, $owner);
    }

}
