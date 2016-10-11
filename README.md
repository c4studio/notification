# Notification

---

Provides an interface for adding and retrieving database stored notifications.

---

## Installation

Just place require new package for your laravel installation via composer.json

    "c4studio/notification": "1.0.*"

Then simply ```composer update```

### Registering to use it with laravel

Add following lines to ```app/config/app.php```

ServiceProvider array

```php
C4studio\Notification\NotificationServiceProvider::class,
```

Alias array
```php
'Notification' => C4studio\Notification\Facades\Notification::class,
```

### Publishing migrations (Laravel 5.2 and lower only)

	php artisan vendor:publish --provider="C4studio\Notification\NotificationServiceProvider" --tag=migrations

### Running migrations

Notification uses a database table for storage, so you'll need to run the migrations

	php artisan migrate

## Usage

### Select models which can receive notifications

You must apply the HasNotifications trait to all models that will be able to receive notifications

```php
class User extends Model
{
    use HasNotifications;

    ...
}
```


### Add notification using facade

You can add notifications using the notify() method, which take two parameters: the notification message and the recipient(s).
The second parameter accepts either a model or an array/collection of models

```php
Notification::notify('Notification message', Auth::user());
Notification::notify('Notification message', \App\User::all());
```

If no recipient is set, the notification will be treated as a system message, and will be attached to every user model

```php
Notification::notify('Notification message');
```

### Add notification using helper function

```php
notify('Message', Auth::user());
notify('Message');
```

### Retrieving notifications for a model

To get notifications for a model, use the notifications relationship

```php
Auth::user()->notifications;
Auth::user()->notifications()->first();
```

You can also easily get the system notifications

```php
Auth::user()->notifications_system;
Auth::user()->notifications_system()->first();
```

### Marking notification as read/unread

You can mark notification as read/unread by using the markRead() and markUnread() methods.
But accept the user model or user ID as a parameter. If ommitted, message will be marked for current authenticated user

```php
$notification::markRead();
$notification::markRead(\App\User::first());
$notification::markUnread(1);
```

### Check if notification has been read by current user

You can check if a notification has been read by the currently authenticated user, by accessing the read attribute.

```php
echo $notification::read ? 'It is read' : 'It is not read';
```

### Get recipients of a notification

You can also get all the recipients of a notification

```php
$notification::recipients;
$notification::recipients->take(2)->get();
```

### Complex queries

For more complex queries, you can return a Builder object by using query(). Easy, right?

```php
Notification::query()->orderBy('timestamp', 'desc')->take(2)->get();
```