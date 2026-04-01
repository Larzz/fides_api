<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'primary_contact_name',
		'primary_contact_email',
		'logo',
		'status',
	];

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'company_user')
			->withPivot('assigned_at');
	}

	public function contracts(): HasMany
	{
		return $this->hasMany(Contract::class);
	}

	public function accesses(): HasMany
	{
		return $this->hasMany(Access::class);
	}
}
