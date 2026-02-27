<?php

namespace App\Services;

use App\Repositories\ContentRepository;
use App\Models\UserContentUpload;
use App\Models\UserContentDownload;
use App\Models\UserContentShare;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentService
{
	public function __construct(
		protected ContentRepository $contentRepository
	) {
	}

	/**
	 * Get all content
	 */
	public function getAllContent(int $perPage = 15): LengthAwarePaginator
	{
		return $this->contentRepository->getWithRelations(['user', 'category'], $perPage);
	}

	/**
	 * Get content by ID
	 */
	public function getContentById(int $id): UserContentUpload
	{
		return $this->contentRepository->findOrFail($id);
	}

	/**
	 * Upload content
	 */
	public function uploadContent(int $userId, $file, ?int $categoryId = null): UserContentUpload
	{
		return DB::transaction(function () use ($userId, $file, $categoryId) {
			$originalName = $file->getClientOriginalName();
			$extension = $file->getClientExtension();
			$mimeType = $file->getMimeType();
			$size = $file->getSize();
			$hash = hash_file('sha256', $file->getRealPath());

			$path = $file->store('content/uploads', 'public');

			$upload = $this->contentRepository->create([
				'user_id' => $userId,
				'file_name' => $originalName,
				'file_path' => $path,
				'file_type' => $mimeType,
				'file_size' => $size,
				'file_mime_type' => $mimeType,
				'file_extension' => $extension,
				'file_hash' => $hash,
				'file_hash_type' => 'sha256',
				'file_hash_algorithm' => 'sha256',
				'category_id' => $categoryId,
			]);

			return $upload;
		});
	}

	/**
	 * Download content
	 */
	public function downloadContent(int $fileId, int $userId): UserContentDownload
	{
		return DB::transaction(function () use ($fileId, $userId) {
			$upload = $this->contentRepository->findOrFail($fileId);

			$download = UserContentDownload::create([
				'file_id' => $fileId,
				'file_name' => $upload->file_name,
				'file_path' => $upload->file_path,
				'file_type' => $upload->file_type,
				'file_size' => $upload->file_size,
				'file_mime_type' => $upload->file_mime_type,
				'file_extension' => $upload->file_extension,
				'user_id' => $userId,
				'downloaded_at' => now(),
			]);

			return $download;
		});
	}

	/**
	 * Share content
	 */
	public function shareContent(int $fileId, int $userId, int $sharedWithUserId): UserContentShare
	{
		return DB::transaction(function () use ($fileId, $userId, $sharedWithUserId) {
			$upload = $this->contentRepository->findOrFail($fileId);

			$share = UserContentShare::create([
				'user_id' => $userId,
				'file_id' => $fileId,
				'file_name' => $upload->file_name,
				'file_path' => $upload->file_path,
				'file_type' => $upload->file_type,
				'file_size' => $upload->file_size,
				'shared_with_user_id' => $sharedWithUserId,
				'shared_at' => now(),
			]);

			return $share;
		});
	}

	/**
	 * Delete content
	 */
	public function deleteContent(int $id): bool
	{
		return DB::transaction(function () use ($id) {
			$upload = $this->contentRepository->findOrFail($id);

			if (Storage::disk('public')->exists($upload->file_path)) {
				Storage::disk('public')->delete($upload->file_path);
			}

			return $this->contentRepository->delete($id);
		});
	}

	/**
	 * Get content by user
	 */
	public function getContentByUser(int $userId, int $perPage = 15): LengthAwarePaginator
	{
		return $this->contentRepository->getByUser($userId, $perPage);
	}

	/**
	 * Get content by category
	 */
	public function getContentByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
	{
		return $this->contentRepository->getByCategory($categoryId, $perPage);
	}

	/**
	 * Get content by file type
	 */
	public function getContentByFileType(string $fileType, int $perPage = 15): LengthAwarePaginator
	{
		return $this->contentRepository->getByFileType($fileType, $perPage);
	}

	/**
	 * Search content
	 */
	public function searchContent(string $query, int $perPage = 15): LengthAwarePaginator
	{
		return $this->contentRepository->search($query, [], $perPage);
	}
}

