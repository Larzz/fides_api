<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileShare extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'file_id',
		'share_all_employees',
		'shared_with_user_id',
		'shared_with_company_id',
		'notifications_enabled',
		'created_at',
	];

	protected function casts(): array
	{
		return [
			'share_all_employees' => 'boolean',
			'notifications_enabled' => 'boolean',
			'created_at' => 'datetime',
		];
	}

	public function file(): BelongsTo
	{
		return $this->belongsTo(DashboardFile::class, 'file_id');
	}

	public function sharedWithUser(): BelongsTo
	{
		return $this->belongsTo(User::class, 'shared_with_user_id');
	}

	public function sharedWithCompany(): BelongsTo
	{
		return $this->belongsTo(Company::class, 'shared_with_company_id');
	}
}
