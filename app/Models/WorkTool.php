<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTool extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'category',
		'billing_type',
		'cost',
		'currency',
		'renewal_date',
		'status',
		'notes',
	];

	protected function casts(): array
	{
		return [
			'cost' => 'decimal:2',
			'renewal_date' => 'date',
		];
	}
}
