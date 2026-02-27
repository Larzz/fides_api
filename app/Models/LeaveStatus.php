<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveStatus extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'description',
	];

	/**
	 * Get leaves with this status
	 */
	public function leaves(): HasMany
	{
		return $this->hasMany(Leave::class, 'status', 'name');
	}
}

