<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolNote extends Model
{
	use HasFactory;

	protected $fillable = [
		'note',
		'tool_id',
	];

	/**
	 * Get the tool
	 */
	public function tool(): BelongsTo
	{
		return $this->belongsTo(Tool::class);
	}
}

