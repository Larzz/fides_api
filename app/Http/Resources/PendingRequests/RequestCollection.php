<?php

namespace App\Http\Resources\PendingRequests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Paginated collection wrapper with a stable {@code data} key.
 *
 * @template TKey of array-key
 * @template TModel
 *
 * @extends ResourceCollection<TKey, TModel>
 */
class RequestCollection extends ResourceCollection
{
	public $collects = RequestResource::class;

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'data' => $this->collection,
		];
	}
}
