<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $table = 'system_logs';

	protected $fillable = [
		'user_id',
		'action',
		'user_type',
		'details',
		'created_at',
	];

	protected function casts(): array
	{
		return [
			'created_at' => 'datetime',
		];
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
