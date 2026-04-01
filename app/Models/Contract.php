<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Contract extends Model
{
	use HasFactory;

	protected $fillable = [
		'company_id',
		'title',
		'start_date',
		'end_date',
		'status',
	];

	protected function casts(): array
	{
		return [
			'start_date' => 'date',
			'end_date' => 'date',
		];
	}

	public function company(): BelongsTo
	{
		return $this->belongsTo(Company::class);
	}

	public static function computeStatusForDates(?Carbon $endDate): string
	{
		if ($endDate === null) {
			return 'active';
		}

		$today = today();
		if ($endDate->lt($today)) {
			return 'expired';
		}

		if ($endDate->lte($today->copy()->addDays(30))) {
			return 'expiring';
		}

		return 'active';
	}

	public function refreshComputedStatus(): bool
	{
		$computed = self::computeStatusForDates($this->end_date);

		if ($this->status === $computed) {
			return false;
		}

		$this->forceFill(['status' => $computed])->save();

		return true;
	}
}
