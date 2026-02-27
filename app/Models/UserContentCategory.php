<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserContentCategory extends Model
{
	use HasFactory;

	protected $fillable = [
		'category_name',
		'category_description',
	];

	/**
	 * Get content uploads in this category
	 */
	public function uploads(): HasMany
	{
		return $this->hasMany(UserContentUpload::class, 'category_id');
	}
}

