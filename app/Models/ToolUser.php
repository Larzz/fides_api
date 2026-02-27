<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolUser extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'tool_id',
	];

	/**
	 * Get the user
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Get the tool
	 */
	public function tool(): BelongsTo
	{
		return $this->belongsTo(Tool::class);
	}
}

