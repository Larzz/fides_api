<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Access extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'company_id',
		'platform',
		'status',
	];

	public function company(): BelongsTo
	{
		return $this->belongsTo(Company::class);
	}

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'access_user')
			->withTimestamps();
	}
}
