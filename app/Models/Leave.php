<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasActivityLog;
use App\Traits\HasNotifications;

class Leave extends Model
{
	use HasFactory, SoftDeletes, HasActivityLog, HasNotifications;

	protected $fillable = [
		'name',
		'user_id',
		'start_date',
		'end_date',
		'status',
		'type',
		'reason',
		'notes',
		'description',
	];

	protected function casts(): array
	{
		return [
			'start_date' => 'date',
			'end_date' => 'date',
		];
	}

	/**
	 * Get the user that owns the leave
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Get leave status
	 */
	public function statusRelation(): BelongsTo
	{
		return $this->belongsTo(LeaveStatus::class, 'status', 'name');
	}

	/**
	 * Get leave notes
	 */
	public function leaveNotes(): HasMany
	{
		return $this->hasMany(LeaveNote::class);
	}

	/**
	 * Scope for filtering by date range
	 */
	public function scopeDateRange($query, ?string $startDate = null, ?string $endDate = null)
	{
		if ($startDate) {
			$query->where('start_date', '>=', $startDate);
		}
		if ($endDate) {
			$query->where('end_date', '<=', $endDate);
		}
		return $query;
	}

	/**
	 * Scope for filtering by status
	 */
	public function scopeByStatus($query, ?string $status = null)
	{
		if ($status) {
			$query->where('status', $status);
		}
		return $query;
	}

	/**
	 * Scope for filtering by user
	 */
	public function scopeByUser($query, ?int $userId = null)
	{
		if ($userId) {
			$query->where('user_id', $userId);
		}
		return $query;
	}
}

