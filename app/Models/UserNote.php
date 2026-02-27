<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNote extends Model
{
	use HasFactory;

	protected $fillable = [
		'note',
		'user_id',
	];

	/**
	 * Get the user that owns the note
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}

