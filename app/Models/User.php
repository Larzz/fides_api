<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
		'job_title',
		'avatar',
		'status',
		'last_active_at',
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
			'last_active_at' => 'datetime',
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

	public function approvals(): HasMany
	{
		return $this->hasMany(Approval::class);
	}

	public function approvedApprovals(): HasMany
	{
		return $this->hasMany(Approval::class, 'approved_by');
	}

	public function uploadedFiles(): HasMany
	{
		return $this->hasMany(DashboardFile::class, 'uploaded_by');
	}

	public function dashboardNotifications(): HasMany
	{
		return $this->hasMany(DashboardNotification::class);
	}

	public function activities(): HasMany
	{
		return $this->hasMany(Activity::class);
	}

	public function companies(): BelongsToMany
	{
		return $this->belongsToMany(Company::class, 'company_user')
			->withPivot('assigned_at');
	}

	/**
	 * Pending Requests module: submissions created by this user.
	 *
	 * @return HasMany<ServiceRequest, $this>
	 */
	public function serviceRequests(): HasMany
	{
		return $this->hasMany(ServiceRequest::class, 'user_id');
	}

	/**
	 * Pending Requests module: requests this user reviewed as admin.
	 *
	 * @return HasMany<ServiceRequest, $this>
	 */
	public function reviewedServiceRequests(): HasMany
	{
		return $this->hasMany(ServiceRequest::class, 'reviewed_by');
	}

	/**
	 * Access management assignments.
	 *
	 * @return BelongsToMany<Access, User>
	 */
	public function accesses(): BelongsToMany
	{
		return $this->belongsToMany(Access::class, 'access_user')
			->withTimestamps();
	}

	/**
	 * Check if user has role
	 */
	public function hasRole(string $role): bool
	{
		return strtolower((string) $this->role) === strtolower($role);
	}

	/**
	 * Check if user has any of the given roles
	 */
	public function hasAnyRole(array $roles): bool
	{
		$normalizedRoles = array_map('strtolower', $roles);
		return in_array(strtolower((string) $this->role), $normalizedRoles, true);
	}

	/**
	 * Check if user is admin
	 */
	public function isAdmin(): bool
	{
		return $this->hasRole('admin');
	}

	/**
	 * Check if user is staff
	 */
	public function isStaff(): bool
	{
		return $this->hasRole('employee');
	}

	/**
	 * Check if user is client
	 */
	public function isClient(): bool
	{
		return $this->hasRole('client');
	}
}
