<?php

namespace C4studio\Notification\Models;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['timestamp'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['message'];

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;



	private static $user_model;



	public function __construct()
	{
		self::$user_model = Config::get('auth.providers.users.model');
	}



	/**
	 * Return true if notification is read for current user.
	 *
	 * @return bool
	 */
	public function getReadAttribute()
	{
		if (!Auth::check())
			throw new AuthenticationException;

		$count = DB::table('notifications_read')->where('notification_id', $this->getKey())->where('user_id', Auth::user()->id)->count();

		return $count ? true : false;
	}

	/**
	 * Return all recipients.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function recipients()
	{
		$result = new Collection();

		$recipients = DB::table('notifications_pivot')->where('notification_id', $this->getKey())->get();

		if (count($recipients) == 1 && $recipients[0]->recipient_type == 'system') {
			$model_name = self::$user_model;

			$result = $model_name::all();
		} else {
			foreach ($recipients as $recipient) {
				$model_name = $recipient->recipient_type;
				$model = $model_name::where('id', $recipient->recipient_id)->first();

				if ($model)
					$result->push($model);
			}
		}

		return $result;
	}
	public function getRecipientsAttribute()
	{
		return $this->recipients();
	}



	/**
	 * Mark notification as read
	 *
	 * @param null $user
	 */
	public function markRead($user = null)
	{
		if (is_null($user) && Auth::check())
			DB::table('notifications_read')->insert([
				'notification_id' => $this->getKey(),
				'user_id' => Auth::user()->id
			]);
		else {
			if (is_a($user, self::$user_model))
				$user_id = $user->id;
			elseif (is_int($user))
				$user_id = $user;
			else
				return false;

			DB::table('notifications_read')->insert([
				'notification_id' => $this->getKey(),
				'user_id' => $user_id
			]);
		}

		return true;
	}

	/**
	 * Mark notification as unread
	 *
	 * @param null $user
	 */
	public function markUnread($user = null)
	{
		if (is_null($user) && Auth::check())
			DB::table('notifications_read')->where('notification_id', $this->getKey())->where('user_id', Auth::user()->id)->delete();
		else {
			if (is_a($user, self::$user_model))
				$user_id = $user->id;
			elseif (is_int($user))
				$user_id = $user;

			DB::table('notifications_read')->where('notification_id', $this->getKey())->where('user_id', $user_id)->delete();
		}

		return true;
	}

}