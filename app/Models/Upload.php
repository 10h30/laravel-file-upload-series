<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    protected $fillable = [
        'filename',
        'original_filename',
        'thumbnail',
    ];

    public function getUrlAttribute(): string
    {
        // Đảm bảo 's3' là tên disk chính xác được sử dụng để lưu trữ các file này.
        $disk = 'minio';
        if ($this->filename) {
            // Thao tác này tạo ra một URL tạm thời mới mỗi khi thuộc tính 'url' được truy cập.
            // URL sẽ có hiệu lực trong 5 phút kể từ thời điểm nó được tạo.
            return Storage::disk($disk)->temporaryUrl($this->filename, now()->addMinutes(5)); // Fixed typo: $this-> to $this->filename
        }
        return ''; // Hoặc xử lý một cách thích hợp nếu tên tệp là null
    }

    // Tạo URL tạm thời mới mỗi khi thuộc tính 'thumbnail_url' được truy cập
    public function getThumbnailUrlAttribute(): string
    {
        $disk = 'minio'; // Ensure this matches the disk used for storing thumbnails
        if ($this->thumbnail) {
            // Generate temporary URL for the thumbnail path
            return Storage::disk($disk)->temporaryUrl($this->thumbnail, now()->addMinutes(5));
        }
        return ''; // Hoặc xử lý một cách thích hợp nếu tên tệp là null
    }
}
