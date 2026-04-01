<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestAttachment extends Model
{
	use HasFactory;

	protected $fillable = [
		'request_id',
		'file_name',
		'file_path',
		'file_size',
		'mime_type',
	];

	protected function casts(): array
	{
		return [
			'file_size' => 'integer',
		];
	}

	/**
	 * @return BelongsTo<ServiceRequest, $this>
	 */
	public function serviceRequest(): BelongsTo
	{
		return $this->belongsTo(ServiceRequest::class, 'request_id');
	}
}
