<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'type',
		'user_id',
		'title',
		'description',
		'metadata',
		'status',
		'approved_by',
		'approved_at',
	];

	protected function casts(): array
	{
		return [
			'metadata' => 'array',
			'approved_at' => 'datetime',
		];
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function approver(): BelongsTo
	{
		return $this->belongsTo(User::class, 'approved_by');
	}
}
