<?php

namespace App\Services;

use App\Enums\RequestWorkflowStatus;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;

/**
 * Thin domain service for creating and updating pending requests.
 */
class ServiceRequestManager
{
	public function __construct(
		protected FileUploadService $files
	) {
	}

	/**
	 * Create a new employee submission with optional file attachments.
	 *
	 * @param  list<UploadedFile>  $uploads
	 */
	public function createSubmission(
		User $user,
		int $requestTypeId,
		string $details,
		array $uploads
	): ServiceRequest {
		$request = ServiceRequest::create([
			'request_type_id' => $requestTypeId,
			'user_id' => $user->id,
			'details' => $details,
			'status' => RequestWorkflowStatus::Pending,
			'submitted_at' => now(),
		]);

		foreach ($uploads as $file) {
			if ($file instanceof UploadedFile && $file->isValid()) {
				$this->files->storeAttachment($request, $file);
			}
		}

		return $request->fresh([
			'requestType',
			'user',
			'attachments',
		]);
	}

	/**
	 * Update review outcome for an admin workflow step.
	 */
	public function updateStatus(
		ServiceRequest $request,
		User $admin,
		RequestWorkflowStatus $status,
		?string $notes
	): ServiceRequest {
		$request->update([
			'status' => $status,
			'reviewed_by' => $admin->id,
			'reviewed_at' => now(),
			'notes' => $notes,
		]);

		return $request->fresh([
			'requestType',
			'user',
			'attachments',
			'reviewer',
		]);
	}

	/**
	 * Remove attachments from disk then delete the request row.
	 */
	public function deleteRequest(ServiceRequest $request): void
	{
		$this->files->deleteAllForRequest($request);
		$request->delete();
	}
}
