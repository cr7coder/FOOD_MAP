<?php

namespace App\Services;

use App\Models\Eatery;
use App\Models\ReviewVideo;
use App\DTOs\StoreVideoDTO;
use App\Helpers\GoogleDriveHelper;
use Illuminate\Support\Str;

class VideoService
{
    /**
     * Lưu trữ video review mới
     */
    public function storeVideo(StoreVideoDTO $dto, int $userId, string $role): ReviewVideo
    {
        $eatery = Eatery::findOrFail($dto->eateryId);

        // Phân quyền cho Seller
        if ($role === 'seller' && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không thể đăng video review cho cơ sở không thuộc sở hữu của bạn!');
        }

        $videoData = $this->parseVideoSource($dto->videoFile, $dto->videoUrl);
        $status = ($role === 'admin') ? 'approved' : 'pending';

        return ReviewVideo::create([
            'eatery_id' => $eatery->id,
            'user_id' => $userId,
            'title' => $dto->title,
            'video_url' => $videoData['url'],
            'video_type' => $videoData['type'],
            'thumbnail_path' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
            'likes_count' => 0,
            'status' => $status
        ]);
    }

    /**
     * Cập nhật video review
     */
    public function updateVideo(int $id, StoreVideoDTO $dto, int $userId, string $role): ReviewVideo
    {
        $video = ReviewVideo::findOrFail($id);
        $eatery = Eatery::findOrFail($video->eatery_id);

        // Phân quyền cho Seller
        if ($role === 'seller' && $video->user_id !== $userId && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không thể sửa video review không thuộc sở hữu của bạn!');
        }

        $newEatery = Eatery::findOrFail($dto->eateryId);
        if ($role === 'seller' && $newEatery->user_id !== $userId) {
            abort(403, 'Bạn không thể liên kết video với cơ sở không thuộc sở hữu của bạn!');
        }

        $videoUrl = $video->video_url;
        $videoType = $video->video_type;

        // Tải file video mới
        if ($dto->videoFile) {
            $this->deleteLocalVideoFile($video->video_url, $video->video_type);
            $uploadData = $this->parseVideoSource($dto->videoFile, null);
            $videoUrl = $uploadData['url'];
            $videoType = $uploadData['type'];
        } 
        // Thay đổi đường dẫn nhúng mới
        elseif ($dto->videoUrl && $dto->videoUrl !== $video->video_url) {
            $this->deleteLocalVideoFile($video->video_url, $video->video_type);
            $uploadData = $this->parseVideoSource(null, $dto->videoUrl);
            $videoUrl = $uploadData['url'];
            $videoType = $uploadData['type'];
        }

        $status = ($role === 'admin') ? $video->status : 'pending';

        $video->update([
            'eatery_id' => $newEatery->id,
            'title' => $dto->title,
            'video_url' => $videoUrl,
            'video_type' => $videoType,
            'status' => $status
        ]);

        return $video;
    }

    /**
     * Phê duyệt video (chỉ cho Admin)
     */
    public function approveVideo(int $id): ReviewVideo
    {
        $video = ReviewVideo::findOrFail($id);
        $video->update(['status' => 'approved']);
        return $video;
    }

    /**
     * Từ chối video (chỉ cho Admin)
     */
    public function rejectVideo(int $id): ReviewVideo
    {
        $video = ReviewVideo::findOrFail($id);
        $video->update(['status' => 'rejected']);
        return $video;
    }

    /**
     * Xóa video review
     */
    public function destroyVideo(int $id, int $userId, string $role): void
    {
        $video = ReviewVideo::findOrFail($id);
        $eatery = Eatery::findOrFail($video->eatery_id);

        if ($role === 'seller' && $video->user_id !== $userId && $eatery->user_id !== $userId) {
            abort(403, 'Bạn không có quyền xóa video review này!');
        }

        $this->deleteLocalVideoFile($video->video_url, $video->video_type);
        $video->delete();
    }

    /**
     * Phân loại nguồn video và tải lên nếu cần
     */
    private function parseVideoSource($file, ?string $url): array
    {
        if ($file) {
            $videoUrl = GoogleDriveHelper::upload($file, 'videos');
            return ['url' => $videoUrl, 'type' => 'local'];
        }

        if ($url) {
            // Regex bóc tách Google Drive link
            if (preg_match('/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?id=))([a-zA-Z0-9_-]{25,50})/i', $url, $matches)) {
                return [
                    'url' => 'https://drive.google.com/uc?export=download&id=' . $matches[1],
                    'type' => 'local'
                ];
            }
            // Regex bóc tách TikTok ID
            elseif (preg_match('/tiktok\.com\/(@[^\/]+\/video\/(\d+)|v\/(\d+))/i', $url, $matches) || preg_match('/vt\.tiktok\.com\/(\w+)/i', $url)) {
                return ['url' => $url, 'type' => 'tiktok'];
            } 
            // Regex bóc tách YouTube ID
            elseif (preg_match('/(?:youtube\.com\/(?:shorts\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                return ['url' => $url, 'type' => 'youtube_shorts'];
            } else {
                return ['url' => $url, 'type' => 'local'];
            }
        }

        throw new \InvalidArgumentException('Phải cung cấp tệp video hoặc liên kết nhúng!');
    }

    /**
     * Xóa file video cục bộ nếu có lưu trữ
     */
    private function deleteLocalVideoFile(string $url, string $type): void
    {
        if ($type === 'local' && Str::startsWith($url, '/uploads/videos/')) {
            $filePath = public_path($url);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
}
