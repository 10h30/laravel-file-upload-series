<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{
    protected $fillable = [
        'filename',
        'original_filename'
    ];

    public function getUrlAttribute(): string
    {
        // Đảm bảo 's3' là tên disk chính xác được sử dụng để lưu trữ các file này.
        $disk = 'minio';
        if ($this->filename) {
            // Thao tác này tạo ra một URL tạm thời mới mỗi khi thuộc tính 'url' được truy cập.
            // URL sẽ có hiệu lực trong 5 phút kể từ thời điểm nó được tạo.
            return Storage::disk($disk)->temporaryUrl($this->filename, now()->addMinutes(5));
        }
        return ''; // Hoặc xử lý một cách thích hợp nếu tên tệp là null
    }
}
