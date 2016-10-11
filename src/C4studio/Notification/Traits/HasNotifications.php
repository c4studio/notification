<?php

namespace C4studio\Notification\Traits;

use C4studio\Notification\Models\Notification as NotificationModel;

trait HasNotifications {


    /**
     * Get notifications for parent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphToMany
     */
    public function notifications()
    {
        return $this->morphToMany('C4studio\Notification\Models\Notification', 'recipient', 'notifications_pivot');
    }

    /**
     * Get all of the notifications for parent.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function notifications_system()
    {
        return NotificationModel::join('notifications_pivot', 'notifications.id', '=', 'notifications_pivot.notification_id')->where('notifications_pivot.recipient_type', 'system')->get();
    }
    public function getNotificationsSystemAttribute()
    {
        return $this->notifications_system();
    }

}