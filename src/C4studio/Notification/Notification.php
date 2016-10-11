<?php

namespace C4studio\Notification;

use C4studio\Notification\Models\Notification as NotificationModel;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class Notification {

    /**
     * Notification::notify(string $message [, User|int $recipient])
     *
     * Adds new notification
     *
     * @param string $message
     * @param array|mixed $recipient
     * @return Notification|bool
     */
    public static function notify($message, $recipients = null)
    {
        $notification = NotificationModel::create([
            'message' => $message
        ]);

        if (is_null($recipients)) {
            DB::table('notifications_pivot')->insert([
                'notification_id' => $notification->id,
                'recipient_id' => null,
                'recipient_type' => 'system'
            ]);
        } elseif (is_array($recipients) || $recipients instanceof Collection) {
            foreach ($recipients as $recipient)
                $recipient->notifications()->attach($notification);
        } else {
            $recipients->notifications()->attach($notification);
        }

        return $notification;
    }

    /**
     * Notification::query()
     *
     * Returns builder object for more complex queries, for ex.:
     * Notification::query()->orderBy('timestamp', 'desc')->take(2)->get();
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query()
    {
        return NotificationModel::query();
    }

}