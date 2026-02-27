<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContentDownload extends Model
{
	use HasFactory;

	protected $fillable = [
		'file_id',
		'file_name',
		'file_path',
		'file_type',
		'file_size',
		'file_mime_type',
		'file_extension',
		'user_id',
		'downloaded_at',
	];

	protected function casts(): array
	{
		return [
			'file_size' => 'integer',
			'downloaded_at' => 'datetime',
		];
	}

	/**
	 * Get the uploaded file
	 */
	public function upload(): BelongsTo
	{
		return $this->belongsTo(UserContentUpload::class, 'file_id');
	}

	/**
	 * Get the user who downloaded
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}

