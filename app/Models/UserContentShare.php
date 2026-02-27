<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContentShare extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'file_id',
		'file_name',
		'file_path',
		'file_type',
		'file_size',
		'shared_with_user_id',
		'shared_at',
	];

	protected function casts(): array
	{
		return [
			'file_size' => 'integer',
			'shared_at' => 'datetime',
		];
	}

	/**
	 * Get the user who shared
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	/**
	 * Get the user it was shared with
	 */
	public function sharedWith(): BelongsTo
	{
		return $this->belongsTo(User::class, 'shared_with_user_id');
	}

	/**
	 * Get the uploaded file
	 */
	public function upload(): BelongsTo
	{
		return $this->belongsTo(UserContentUpload::class, 'file_id');
	}
}

