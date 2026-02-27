<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserContentUpload extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'user_id',
		'file_name',
		'file_path',
		'file_type',
		'file_size',
		'file_mime_type',
		'file_extension',
		'file_hash',
		'file_hash_type',
		'file_hash_algorithm',
		'file_hash_algorithm_name',
		'file_hash_algorithm_description',
		'file_hash_algorithm_version',
		'file_hash_algorithm_version_name',
		'file_hash_algorithm_version_description',
		'file_hash_algorithm_version_version',
		'category_id',
		'notification_id',
	];

	protected function casts(): array
	{
		return [
			'file_size' => 'integer',
		];
	}

	/**
	 * Get the user
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Get the category
	 */
	public function category(): BelongsTo
	{
		return $this->belongsTo(UserContentCategory::class, 'category_id');
	}

	/**
	 * Get content shares
	 */
	public function shares()
	{
		return $this->hasMany(UserContentShare::class, 'file_id');
	}

	/**
	 * Get content downloads
	 */
	public function downloads()
	{
		return $this->hasMany(UserContentDownload::class, 'file_id');
	}
}

