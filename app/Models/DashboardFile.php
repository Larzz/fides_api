<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DashboardFile extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'files';

	protected $fillable = [
		'title',
		'category',
		'file_path',
		'uploaded_by',
		'archived_at',
		'notify_stakeholders',
	];

	protected function casts(): array
	{
		return [
			'archived_at' => 'datetime',
			'notify_stakeholders' => 'boolean',
		];
	}

	public function uploader(): BelongsTo
	{
		return $this->belongsTo(User::class, 'uploaded_by');
	}

	public function shares(): HasMany
	{
		return $this->hasMany(FileShare::class, 'file_id');
	}
}
