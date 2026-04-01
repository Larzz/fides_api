<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardNotification extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $table = 'notifications';

	protected $fillable = [
		'user_id',
		'title',
		'message',
		'type',
		'read_at',
		'created_at',
	];

	protected function casts(): array
	{
		return [
			'read_at' => 'datetime',
			'created_at' => 'datetime',
		];
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
