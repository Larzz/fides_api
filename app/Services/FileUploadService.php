<?php

namespace App\Services;

use App\Models\RequestAttachment;
use App\Models\ServiceRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores request attachments under storage/app/requests/{user_id}/.
 */
class FileUploadService
{
	/**
	 * Filesystem disk used for employee request uploads.
	 */
	public function disk(): string
	{
		return 'local';
	}

	/**
	 * Relative directory for a user's uploads.
	 */
	public function directoryForUser(int $userId): string
	{
		return "requests/{$userId}";
	}

	/**
	 * Persist an uploaded file and create an attachment row.
	 */
	public function storeAttachment(
		ServiceRequest $request,
		UploadedFile $file
	): RequestAttachment {
		$dir = $this->directoryForUser($request->user_id);
		$path = $file->store($dir, $this->disk());

		return $request->attachments()->create([
			'file_name' => $file->getClientOriginalName(),
			'file_path' => $path,
			'file_size' => $file->getSize() ?: 0,
			'mime_type' => $file->getClientMimeType(),
		]);
	}

	/**
	 * Remove file from disk and delete the attachment record.
	 */
	public function deleteAttachment(RequestAttachment $attachment): void
	{
		Storage::disk($this->disk())->delete($attachment->file_path);
		$attachment->delete();
	}

	/**
	 * Delete every attachment for a service request.
	 */
	public function deleteAllForRequest(ServiceRequest $request): void
	{
		$request->loadMissing('attachments');
		foreach ($request->attachments as $attachment) {
			$this->deleteAttachment($attachment);
		}
	}
}
