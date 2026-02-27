<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveNote extends Model
{
	use HasFactory;

	protected $fillable = [
		'note',
		'leave_id',
	];

	/**
	 * Get the leave that owns the note
	 */
	public function leave(): BelongsTo
	{
		return $this->belongsTo(Leave::class);
	}
}

