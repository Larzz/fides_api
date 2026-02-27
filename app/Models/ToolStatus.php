<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToolStatus extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'description',
		'tool_id',
	];

	/**
	 * Get the tool
	 */
	public function tool(): HasMany
	{
		return $this->hasMany(Tool::class, 'status', 'name');
	}
}

