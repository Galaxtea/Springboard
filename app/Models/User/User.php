<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Traits\Reportable;

use App\Models\Admin\IPHistory;

use Carbon;

class User extends Authenticatable
{
	// Traits
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable, Reportable;


	// Model Settings
		protected $table = 'users';
		public $timestamps = true;
		protected $with = ['settings:user_id,reg_step,display_active', 'powers:id,slug'];

		protected $fillable = [
			'username', 'rank_id', 'pri_curr', 'sec_curr'
		];

		protected $hidden = [
			'remember_token',
		];

		protected function casts(): array {
			return [
				'active_at' => 'datetime',
				'created_at' => 'datetime',
				'updated_at' => 'datetime',
			];
		}


	// Mutators


	// Accessors
		// IMPORTANT
			public function getAuthPasswordAttribute() {
				return $this->settings->password;
			}
			public function perms($key = null) {
				$perms = $this->powers->keyBy('slug');
				if($key) $perms = $perms->has($key);
				return $perms;
			}
			public function getPermListAttribute() {
				return array_keys($this->perms()->toArray());
			}

		// Misc
			public function getDisplayNameAttribute() {
				return '<a href="/user/'.$this->id.'">'.$this->username.'</a>';
			}

		// Blocks
			public function findBlock($user_id) {
				return $this->blockList()->where('blocked_id', '=', $user_id)->first();
			}
			public function isBlocked($user_id) {
				return $this->blockedBy()->where('blocker_id', '=', $user_id)->first();
			}

		// Friends
			public function findFriend($user_id) {
				return $this->friendList()->where('friended_id', '=', $user_id)->first();
			}
			public function isFriended($user_id) {
				return $this->friendedBy()->where('friend_id', '=', $user_id)->first();
			}


	// Relations
		// IMPORTANT
			public function powers() {
				return $this->hasManyThrough(Permissions::class, RankPerms::class, 'rank_id',  'id',  'rank_id',  'permission_id');
			}
			public function rank() {
				return $this->belongsTo(Rank::class);
			}

		// General
			public function settings() {
				return $this->hasOne(UserSettings::class);
			}
			public function stats() {
				return $this->hasOne(UserStats::class);
			}
			public function profile() {
				return $this->hasOne(UserProfile::class);
			}

		// Referrals go both ways
			public function referrer() {
				return $this->hasOne(UserReferrals::class);
			}
			public function referring_user() {
				return $this->hasOneThrough(User::class, UserReferrals::class, 'user_id', 'id', 'id', 'referred_by');
			}
			public function referred_users() {
				return $this->hasManyThrough(User::class, UserReferrals::class, 'referred_by', 'id', 'id', 'user_id');
			}

		// Blocks
			public function blockList() {
				return $this->hasMany(BlockedUsers::class, 'blocker_id', 'id');
			}
			public function blockedBy() {
				return $this->belongsTo(BlockedUsers::class, 'id', 'blocked_id');
			}

		// Friends
			public function friendList() {
				return $this->hasMany(FriendedUsers::class, 'friend_id', 'id');
			}
			public function friendedBy() {
				return $this->belongsTo(FriendedUsers::class, 'id', 'friended_id');
			}
			public function pendingFriends() {
				return $this->belongsTo(FriendedUsers::class, 'id', 'friended_id')->where('status', 'Pending');
			}
			public function friendedUsers() {
				return $this->hasManyThrough(User::class, FriendedUsers::class, 'friend_id', 'id', 'id', 'friended_id');
			}


	// Functions
		public function seen($user = null) {
			// Check if their activity is enabled, or if the viewer has perms to see anyways (i.e. Admin)
			// or if they've even been online at all since signing up
			$visible = $this->settings->display_active;
			if((!$visible && $user?->id != $this->id && !$user?->perms('can_reports')) || ($this->active_at === null)) return 'Offline';

			// Get their active time and check with the current time (Carbon gets the current time automatically)
			$time = $this->active_at;
			$diff = $time->diffInMinutes();

			if($diff <= 5) $seen = 'Online';
			elseif($diff <= 15) $seen = 'Idle';
			elseif($diff >= (60 * 6)) $seen = 'Offline'; // Just a small window where time is displayed - we don't want to tell people an account has been inactive for 3 years and encourage someone to try to hack the account.
			else $seen = $time->diffForHumans(); // They've been on in more than 15min but less than 6h; "30 minutes ago"

			// If we made it here but they're invisible, it's a staff viewing and should know the user is set to invisible.
			return $seen . (!$visible ? ' (invisible)' : '');
		}

		public function touchActive($ip) {
			// Quickly update the user's last seen timestamp :)
			$this->active_at = Carbon\Carbon::now();
			$this->save();
			IPHistory::upsert([
				'user_id' => $this->id,
				'ip_address' => $ip,
				'updated_at' => Carbon\Carbon::now()
			], uniqueBy: ['user_id', 'ip_address'], update: ['updated_at']);
			return;
		}
}
