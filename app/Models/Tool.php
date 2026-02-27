<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasActivityLog;
use App\Traits\HasNotifications;

class Tool extends Model
{
	use HasFactory, SoftDeletes, HasActivityLog, HasNotifications;

	protected $fillable = [
		'name',
		'description',
		'image',
		'url',
		'category',
		'subcategory',
		'tags',
		'status',
		'notes',
		'user_id',
	];

	/**
	 * Get the user that owns the tool
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Get tool category
	 */
	public function categoryRelation(): BelongsTo
	{
		return $this->belongsTo(ToolCategory::class, 'category', 'name');
	}

	/**
	 * Get tool status
	 */
	public function statusRelation(): BelongsTo
	{
		return $this->belongsTo(ToolStatus::class, 'status', 'name');
	}

	/**
	 * Get tool users (pivot)
	 */
	public function toolUsers(): HasMany
	{
		return $this->hasMany(ToolUser::class);
	}

	/**
	 * Get users assigned to tool
	 */
	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'tool_users', 'tool_id', 'user_id');
	}

	/**
	 * Get tool tags
	 */
	public function toolTags(): HasMany
	{
		return $this->hasMany(ToolTag::class);
	}

	/**
	 * Get tool notes
	 */
	public function toolNotes(): HasMany
	{
		return $this->hasMany(ToolNote::class);
	}

	/**
	 * Get tool costs
	 */
	public function toolCosts(): HasMany
	{
		return $this->hasMany(ToolCost::class);
	}

	/**
	 * Get tool billings
	 */
	public function toolBillings(): HasMany
	{
		return $this->hasMany(ToolBilling::class);
	}
}

