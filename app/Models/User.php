<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasActivityLog;
use App\Traits\HasNotifications;

class User extends Authenticatable
{
	use HasFactory, Notifiable, HasApiTokens, HasActivityLog, HasNotifications;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'role',
		'status',
		'notes',
		'image',
		'resume',
		'cover_letter',
		'phone',
		'address',
		'city',
		'state',
		'zip',
		'country',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var list<string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

	/**
	 * Get the user role
	 */
	public function roleRelation(): BelongsTo
	{
		return $this->belongsTo(UserRole::class, 'role', 'name');
	}

	/**
	 * Get the user status
	 */
	public function statusRelation(): BelongsTo
	{
		return $this->belongsTo(UserStatus::class, 'status', 'name');
	}

	/**
	 * Get user notes
	 */
	public function notes(): HasMany
	{
		return $this->hasMany(UserNote::class);
	}

	/**
	 * Get user images
	 */
	public function images(): HasMany
	{
		return $this->hasMany(UserImage::class);
	}

	/**
	 * Get user leaves
	 */
	public function leaves(): HasMany
	{
		return $this->hasMany(Leave::class);
	}

	/**
	 * Get user tools
	 */
	public function tools(): HasMany
	{
		return $this->hasMany(ToolUser::class);
	}

	/**
	 * Get user content uploads
	 */
	public function contentUploads(): HasMany
	{
		return $this->hasMany(UserContentUpload::class);
	}

	/**
	 * Check if user has role
	 */
	public function hasRole(string $role): bool
	{
		return $this->role === $role;
	}

	/**
	 * Check if user has any of the given roles
	 */
	public function hasAnyRole(array $roles): bool
	{
		return in_array($this->role, $roles);
	}

	/**
	 * Check if user is admin
	 */
	public function isAdmin(): bool
	{
		return $this->hasRole('Admin');
	}

	/**
	 * Check if user is staff
	 */
	public function isStaff(): bool
	{
		return $this->hasRole('Staff');
	}

	/**
	 * Check if user is client
	 */
	public function isClient(): bool
	{
		return $this->hasRole('Client');
	}
}
