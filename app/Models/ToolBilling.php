<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolBilling extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'description',
		'tool_id',
		'amount',
		'currency',
		'billing_date',
		'billing_period',
	];

	protected function casts(): array
	{
		return [
			'amount' => 'decimal:2',
			'billing_date' => 'date',
		];
	}

	/**
	 * Get the tool
	 */
	public function tool(): BelongsTo
	{
		return $this->belongsTo(Tool::class);
	}
}

