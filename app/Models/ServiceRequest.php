<?php

namespace App\Models;

use App\Enums\RequestWorkflowStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Business "request" record (database table: {@see $table requests}).
 */
class ServiceRequest extends Model
{
	use HasFactory;

	protected $table = 'requests';

	protected $fillable = [
		'request_type_id',
		'user_id',
		'details',
		'status',
		'submitted_at',
		'reviewed_by',
		'reviewed_at',
		'notes',
	];

	protected function casts(): array
	{
		return [
			'status' => RequestWorkflowStatus::class,
			'submitted_at' => 'datetime',
			'reviewed_at' => 'datetime',
		];
	}

	/**
	 * @return BelongsTo<RequestType, $this>
	 */
	public function requestType(): BelongsTo
	{
		return $this->belongsTo(RequestType::class);
	}

	/**
	 * @return BelongsTo<User, $this>
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * @return BelongsTo<User, $this>
	 */
	public function reviewer(): BelongsTo
	{
		return $this->belongsTo(User::class, 'reviewed_by');
	}

	/**
	 * @return HasMany<RequestAttachment, $this>
	 */
	public function attachments(): HasMany
	{
		return $this->hasMany(RequestAttachment::class, 'request_id');
	}

	/**
	 * @param  Builder<ServiceRequest>  $query
	 * @return Builder<ServiceRequest>
	 */
	public function scopePending(Builder $query): Builder
	{
		return $query->where('status', RequestWorkflowStatus::Pending);
	}

	/**
	 * @param  Builder<ServiceRequest>  $query
	 * @return Builder<ServiceRequest>
	 */
	public function scopeByStatus(Builder $query, RequestWorkflowStatus|string $status): Builder
	{
		$value = $status instanceof RequestWorkflowStatus
			? $status->value
			: $status;

		return $query->where('status', $value);
	}

	/**
	 * @param  Builder<ServiceRequest>  $query
	 * @return Builder<ServiceRequest>
	 */
	public function scopeForUser(Builder $query, int $userId): Builder
	{
		return $query->where('user_id', $userId);
	}
}
